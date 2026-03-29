<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Property;
use App\Models\Role;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Role Management', route('roles.index'));
        Breadcrumbs::add('Roles');
        $properties = Property::all();
        $permissions = Permission::orderBy('name')->get();

        return view('roles.index', compact('properties', 'permissions'));
    }

    public function ajaxRoles(Request $request)
    {
        $query = Role::with('property')
            ->select(['roles.id', 'roles.property_id', 'roles.name', 'roles.slug', 'roles.created_at'])->orderBy('roles.created_at', 'desc');

        // Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];

                if (! empty($searchValue)) {
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

                            // dd($start, $end);

                            $query->whereBetween('created_at', [$start, $end]);
                        }
                    } else {
                        $query->where($colName, 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('created_at', function ($query, $keyword) {
                // yaha kuch bhi nahi karna because date range filter hum upar hi handle kar chuke hain
                // agar chaho to aap specific handling bhi kar sakte ho
            })
            ->editColumn('title', function ($row) {
                $titles = json_decode($row->title, true);

                return $titles[0]['title'] ?? $row->title;
            })
            ->addColumn('type', function ($row) {
                $titles = json_decode($row->title, true);

                return $titles[0]['language'] ?? '';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item edit-role" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item text-danger delete-role" data-id="'.$row->id.'">Delete</a>
                            </li>

                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);

    }

    // Show create form
    public function create()
    {
        $properties = Property::all();
        $permissions = Permission::orderBy('name')->get();

        return view('roles.create', compact('properties', 'permissions'));
    }

    // Store new role
    public function store(Request $request)
    {
        // Inline validation
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'property_id' => 'nullable|exists:properties,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'property_id' => $request->property_id,
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return response()->json([
            'status' => true,
            'message' => 'Role created successfully',
        ]);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json($role);
    }


    // Update a role
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,'.$id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'property_id' => $request->property_id,
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully',
        ]);
    }

    // Delete a role safely
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        // 🔒 Safety 1: Check assigned users
        $assignedUsers = DB::table('role_user')
            ->where('role_id', $role->id)
            ->count();

        if ($assignedUsers > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Role is assigned to users and cannot be deleted'
            ], 422);
        }

        // 🔒 Safety 2: Protect system roles (optional)
        if (in_array($role->slug, ['super-admin', 'property-admin'])) {
            return response()->json([
                'status' => false,
                'message' => 'This role is protected and cannot be deleted'
            ], 403);
        }

        DB::transaction(function () use ($role) {
            // Remove permissions mapping
            DB::table('permission_role')
                ->where('role_id', $role->id)
                ->delete();

            // Delete role
            $role->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}
