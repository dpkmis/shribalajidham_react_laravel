<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Property;
use App\Models\RoomFeature;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomTypeController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Room Management', route('room-types.index'));
        Breadcrumbs::add('Room Types');
        
        $properties = Property::all();
        $features = RoomFeature::active()->ordered()->get();

        return view('room-types.index', compact('properties', 'features'));
    }

    public function ajaxRoomTypes(Request $request)
    {
        $query = RoomType::with(['property', 'rooms'])
            ->select(['room_types.*'])
            ->orderBy('room_types.sort_order')
            ->orderBy('room_types.created_at', 'desc');

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
                    } elseif ($colName === 'created_at') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('room_types.created_at', [$start, $end]);
                        }
                    } elseif ($colName === 'is_active') {
                        $query->where('is_active', $searchValue === 'active' ? 1 : 0);
                    } else {
                        $query->where("room_types.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('default_rate_display', function ($row) {
                return '₹' . number_format($row->default_rate, 2);
            })
            ->addColumn('occupancy_display', function ($row) {
                return $row->max_adults . 'A + ' . $row->max_children . 'C';
            })
            ->addColumn('room_count', function ($row) {
                $total = $row->rooms->count();
                $available = $row->rooms->where('status', 'available')->count();
                return "<span class='badge bg-primary'>{$available}/{$total}</span>";
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item edit-room-type" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="javascript:void(0);" class="dropdown-item text-danger delete-room-type" data-id="'.$row->id.'">Delete</a></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['status', 'action', 'room_count'])
            ->make(true);
    }

    // Store new room type
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:room_types,code,NULL,id,property_id,' . ($request->property_id ?? 'NULL'),
            'description' => 'nullable|string',
            'default_rate' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:20',
            'max_adults' => 'required|integer|min:1|max:20',
            'max_children' => 'nullable|integer|min:0|max:10',
            'beds' => 'required|integer|min:1|max:10',
            'room_size_sqm' => 'nullable|numeric|min:0',
            'bed_type' => 'required|in:single,double,queen,king,twin',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'features' => 'nullable|array',
            'features.*' => 'exists:room_features,id',
            'room_images' => 'nullable|array|max:5',
            'room_images.*' => 'image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Handle image uploads
            $images = [];
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $file) {
                    $path = $file->store('room-types', 'public');
                    $images[] = '/storage/' . $path;
                }
            }

            $roomType = RoomType::create([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'code' => $request->code ?? Str::slug($request->name),
                'description' => $request->description,
                'default_rate_cents' => $request->default_rate * 100,
                'max_occupancy' => $request->max_occupancy,
                'max_adults' => $request->max_adults,
                'max_children' => $request->max_children ?? 0,
                'beds' => $request->beds,
                'room_size_sqm' => $request->room_size_sqm,
                'bed_type' => $request->bed_type,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
                'images' => !empty($images) ? $images : null,
            ]);

            // Sync features
            if ($request->has('features')) {
                $roomType->features()->sync($request->features);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Room type created successfully',
                'data' => $roomType
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create room type: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single room type
    public function show($id)
    {
        $roomType = RoomType::with(['property', 'features'])->findOrFail($id);
        
        // Convert cents to dollars for frontend
        $roomType->default_rate_display = $roomType->default_rate;
        
        return response()->json($roomType);
    }

    // Update room type
    public function update(Request $request, $id)
    {
        $roomType = RoomType::findOrFail($id);

        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:room_types,code,' . $id . ',id,property_id,' . ($request->property_id ?? 'NULL'),
            'description' => 'nullable|string',
            'default_rate' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1|max:20',
            'max_adults' => 'required|integer|min:1|max:20',
            'max_children' => 'nullable|integer|min:0|max:10',
            'beds' => 'required|integer|min:1|max:10',
            'room_size_sqm' => 'nullable|numeric|min:0',
            'bed_type' => 'required|in:single,double,queen,king,twin',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'features' => 'nullable|array',
            'features.*' => 'exists:room_features,id',
            'room_images' => 'nullable|array|max:5',
            'room_images.*' => 'image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'remove_images' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Handle existing images
            $images = $roomType->images ?? [];

            // Remove images marked for deletion
            if ($request->filled('remove_images')) {
                $removeIndexes = explode(',', $request->remove_images);
                foreach ($removeIndexes as $idx) {
                    $idx = (int) $idx;
                    if (isset($images[$idx])) {
                        $path = str_replace('/storage/', '', $images[$idx]);
                        Storage::disk('public')->delete($path);
                        unset($images[$idx]);
                    }
                }
                $images = array_values($images);
            }

            // Add new uploaded images
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $file) {
                    $path = $file->store('room-types', 'public');
                    $images[] = '/storage/' . $path;
                }
            }

            $roomType->update([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'code' => $request->code ?? Str::slug($request->name),
                'description' => $request->description,
                'default_rate_cents' => $request->default_rate * 100,
                'max_occupancy' => $request->max_occupancy,
                'max_adults' => $request->max_adults,
                'max_children' => $request->max_children ?? 0,
                'beds' => $request->beds,
                'room_size_sqm' => $request->room_size_sqm,
                'bed_type' => $request->bed_type,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
                'images' => !empty($images) ? $images : null,
            ]);

            // Sync features
            $roomType->features()->sync($request->features ?? []);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Room type updated successfully',
                'data' => $roomType
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update room type: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete room type
    public function destroy($id)
    {
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return response()->json([
                'status' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        // Safety check: rooms assigned
        $assignedRooms = $roomType->rooms()->count();
        if ($assignedRooms > 0) {
            return response()->json([
                'status' => false,
                'message' => "This room type has {$assignedRooms} room(s) assigned and cannot be deleted"
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete uploaded images
            if ($roomType->images) {
                foreach ($roomType->images as $img) {
                    $path = str_replace('/storage/', '', $img);
                    Storage::disk('public')->delete($path);
                }
            }

            // Remove feature associations
            $roomType->features()->detach();

            // Delete room type
            $roomType->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Room type deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete room type: ' . $e->getMessage()
            ], 500);
        }
    }

    // Bulk update sort order
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:room_types,id',
            'items.*.sort_order' => 'required|integer'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                RoomType::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Sort order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update sort order'
            ], 500);
        }
    }
}