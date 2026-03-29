<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Room Management', route('rooms.index'));
        Breadcrumbs::add('Rooms');
        
        $properties = Property::all();
        $roomTypes = RoomType::active()->ordered()->get();
        $statusOptions = Room::getStatusOptions();
        $housekeepingOptions = Room::getHousekeepingStatusOptions();

        return view('rooms.index', compact('properties', 'roomTypes', 'statusOptions', 'housekeepingOptions'));
    }

    public function ajaxRooms(Request $request)
    {
        $query = Room::with(['property', 'roomType'])
            ->select(['rooms.*'])
            ->orderBy('rooms.floor')
            ->orderBy('rooms.room_number');

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
                    } elseif ($colName === 'roomType.name') {
                        $query->whereHas('roomType', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'created_at') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('rooms.created_at', [$start, $end]);
                        }
                    } else {
                        $query->where("rooms.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('floor_display', function ($row) {
                return $row->floor ? 'Floor ' . $row->floor : '-';
            })
            ->addColumn('rate_display', function ($row) {
                $rate = $row->price_override_cents 
                    ? $row->price_override_cents / 100 
                    : ($row->roomType->default_rate_cents / 100);
                return '₹' . number_format($rate, 2);
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'available' => 'success',
                    'occupied' => 'danger',
                    'reserved' => 'warning',
                    'maintenance' => 'info',
                    'out-of-order' => 'dark',
                    'blocked' => 'secondary'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
            })
            ->addColumn('housekeeping_badge', function ($row) {
                $colors = [
                    'clean' => 'success',
                    'dirty' => 'danger',
                    'inspected' => 'primary',
                    'out-of-service' => 'dark',
                    'pickup' => 'warning'
                ];
                $color = $colors[$row->housekeeping_status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst($row->housekeeping_status).'</span>';
            })
            ->addColumn('features_display', function ($row) {
                $features = [];
                if ($row->is_smoking) $features[] = '<i class="bx bx-smoke" title="Smoking"></i>';
                if ($row->is_accessible) $features[] = '<i class="bx bx-handicap" title="Accessible"></i>';
                if ($row->is_connecting) $features[] = '<i class="bx bx-link" title="Connecting"></i>';
                return implode(' ', $features);
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item edit-room" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><a class="dropdown-item change-status" href="#" data-id="'.$row->id.'">Change Status</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="javascript:void(0);" class="dropdown-item text-danger delete-room" data-id="'.$row->id.'">Delete</a></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'housekeeping_badge', 'features_display', 'action'])
            ->make(true);
    }

    // Store new room
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_number' => [
                'required',
                'string',
                'max:50',
                'unique:rooms,room_number,NULL,id,property_id,' . $request->property_id
            ],
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'nullable|integer|min:0|max:100',
            'block' => 'nullable|string|max:50',
            'wing' => 'nullable|string|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance,out-of-order,blocked',
            'housekeeping_status' => 'required|in:clean,dirty,inspected,out-of-service,pickup',
            'price_override' => 'nullable|numeric|min:0',
            'is_smoking' => 'boolean',
            'is_accessible' => 'boolean',
            'is_connecting' => 'boolean',
            'connecting_room_id' => 'nullable|exists:rooms,id',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $room = Room::create([
                'property_id' => $request->property_id,
                'room_number' => $request->room_number,
                'room_type_id' => $request->room_type_id,
                'floor' => $request->floor,
                'block' => $request->block,
                'wing' => $request->wing,
                'status' => $request->status,
                'housekeeping_status' => $request->housekeeping_status,
                'price_override_cents' => $request->price_override ? $request->price_override * 100 : null,
                'is_smoking' => $request->is_smoking ?? false,
                'is_accessible' => $request->is_accessible ?? false,
                'is_connecting' => $request->is_connecting ?? false,
                'connecting_room_id' => $request->connecting_room_id,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Room created successfully',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create room: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single room
    public function show($id)
    {
        $room = Room::with(['property', 'roomType', 'connectingRoom'])->findOrFail($id);
        
        // Convert cents to dollars for frontend
        $room->price_override_display = $room->price_override;
        
        return response()->json($room);
    }

    // Update room
    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_number' => [
                'required',
                'string',
                'max:50',
                'unique:rooms,room_number,' . $id . ',id,property_id,' . $request->property_id
            ],
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'nullable|integer|min:0|max:100',
            'block' => 'nullable|string|max:50',
            'wing' => 'nullable|string|max:50',
            'status' => 'required|in:available,occupied,reserved,maintenance,out-of-order,blocked',
            'housekeeping_status' => 'required|in:clean,dirty,inspected,out-of-service,pickup',
            'price_override' => 'nullable|numeric|min:0',
            'is_smoking' => 'boolean',
            'is_accessible' => 'boolean',
            'is_connecting' => 'boolean',
            'connecting_room_id' => 'nullable|exists:rooms,id|not_in:' . $id,
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $room->update([
                'property_id' => $request->property_id,
                'room_number' => $request->room_number,
                'room_type_id' => $request->room_type_id,
                'floor' => $request->floor,
                'block' => $request->block,
                'wing' => $request->wing,
                'status' => $request->status,
                'housekeeping_status' => $request->housekeeping_status,
                'price_override_cents' => $request->price_override ? $request->price_override * 100 : null,
                'is_smoking' => $request->is_smoking ?? false,
                'is_accessible' => $request->is_accessible ?? false,
                'is_connecting' => $request->is_connecting ?? false,
                'connecting_room_id' => $request->connecting_room_id,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Room updated successfully',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update room: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete room
    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'status' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // Safety check: room currently occupied or reserved
        if (in_array($room->status, ['occupied', 'reserved'])) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete a room that is currently ' . $room->status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $room->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Room deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete room: ' . $e->getMessage()
            ], 500);
        }
    }

    // Bulk status update
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'status' => 'required|in:available,occupied,reserved,maintenance,out-of-order,blocked',
            'housekeeping_status' => 'nullable|in:clean,dirty,inspected,out-of-service,pickup',
        ]);

        DB::beginTransaction();
        try {
            $updateData = ['status' => $request->status];
            if ($request->filled('housekeeping_status')) {
                $updateData['housekeeping_status'] = $request->housekeeping_status;
            }

            Room::whereIn('id', $request->room_ids)->update($updateData);
            
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($request->room_ids) . ' room(s) updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get available rooms for booking
    public function getAvailableRooms(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date|after:check_in',
        ]);

        $query = Room::with('roomType')
            ->available()
            ->where('property_id', $request->property_id);

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        // TODO: Add booking conflict check if you have reservations table
        
        $rooms = $query->ordered()->get();

        return response()->json([
            'status' => true,
            'data' => $rooms
        ]);
    }
}