<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display users listing
     */
    public function index()
    {
        Breadcrumbs::add('User Management', route('users.index'));
        Breadcrumbs::add('Users');
        
        $properties = Property::all();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('properties', 'roles'));
    }

    /**
     * Get users data for DataTables
     */
    public function ajaxUsers(Request $request)
    {
        $query = User::with(['property', 'roles'])
            ->select(['users.*'])
            ->orderBy('users.created_at', 'desc');

        // Apply property filter if not super admin
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('property_id', auth()->user()->property_id);
        }

        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
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
                    } elseif ($colName === 'roles_display') {
                        $query->whereHas('roles', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'created_at') {
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('created_at', [$start, $end]);
                        }
                    } else {
                        $query->where("users.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name_display', function ($row) {
                $avatar = $row->avatar 
                    ? '<img src="' . asset($row->avatar) . '" class="rounded-circle me-2" width="32" height="32">'
                    : '<div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">' . strtoupper(substr($row->name, 0, 1)) . '</div>';
                
                return '<div class="d-flex align-items-center">' . 
                       $avatar .
                       '<div>' .
                       '<div class="fw-bold">' . $row->name . '</div>' .
                       '<small class="text-muted">' . $row->email . '</small>' .
                       '</div></div>';
            })
            ->addColumn('property_display', function ($row) {
                return $row->property ? $row->property->name : '<span class="badge bg-secondary">Global</span>';
            })
            ->addColumn('roles_display', function ($row) {
                $badges = $row->roles->map(function($role) {
                    $colors = [
                        'super-admin' => 'danger',
                        'property-admin' => 'primary',
                        'manager' => 'info',
                        'receptionist' => 'success',
                        'accountant' => 'warning'
                    ];
                    $color = $colors[$role->slug] ?? 'secondary';
                    return '<span class="badge bg-'.$color.' me-1">'.$role->name.'</span>';
                })->join('');
                
                return $badges ?: '<span class="text-muted">No roles</span>';
            })
            ->addColumn('contact_display', function ($row) {
                return '<div>' .
                       ($row->phone ? '<div><i class="bx bx-phone"></i> ' . $row->phone . '</div>' : '') .
                       ($row->designation ? '<small class="text-muted">' . $row->designation . '</small>' : '') .
                       '</div>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->is_locked) {
                    return '<span class="badge bg-danger"><i class="bx bx-lock"></i> Locked</span>';
                }
                
                $color = $row->is_active ? 'success' : 'secondary';
                $text = $row->is_active ? 'Active' : 'Inactive';
                return '<span class="badge bg-'.$color.'">'.$text.'</span>';
            })
            ->addColumn('last_login_display', function ($row) {
                if ($row->last_login_at) {
                    return '<div>' . $row->last_login_at->format('d M Y') . '</div>' .
                           '<small class="text-muted">' . $row->last_login_at->format('h:i A') . '</small>';
                }
                return '<span class="text-muted">Never</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu">';
                
                // View
                if (auth()->user()->hasPermission('users.view')) {
                    $actions .= '<li><a class="dropdown-item view-user" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-show"></i> View</a></li>';
                }
                
                // Edit
                if (auth()->user()->hasPermission('users.edit')) {
                    $actions .= '<li><a class="dropdown-item edit-user" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-edit"></i> Edit</a></li>';
                }
                
                $actions .= '<li><hr class="dropdown-divider"></li>';
                
                // Activate/Deactivate
                if (auth()->user()->hasPermission('users.edit')) {
                    if ($row->is_active) {
                        $actions .= '<li><a class="dropdown-item deactivate-user" href="#" data-id="'.$row->id.'">
                                    <i class="bx bx-user-x"></i> Deactivate</a></li>';
                    } else {
                        $actions .= '<li><a class="dropdown-item activate-user" href="#" data-id="'.$row->id.'">
                                    <i class="bx bx-user-check"></i> Activate</a></li>';
                    }
                }
                
                // Unlock
                if ($row->is_locked && auth()->user()->hasPermission('users.edit')) {
                    $actions .= '<li><a class="dropdown-item unlock-user" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-lock-open"></i> Unlock</a></li>';
                }
                
                $actions .= '<li><hr class="dropdown-divider"></li>';
                
                // Reset Password
                if (auth()->user()->hasPermission('users.edit')) {
                    $actions .= '<li><a class="dropdown-item reset-password" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-key"></i> Reset Password</a></li>';
                }
                
                // View Activity
                $actions .= '<li><a class="dropdown-item view-activity" href="#" data-id="'.$row->id.'">
                            <i class="bx bx-history"></i> View Activity</a></li>';
                
                // Delete
                if (auth()->user()->hasPermission('users.delete') && $row->id !== auth()->id()) {
                    $actions .= '<li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger delete-user" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-trash"></i> Delete</a></li>';
                }
                
                $actions .= '</ul></div>';
                
                return $actions;
            })
            ->rawColumns(['name_display', 'property_display', 'roles_display', 'contact_display', 'status_badge', 'last_login_display', 'action'])
            ->make(true);
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('users.create')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'designation' => $request->designation,
                'department' => $request->department,
                'date_of_joining' => $request->date_of_joining,
                'is_active' => true,
                'created_by_user_id' => auth()->id()
            ]);

            // Assign roles
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            }

            // Log activity
            $user->logActivity('created', 'users', 'User account created by ' . auth()->user()->name);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        if (!auth()->user()->hasPermission('users.view')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $user = User::with(['property', 'roles.permissions', 'createdBy'])
            ->findOrFail($id);

        // Get user stats
        $stats = $user->getStats();
        
        // Get recent activity
        $recentActivity = $user->getRecentActivity(15);

        return response()->json([
            'status' => true,
            'user' => $user,
            'stats' => $stats,
            'recent_activity' => $recentActivity
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'designation' => $request->designation,
                'department' => $request->department,
                'date_of_joining' => $request->date_of_joining,
                'updated_by_user_id' => auth()->id()
            ]);

            // Update roles
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            }

            // Log activity
            $user->logActivity('updated', 'users', 'User details updated by ' . auth()->user()->name);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('users.delete')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        // Check if user can be deleted
        if (!$user->canBeDeleted()) {
            return response()->json([
                'status' => false,
                'message' => 'User cannot be deleted due to existing records'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Log before deletion
            auth()->user()->logActivity('deleted_user', 'users', 'Deleted user: ' . $user->name);
            
            $user->delete();
            
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate user
     */
    public function activate($id)
    {
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->activate();

        return response()->json([
            'status' => true,
            'message' => 'User activated successfully'
        ]);
    }

    /**
     * Deactivate user
     */
    public function deactivate($id)
    {
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot deactivate your own account'
            ], 422);
        }

        $user->deactivate();

        return response()->json([
            'status' => true,
            'message' => 'User deactivated successfully'
        ]);
    }

    /**
     * Unlock user account
     */
    public function unlock($id)
    {
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->unlockAccount();

        return response()->json([
            'status' => true,
            'message' => 'User account unlocked successfully'
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        $user = User::findOrFail($id);
        
        $user->update([
            'password' => $request->password
        ]);

        $user->logActivity('password_reset', 'users', 'Password reset by ' . auth()->user()->name);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully'
        ]);
    }

    /**
     * Get user activity logs
     */
    public function getActivity($id)
    {
        $user = User::findOrFail($id);
        
        $activities = $user->activityLogs()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'activities' => $activities
        ]);
    }

    /**
     * Get user stats for dashboard
     */
    public function getUserStats(Request $request)
    {
        $query = User::query();

        if (!auth()->user()->isSuperAdmin()) {
            $query->where('property_id', auth()->user()->property_id);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $stats = [
            'total_users' => $query->count(),
            'active_users' => (clone $query)->where('is_active', true)->count(),
            'inactive_users' => (clone $query)->where('is_active', false)->count(),
            'locked_users' => (clone $query)->where('locked_until', '>', now())->count(),
            'online_users' => (clone $query)->whereHas('sessions', function($q) {
                $q->where('is_active', true)
                  ->where('last_activity', '>', now()->subMinutes(15));
            })->count()
        ];

        return response()->json([
            'status' => true,
            'stats' => $stats
        ]);
    }

    private function getStatus($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
    }
}   