<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuestController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Guest Management', route('guests.index'));
        Breadcrumbs::add('Guests');
        
        $properties = Property::all();

        return view('guests.index', compact('properties'));
    }

    public function ajaxGuests(Request $request)
    {
        $query = Guest::with(['property'])
            ->select(['guests.*'])
            ->orderBy('guests.created_at', 'desc');

        // Apply property filter if set
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
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
                    } elseif ($colName === 'guest_type') {
                        $query->where('guest_type', $searchValue);
                    } elseif ($colName === 'is_blacklisted') {
                        $query->where('is_blacklisted', $searchValue === 'yes' ? 1 : 0);
                    } elseif ($colName === 'created_at') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('guests.created_at', [$start, $end]);
                        }
                    } else {
                        // Search in multiple fields
                        if (in_array($colName, ['first_name', 'email', 'phone'])) {
                            $query->where(function ($q) use ($searchValue) {
                                $q->where('first_name', 'like', "%{$searchValue}%")
                                  ->orWhere('last_name', 'like', "%{$searchValue}%")
                                  ->orWhere('email', 'like', "%{$searchValue}%")
                                  ->orWhere('phone', 'like', "%{$searchValue}%");
                            });
                        }
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('full_name_display', function ($row) {
                $html = '<div class="d-flex align-items-center">';
                
                // Photo
                if ($row->photo_path) {
                    $html .= '<img src="' . Storage::url($row->photo_path) . '" class="rounded-circle me-2" width="32" height="32" alt="Photo">';
                } else {
                    $initials = strtoupper(substr($row->first_name, 0, 1) . substr($row->last_name, 0, 1));
                    $html .= '<div class="avatar-circle me-2">' . $initials . '</div>';
                }
                
                $html .= '<div>';
                $html .= '<div class="fw-bold">' . $row->full_name . '</div>';
                
                // Badges
                if ($row->is_vip) {
                    $html .= '<span class="badge bg-warning text-dark me-1">VIP</span>';
                }
                if ($row->is_blacklisted) {
                    $html .= '<span class="badge bg-danger">Blacklisted</span>';
                }
                
                $html .= '</div></div>';
                return $html;
            })
            ->addColumn('contact_display', function ($row) {
                $html = '';
                if ($row->email) {
                    $html .= '<div><i class="bx bx-envelope"></i> ' . $row->email . '</div>';
                }
                if ($row->phone) {
                    $html .= '<div><i class="bx bx-phone"></i> ' . $row->phone . '</div>';
                }
                return $html ?: '-';
            })
            ->addColumn('guest_type_badge', function ($row) {
                $colors = [
                    'individual' => 'primary',
                    'corporate' => 'success',
                    'group' => 'info',
                    'vip' => 'warning'
                ];
                $color = $colors[$row->guest_type] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->guest_type) . '</span>';
            })
            ->addColumn('bookings_count', function ($row) {
                $count = $row->getTotalBookings();
                $spent = number_format($row->getTotalSpent(), 2);
                return '<div class="text-center"><strong>' . $count . '</strong> bookings<br><small>₹' . $spent . ' spent</small></div>';
            })
            ->addColumn('status_display', function ($row) {
                if ($row->is_blacklisted) {
                    return '<span class="badge bg-danger">Blacklisted</span>';
                }
                return '<span class="badge bg-success">Active</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-guest" href="#" data-id="'.$row->id.'"><i class="bx bx-show"></i> View</a></li>
                            <li><a class="dropdown-item edit-guest" href="#" data-id="'.$row->id.'"><i class="bx bx-edit"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>';
                
                if ($row->is_blacklisted) {
                    $actions .= '<li><a class="dropdown-item whitelist-guest" href="#" data-id="'.$row->id.'"><i class="bx bx-check"></i> Whitelist</a></li>';
                } else {
                    $actions .= '<li><a class="dropdown-item blacklist-guest" href="#" data-id="'.$row->id.'"><i class="bx bx-block"></i> Blacklist</a></li>';
                }
                
                $actions .= '
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="javascript:void(0);" class="dropdown-item text-danger delete-guest" data-id="'.$row->id.'"><i class="bx bx-trash"></i> Delete</a></li>
                        </ul>
                    </div>
                ';
                
                return $actions;
            })
            ->rawColumns(['full_name_display', 'contact_display', 'guest_type_badge', 'bookings_count', 'status_display', 'action'])
            ->make(true);
    }

    // Store new guest
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'title' => 'required|in:Mr,Mrs,Ms,Dr,Prof',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:200',
            'company_designation' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:100',
            'id_expiry_date' => 'nullable|date|after:today',
            'preferred_language' => 'nullable|in:en,hi,es,fr,de',
            'meal_preference' => 'nullable|in:veg,non-veg,vegan,jain',
            'special_requests' => 'nullable|string',
            'allergies' => 'nullable|string',
            'guest_type' => 'required|in:individual,corporate,group,vip',
            'is_vip' => 'boolean',
            'marketing_consent' => 'boolean',
            'sms_consent' => 'boolean',
            'email_consent' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['photo', 'id_document']);
            $data['created_by_user_id'] = auth()->id();
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo_path'] = $request->file('photo')->store('guests/photos', 'public');
            }
            
            // Handle ID document upload
            if ($request->hasFile('id_document')) {
                $data['id_document_path'] = $request->file('id_document')->store('guests/documents', 'public');
            }

            $guest = Guest::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Guest created successfully',
                'data' => $guest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create guest: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single guest
    public function show($id)
    {
        $guest = Guest::with(['property', 'bookings'])->findOrFail($id);
        
        // Add computed fields
        $guest->total_bookings = $guest->getTotalBookings();
        $guest->total_spent = $guest->getTotalSpent();
        $guest->last_booking_date = $guest->getLastBookingDate();
        
        return response()->json($guest);
    }

    // Update guest
    public function update(Request $request, $id)
    {
        $guest = Guest::findOrFail($id);

        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'title' => 'required|in:Mr,Mrs,Ms,Dr,Prof',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:200',
            'company_designation' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:100',
            'id_expiry_date' => 'nullable|date|after:today',
            'preferred_language' => 'nullable|in:en,hi,es,fr,de',
            'meal_preference' => 'nullable|in:veg,non-veg,vegan,jain',
            'special_requests' => 'nullable|string',
            'allergies' => 'nullable|string',
            'guest_type' => 'required|in:individual,corporate,group,vip',
            'is_vip' => 'boolean',
            'marketing_consent' => 'boolean',
            'sms_consent' => 'boolean',
            'email_consent' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120'
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['photo', 'id_document']);
            $data['updated_by_user_id'] = auth()->id();
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($guest->photo_path) {
                    Storage::disk('public')->delete($guest->photo_path);
                }
                $data['photo_path'] = $request->file('photo')->store('guests/photos', 'public');
            }
            
            // Handle ID document upload
            if ($request->hasFile('id_document')) {
                // Delete old document
                if ($guest->id_document_path) {
                    Storage::disk('public')->delete($guest->id_document_path);
                }
                $data['id_document_path'] = $request->file('id_document')->store('guests/documents', 'public');
            }

            $guest->update($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Guest updated successfully',
                'data' => $guest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update guest: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete guest
    public function destroy($id)
    {
        $guest = Guest::find($id);

        if (!$guest) {
            return response()->json([
                'status' => false,
                'message' => 'Guest not found'
            ], 404);
        }

        // Safety check: guest has bookings
        $bookingsCount = $guest->bookings()->count();
        if ($bookingsCount > 0) {
            return response()->json([
                'status' => false,
                'message' => "Cannot delete guest with {$bookingsCount} booking(s). Please archive instead."
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete uploaded files
            if ($guest->photo_path) {
                Storage::disk('public')->delete($guest->photo_path);
            }
            if ($guest->id_document_path) {
                Storage::disk('public')->delete($guest->id_document_path);
            }
            
            $guest->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Guest deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete guest: ' . $e->getMessage()
            ], 500);
        }
    }

    // Blacklist guest
    public function blacklist(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $guest = Guest::findOrFail($id);
        $guest->blacklist($request->reason);

        return response()->json([
            'status' => true,
            'message' => 'Guest has been blacklisted'
        ]);
    }

    // Whitelist guest
    public function whitelist($id)
    {
        $guest = Guest::findOrFail($id);
        $guest->whitelist();

        return response()->json([
            'status' => true,
            'message' => 'Guest has been whitelisted'
        ]);
    }

    // Search guests (for autocomplete)
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $guests = Guest::where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->active()
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);

        return response()->json($guests);
    }
}