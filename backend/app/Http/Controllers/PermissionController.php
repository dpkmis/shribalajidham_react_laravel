<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        $modules = config('hms_permissions');
        return view('permissions.index', compact('modules'));
    }

    public function ajax()
    {
        $query = Permission::select('id','name','slug','created_at')
            ->orderBy('created_at','desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('module', fn ($row) =>
                ucfirst(explode('.', $row->slug)[0] ?? '-')
            )
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-primary edit-permission" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-sm btn-danger delete-permission" data-id="'.$row->id.'">Delete</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:permissions,slug',
        ]);

        Permission::create($request->only('name','slug'));

        return response()->json([
            'status' => true,
            'message' => 'Permission created successfully'
        ]);
    }

    public function show($id)
    {
        return Permission::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:permissions,slug,'.$id,
        ]);

        $permission->update($request->only('name','slug'));

        return response()->json([
            'status' => true,
            'message' => 'Permission updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message'=>'Permission not found'],404);
        }

        // 🔒 Safety: assigned to role?
        $assigned = DB::table('permission_role')
            ->where('permission_id',$id)
            ->count();

        if ($assigned > 0) {
            return response()->json([
                'message' => 'Permission is assigned to roles'
            ],422);
        }

        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
