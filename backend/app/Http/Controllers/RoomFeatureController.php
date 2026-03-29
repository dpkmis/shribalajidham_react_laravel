<?php

namespace App\Http\Controllers;

use App\Models\RoomFeature;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomFeatureController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Room Management', route('room-features.index'));
        Breadcrumbs::add('Room Features');
        
        $properties = Property::all();

        return view('room-features.index', compact('properties'));
    }

    public function ajaxRoomFeatures(Request $request)
    {
        $query = RoomFeature::with('property')
            ->select(['room_features.*'])
            ->orderBy('room_features.sort_order')
            ->orderBy('room_features.name');

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
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('room_features.created_at', [$start, $end]);
                        }
                    } elseif ($colName === 'is_active') {
                        $query->where('is_active', $searchValue === 'active' ? 1 : 0);
                    } else {
                        $query->where("room_features.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('icon_display', function ($row) {
                return $row->icon ? '<i class="'.$row->icon.'"></i>' : '-';
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
                            <li><a class="dropdown-item edit-feature" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="javascript:void(0);" class="dropdown-item text-danger delete-feature" data-id="'.$row->id.'">Delete</a></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['icon_display', 'status', 'action'])
            ->make(true);
    }

    // Store new feature
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:room_features,code,NULL,id,property_id,' . ($request->property_id ?? 'NULL'),
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $feature = RoomFeature::create([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'code' => $request->code ?? Str::slug($request->name),
                'icon' => $request->icon,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Feature created successfully',
                'data' => $feature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create feature: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single feature
    public function show($id)
    {
        $feature = RoomFeature::with('property')->findOrFail($id);
        return response()->json($feature);
    }

    // Update feature
    public function update(Request $request, $id)
    {
        $feature = RoomFeature::findOrFail($id);

        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:room_features,code,' . $id . ',id,property_id,' . ($request->property_id ?? 'NULL'),
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $feature->update([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'code' => $request->code ?? Str::slug($request->name),
                'icon' => $request->icon,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Feature updated successfully',
                'data' => $feature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update feature: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete feature
    public function destroy($id)
    {
        $feature = RoomFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        // Check if feature is assigned to room types
        $assignedCount = $feature->roomTypes()->count();
        if ($assignedCount > 0) {
            return response()->json([
                'status' => false,
                'message' => "This feature is assigned to {$assignedCount} room type(s) and cannot be deleted"
            ], 422);
        }

        try {
            $feature->delete();

            return response()->json([
                'status' => true,
                'message' => 'Feature deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete feature: ' . $e->getMessage()
            ], 500);
        }
    }
}