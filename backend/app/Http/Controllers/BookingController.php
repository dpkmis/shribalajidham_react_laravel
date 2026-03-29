<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\BookingCharge;
use App\Models\BookingPayment;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HousekeepingTask;

class BookingController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Booking Management', route('bookings.index'));
        Breadcrumbs::add('Bookings');
        
        $properties = Property::all();
        $roomTypes = RoomType::active()->ordered()->get();

        return view('bookings.index', compact('properties', 'roomTypes'));
    }

    public function ajaxBookings(Request $request)
    {
        $query = Booking::with(['property', 'guest', 'bookingRooms.roomType'])
            ->select(['bookings.*'])
            ->orderBy('bookings.created_at', 'desc');

        // Apply property filter if set
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Apply status filter if set
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        // Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];

                if (!empty($searchValue)) {
                    if ($colName === 'property.name') {
                        $query->whereHas('property', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'guest_display') {
                        $query->whereHas('guest', function ($q) use ($searchValue) {
                            $q->where('first_name', 'like', "%{$searchValue}%")
                              ->orWhere('last_name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'status_badge') {
                        $query->where('status', $searchValue);
                    } elseif ($colName === 'payment_badge') {
                        $query->where('payment_status', $searchValue);
                    } elseif ($colName === 'booking_ref_display') {
                        $query->where('booking_reference', 'like', "%{$searchValue}%");
                    } elseif ($colName === 'dates_display') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d', strtotime($dates[0]));
                            $end = date('Y-m-d', strtotime($dates[1]));
                            $query->whereBetween('checkin_date', [$start, $end]);
                        }
                    } else {
                        $query->where("bookings.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('booking_ref_display', function ($row) {
                $html = '<div class="fw-bold">' . $row->booking_reference . '</div>';
                $html .= '<small class="text-muted">' . $row->source . '</small>';
                return $html;
            })
            ->addColumn('guest_display', function ($row) {
                if ($row->guest) {
                    $html = '<div class="fw-bold">' . $row->guest->full_name . '</div>';
                    $html .= '<small><i class="bx bx-phone"></i> ' . $row->guest->phone . '</small>';
                    return $html;
                }
                return '<span class="text-muted">Walk-in</span>';
            })
            ->addColumn('dates_display', function ($row) {
                $checkin = $row->checkin_date->format('d M Y');
                $checkout = $row->checkout_date->format('d M Y');
                return '<div><strong>In:</strong> ' . $checkin . '</div><div><strong>Out:</strong> ' . $checkout . '</div><small>' . $row->nights . ' night(s)</small>';
            })
            ->addColumn('rooms_display', function ($row) {
                $rooms = $row->bookingRooms->map(function ($br) {
                    return $br->room ? $br->room->room_number : $br->roomType->name;
                })->take(3)->implode(', ');
                
                $count = $row->bookingRooms->count();
                return $rooms . ($count > 3 ? ' +' . ($count - 3) : '');
            })
            ->addColumn('guests_display', function ($row) {
                return '<div class="text-center"><i class="bx bx-user"></i> ' . $row->number_of_adults . 'A';
                if ($row->number_of_children > 0) {
                    return ' + ' . $row->number_of_children . 'C';
                }
                return '</div>';
            })
            ->addColumn('amount_display', function ($row) {
                return '<div class="text-end"><div class="fw-bold">₹' . number_format($row->total_amount, 2) . '</div><small class="text-success">Paid: ₹' . number_format($row->paid_amount, 2) . '</small></div>';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'pending' => 'warning',
                    'confirmed' => 'info',
                    'checked-in' => 'success',
                    'checked-out' => 'secondary',
                    'cancelled' => 'danger',
                    'no-show' => 'dark'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
            })
            ->addColumn('payment_badge', function ($row) {
                $colors = [
                    'unpaid' => 'danger',
                    'partially-paid' => 'warning',
                    'paid' => 'success',
                    'refunded' => 'info',
                    'cancelled' => 'secondary'
                ];
                $color = $colors[$row->payment_status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst(str_replace('-', ' ', $row->payment_status)).'</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-booking" href="#" data-id="'.$row->id.'"><i class="bx bx-show"></i> View</a></li>
                            <li><a class="dropdown-item edit-booking" href="#" data-id="'.$row->id.'"><i class="bx bx-edit"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>';
                
                if ($row->status === 'confirmed') {
                    $actions .= '<li><a class="dropdown-item checkin-booking" href="#" data-id="'.$row->id.'"><i class="bx bx-log-in"></i> Check In</a></li>';
                }
                
                if ($row->status === 'checked-in') {
                    $actions .= '<li><a class="dropdown-item checkout-booking" href="#" data-id="'.$row->id.'"><i class="bx bx-log-out"></i> Check Out</a></li>';
                }
                
                if (in_array($row->status, ['pending', 'confirmed'])) {
                    $actions .= '<li><a class="dropdown-item cancel-booking" href="#" data-id="'.$row->id.'"><i class="bx bx-x"></i> Cancel</a></li>';
                }
                
                $actions .= '
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item manage-charges" href="#" data-id="'.$row->id.'"><i class="bx bx-money"></i> Manage Charges</a></li>
                            <li><a class="dropdown-item add-payment" href="#" data-id="'.$row->id.'"><i class="bx bx-wallet"></i> Add Payment</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="' . route('bookings.invoice', $row->id) . '" target="_blank"><i class="bx bx-receipt"></i> Invoice</a></li>
                        </ul>
                    </div>
                ';
                
                return $actions;
            })
            ->rawColumns(['booking_ref_display', 'guest_display', 'dates_display', 'rooms_display', 'guests_display', 'amount_display', 'status_badge', 'payment_badge', 'action'])
            ->make(true);
    }

    // Store new booking
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'guest_id' => 'nullable|exists:guests,id',
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'source' => 'nullable|in:walk-in,phone,email,website,booking.com,airbnb,agoda,makemytrip,goibibo,corporate,travel-agent',
            'number_of_adults' => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'number_of_infants' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:500',
            'arrival_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            
            // Guest info if not linked
            'guest_first_name' => 'required_without:guest_id|string|max:100',
            'guest_last_name' => 'required_without:guest_id|string|max:100',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'required_without:guest_id|string|max:20',
            
            // Room assignments
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type_id' => 'required|exists:room_types,id',
            'rooms.*.room_id' => 'nullable|exists:rooms,id',
            'rooms.*.rate_per_night' => 'required|numeric|min:0',
            'rooms.*.adults' => 'required|integer|min:1',
            'rooms.*.children' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create or get guest
            if ($request->guest_id) {
                $guestId = $request->guest_id;
            } else {
                // Create walk-in guest
                $guest = Guest::create([
                    'property_id' => $request->property_id,
                    'title' => $request->guest_title ?? 'Mr',
                    'first_name' => $request->guest_first_name,
                    'last_name' => $request->guest_last_name,
                    'email' => $request->guest_email,
                    'phone' => $request->guest_phone,
                    'guest_type' => 'individual',
                    'created_by_user_id' => auth()->id()
                ]);
                $guestId = $guest->id;
            }

            // Create booking
            $booking = Booking::create([
                'property_id' => $request->property_id,
                'guest_id' => $guestId,
                'status' => 'confirmed',
                'source' => $request->source ?? 'walk-in',
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'number_of_adults' => $request->number_of_adults,
                'number_of_children' => $request->number_of_children ?? 0,
                'number_of_infants' => $request->number_of_infants ?? 0,
                'currency' => 'INR',
                'special_requests' => $request->special_requests,
                'arrival_time' => $request->arrival_time,
                'notes' => $request->notes,
                'created_by_user_id' => auth()->id()
            ]);

            // Create room assignments
            foreach ($request->rooms as $roomData) {
                $nights = Carbon::parse($request->checkin_date)->diffInDays(Carbon::parse($request->checkout_date));
                $ratePerNight = $roomData['rate_per_night'] * 100; // Convert to cents
                $totalRate = $ratePerNight * $nights;

                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $roomData['room_id'] ?? null,
                    'room_type_id' => $roomData['room_type_id'],
                    'checkin_date' => $request->checkin_date,
                    'checkout_date' => $request->checkout_date,
                    'rate_per_night_cents' => $ratePerNight,
                    'total_rate_cents' => $totalRate,
                    'final_rate_cents' => $totalRate,
                    'status' => 'confirmed',
                    'adults' => $roomData['adults'],
                    'children' => $roomData['children'] ?? 0
                ]);

                // Mark room as reserved if assigned
                if (!empty($roomData['room_id'])) {
                    Room::where('id', $roomData['room_id'])->update(['status' => 'reserved']);
                }
            }

            // Calculate totals
            $booking->calculateTotals();

            // Log activity
            $booking->logActivity('created', 'Booking created');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking created successfully',
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single booking
    public function show($id)
    {
        $booking = Booking::with([
            'property',
            'guest',
            'bookingRooms.room',
            'bookingRooms.roomType',
            'bookingGuests',
            'charges',
            'payments' => function($q) {
                $q->orderBy('paid_at', 'desc');
            },
            'activities' => function($q) {
                $q->orderBy('created_at', 'desc')->limit(10);
            }
        ])->findOrFail($id);
        
        return response()->json($booking);
    }

    // Update booking
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Cannot edit checked-out or cancelled bookings
        if (in_array($booking->status, ['checked-out', 'cancelled'])) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot edit a booking that is ' . $booking->status
            ], 422);
        }

        $request->validate([
            'guest_id' => 'nullable|exists:guests,id',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
            'number_of_adults' => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'number_of_infants' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:500',
            'arrival_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $booking->update([
                'guest_id' => $request->guest_id,
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'number_of_adults' => $request->number_of_adults,
                'number_of_children' => $request->number_of_children ?? 0,
                'number_of_infants' => $request->number_of_infants ?? 0,
                'special_requests' => $request->special_requests,
                'arrival_time' => $request->arrival_time,
                'notes' => $request->notes,
                'updated_by_user_id' => auth()->id()
            ]);

            $booking->logActivity('updated', 'Booking details updated');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete booking
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Safety check: cannot delete checked-in bookings
        if ($booking->status === 'checked-in') {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete a checked-in booking. Please check out first.'
            ], 422);
        }

        // Safety check: has payments
        if ($booking->paid_amount_cents > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete booking with payments. Please cancel instead.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Free up rooms
            foreach ($booking->bookingRooms as $bookingRoom) {
                if ($bookingRoom->room) {
                    $bookingRoom->room->update(['status' => 'available']);
                }
            }
            
            $booking->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check In
    public function checkIn($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'status' => false,
                'message' => 'Only confirmed bookings can be checked in'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking->checkIn();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Guest checked in successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to check in: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check Out
    public function checkOut(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking_rooms = BookingRoom::where('booking_id', $id)->get();
        
            

        if ($booking->status !== 'checked-in') {
            return response()->json([
                'status' => false,
                'message' => 'Only checked-in bookings can be checked out'
            ], 422);
        }

        // Check if balance is paid
        if ($booking->balance_amount_cents > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot check out with pending balance of ₹' . number_format($booking->balance_amount, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking->checkOut();
            foreach ($booking_rooms as $booking_room) {
                $room = Room::where('id', $booking_room->room_id)->first();
                $room->update([
                    'status' => 'available',
                    'housekeeping_status' => 'dirty'
            ]);
            
        }
            // Automatically create housekeeping task
            HousekeepingTask::create([
                'property_id' => $room->property_id,
                'room_id' => $room->id,
                'task_type' => 'checkout-cleaning',
                'priority' => 'high',
                'scheduled_date' => now(),
                'status' => 'pending',
                'is_occupied' => false,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Guest checked out successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to check out: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel Booking
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $booking = Booking::findOrFail($id);

        if (in_array($booking->status, ['checked-out', 'cancelled'])) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot cancel a booking that is ' . $booking->status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking->cancel($request->reason);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mark as No Show
    public function markNoShow($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'status' => false,
                'message' => 'Only confirmed bookings can be marked as no-show'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'no-show']);
            
            // Free up rooms
            $booking->bookingRooms()->update(['status' => 'no-show']);
            foreach ($booking->bookingRooms as $br) {
                $br->room?->update(['status' => 'available']);
            }
            
            $booking->logActivity('no-show', 'Marked as no-show');
            
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking marked as no-show'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to mark as no-show: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add Charge
    public function addCharge(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:service-charge,food-beverage,laundry,minibar,spa,transportation,extra-bed,early-checkin,late-checkout,pet-charge,parking,damage,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $booking = Booking::findOrFail($id);

        DB::beginTransaction();
        try {
            $charge = BookingCharge::create([
                'booking_id' => $booking->id,
                'type' => $request->type,
                'description' => $request->description,
                'amount_cents' => $request->amount * 100,
                'quantity' => $request->quantity ?? 1,
                'charge_date' => now(),
                'created_by_user_id' => auth()->id()
            ]);

            $booking->calculateTotals();
            $booking->logActivity('charge-added', 'Charge added: ' . $request->description);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Charge added successfully',
                'data' => $charge
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add charge: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add Payment
    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,upi,net-banking,cheque,wallet,bank-transfer,other',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500'
        ]);

        $booking = Booking::findOrFail($id);

        // Check if amount exceeds balance
        $amountCents = $request->amount * 100;
        if ($amountCents > $booking->balance_amount_cents) {
            return response()->json([
                'status' => false,
                'message' => 'Payment amount cannot exceed balance amount of ₹' . number_format($booking->balance_amount, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment = BookingPayment::create([
                'booking_id' => $booking->id,
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'amount_cents' => $amountCents,
                'method' => $request->method,
                'type' => 'payment',
                'transaction_id' => $request->transaction_id,
                'status' => 'completed',
                'paid_at' => now(),
                'remarks' => $request->remarks,
                'received_by_user_id' => auth()->id()
            ]);

            $booking->calculateTotals();
            $booking->logActivity('payment-added', 'Payment received: ₹' . number_format($request->amount, 2));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment added successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check Availability
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_type_id' => 'required|exists:room_types,id',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date'
        ]);

        // Get all rooms of this type
        $allRooms = Room::where('property_id', $request->property_id)
            ->where('room_type_id', $request->room_type_id)
            ->where('is_active', true)
            ->get();

        // Get booked rooms in this date range
        $bookedRoomIds = BookingRoom::whereHas('booking', function ($q) {
            $q->whereNotIn('status', ['cancelled', 'no-show']);
        })
        ->where(function ($q) use ($request) {
            $q->whereBetween('checkin_date', [$request->checkin_date, $request->checkout_date])
              ->orWhereBetween('checkout_date', [$request->checkin_date, $request->checkout_date])
              ->orWhere(function ($q2) use ($request) {
                  $q2->where('checkin_date', '<=', $request->checkin_date)
                     ->where('checkout_date', '>=', $request->checkout_date);
              });
        })
        ->pluck('room_id')
        ->toArray();

        // Available rooms
        $availableRooms = $allRooms->whereNotIn('id', $bookedRoomIds);

        return response()->json([
            'status' => true,
            'total_rooms' => $allRooms->count(),
            'available_rooms' => $availableRooms->count(),
            'rooms' => $availableRooms->values()
        ]);
    }

    // Generate Invoice
    public function generateInvoice($id)
    {
        
        $booking = Booking::with([
            'property',
            'guest',
            'bookingRooms.roomType',
            'charges',
            'payments'
        ])->findOrFail($id);

        return Pdf::loadView('invoices.pdf', compact('booking'))
            ->download('Invoice-'.$booking->booking_reference.'.pdf');
    }

    // Calendar View
    public function calendarView()
    {
        $properties = Property::all();
        return view('bookings.calendar', compact('properties'));
    }

    // Calendar Data
    public function calendarData(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        $bookings = Booking::with(['guest', 'bookingRooms.room'])
            ->where('property_id', $request->property_id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('checkin_date', [$request->start, $request->end])
                  ->orWhereBetween('checkout_date', [$request->start, $request->end])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('checkin_date', '<=', $request->start)
                         ->where('checkout_date', '>=', $request->end);
                  });
            })
            ->get();

        $events = [];
        foreach ($bookings as $booking) {
            $colors = [
                'pending' => '#ffc107',
                'confirmed' => '#17a2b8',
                'checked-in' => '#28a745',
                'checked-out' => '#6c757d',
                'cancelled' => '#dc3545',
                'no-show' => '#343a40'
            ];

            $events[] = [
                'id' => $booking->id,
                'title' => $booking->guest ? $booking->guest->full_name : 'Walk-in',
                'start' => $booking->checkin_date->format('Y-m-d'),
                'end' => $booking->checkout_date->format('Y-m-d'),
                'backgroundColor' => $colors[$booking->status] ?? '#6c757d',
                'borderColor' => $colors[$booking->status] ?? '#6c757d',
                'extendedProps' => [
                    'reference' => $booking->booking_reference,
                    'status' => $booking->status,
                    'rooms' => $booking->bookingRooms->count(),
                    'guests' => $booking->number_of_adults + $booking->number_of_children
                ]
            ];
        }

        return response()->json($events);
    }

    // Get Booking Statistics
    public function getStats(Request $request)
    {
        $propertyId = $request->get('property_id');
        
        $query = Booking::query();
        
        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        // Total bookings (active)
        $totalBookings = (clone $query)
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->count();

        // Checked in today
        $checkedInCount = (clone $query)
            ->where('status', 'checked-in')
            ->count();

        // Upcoming (confirmed bookings with future check-in)
        $upcomingCount = (clone $query)
            ->where('status', 'confirmed')
            ->where('checkin_date', '>=', today())
            ->count();

        // Pending payment amount
        $pendingPayment = (clone $query)
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->where('payment_status', '!=', 'paid')
            ->sum('balance_amount_cents');

        // Arrivals today
        $arrivalsToday = (clone $query)
            ->where('checkin_date', today())
            ->whereIn('status', ['confirmed', 'checked-in'])
            ->count();

        // Departures today
        $departuresToday = (clone $query)
            ->where('checkout_date', today())
            ->whereIn('status', ['checked-in', 'checked-out'])
            ->count();

        return response()->json([
            'status' => true,
            'stats' => [
                'total_bookings' => $totalBookings,
                'checked_in' => $checkedInCount,
                'upcoming' => $upcomingCount,
                'pending_payment' => $pendingPayment / 100, // Convert to rupees
                'arrivals_today' => $arrivalsToday,
                'departures_today' => $departuresToday
            ]
        ]);
    }
}