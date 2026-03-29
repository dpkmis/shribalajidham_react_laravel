<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingStaff;
use App\Models\HousekeepingTask;
use App\Models\HousekeepingAttendance;
use App\Models\Property;
use App\Models\User;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HousekeepingStaffController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Housekeeping Management', route('housekeeping.index'));
        Breadcrumbs::add('Staff Management');
        
        $properties = Property::all();
        $employmentTypes = HousekeepingStaff::getEmploymentTypes();
        $shifts = HousekeepingStaff::getShifts();

        return view('housekeeping.staff.index', compact('properties', 'employmentTypes', 'shifts'));
    }

    public function ajaxStaff(Request $request)
    {
        $query = HousekeepingStaff::with(['property', 'user'])
            ->select(['housekeeping_staff.*']);

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
                            $query->whereBetween('housekeeping_staff.created_at', [$start, $end]);
                        }
                    } elseif (in_array($colName, ['full_name', 'staff_code', 'email', 'phone', 'employment_type', 'shift'])) {
                        $query->where("housekeeping_staff.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('staff_display', function ($row) {
                $html = '<div>';
                $html .= '<strong>' . $row->full_name . '</strong><br>';
                $html .= '<small class="text-muted">' . $row->staff_code . '</small>';
                if ($row->is_supervisor) {
                    $html .= ' <span class="badge bg-primary">Supervisor</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('contact_display', function ($row) {
                $html = '<div>';
                if ($row->email) {
                    $html .= '<i class="bx bx-envelope"></i> ' . $row->email . '<br>';
                }
                if ($row->phone) {
                    $html .= '<i class="bx bx-phone"></i> ' . $row->phone;
                }
                return $html ?: '-';
            })
            ->addColumn('employment_badge', function ($row) {
                $colors = [
                    'full-time' => 'success',
                    'part-time' => 'info',
                    'contract' => 'warning',
                    'temporary' => 'secondary'
                ];
                $color = $colors[$row->employment_type] ?? 'secondary';
                $label = ucwords(str_replace('-', ' ', $row->employment_type));
                return '<span class="badge bg-'.$color.'">'.$label.'</span>';
            })
            ->addColumn('shift_badge', function ($row) {
                $colors = [
                    'morning' => 'warning',
                    'afternoon' => 'info',
                    'evening' => 'primary',
                    'night' => 'dark',
                    'rotating' => 'secondary'
                ];
                $icons = [
                    'morning' => 'bx-sun',
                    'afternoon' => 'bx-cloud',
                    'evening' => 'bx-moon',
                    'night' => 'bx-star',
                    'rotating' => 'bx-sync'
                ];
                $color = $colors[$row->shift] ?? 'secondary';
                $icon = $icons[$row->shift] ?? 'bx-time';
                return '<span class="badge bg-'.$color.'"><i class="bx '.$icon.'"></i> '.ucfirst($row->shift).'</span>';
            })
            ->addColumn('workload_display', function ($row) {
                $today = $row->getTodayWorkload();
                $max = $row->max_rooms_per_day;
                $percentage = $max > 0 ? ($today / $max) * 100 : 0;
                
                $colorClass = 'success';
                if ($percentage >= 100) {
                    $colorClass = 'danger';
                } elseif ($percentage >= 80) {
                    $colorClass = 'warning';
                }
                
                return '<div class="d-flex flex-column">
                    <span class="text-'.$colorClass.' fw-bold">'.$today.' / '.$max.'</span>
                    <small class="text-muted">Today\'s Rooms</small>
                </div>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->is_active) {
                    return '<span class="badge bg-success">Active</span>';
                } else {
                    return '<span class="badge bg-secondary">Inactive</span>';
                }
            })
            ->addColumn('tenure_display', function ($row) {
                if ($row->joining_date) {
                    $tenure = $row->joining_date->diffForHumans(null, true);
                    $date = $row->joining_date->format('d M Y');
                    return '<div>'.$tenure.'<br><small class="text-muted">Since '.$date.'</small></div>';
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="dropdown ms-auto">
                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-horizontal-rounded text-option"></i>
                    </a>
                    <ul class="dropdown-menu">';
                
                $actions .= '<li><a class="dropdown-item edit-staff" href="#" data-id="'.$row->id.'"><i class="bx bx-edit"></i> Edit</a></li>';
                $actions .= '<li><a class="dropdown-item view-workload" href="#" data-id="'.$row->id.'"><i class="bx bx-bar-chart"></i> View Workload</a></li>';
                $actions .= '<li><a class="dropdown-item mark-attendance" href="#" data-id="'.$row->id.'"><i class="bx bx-calendar-check"></i> Mark Attendance</a></li>';
                $actions .= '<li><hr class="dropdown-divider"></li>';
                
                if ($row->is_active) {
                    $actions .= '<li><a class="dropdown-item text-warning toggle-status" href="#" data-id="'.$row->id.'"><i class="bx bx-pause"></i> Deactivate</a></li>';
                } else {
                    $actions .= '<li><a class="dropdown-item text-success toggle-status" href="#" data-id="'.$row->id.'"><i class="bx bx-play"></i> Activate</a></li>';
                }
                
                $actions .= '<li><a href="javascript:void(0);" class="dropdown-item text-danger delete-staff" data-id="'.$row->id.'"><i class="bx bx-trash"></i> Delete</a></li>';
                $actions .= '</ul></div>';
                
                return $actions;
            })
            ->rawColumns(['staff_display', 'contact_display', 'employment_badge', 'shift_badge', 'workload_display', 'status_badge', 'tenure_display', 'action'])
            ->make(true);
    }

    // Store new staff
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'staff_code' => 'required|string|max:50|unique:housekeeping_staff,staff_code',
            'full_name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:20',
            'employment_type' => 'required|in:full-time,part-time,contract,temporary',
            'shift' => 'required|in:morning,afternoon,evening,night,rotating',
            'joining_date' => 'nullable|date',
            'max_rooms_per_day' => 'nullable|integer|min:1|max:30',
            'specializations' => 'nullable|array',
            'is_supervisor' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            
            // User account creation (optional)
            'create_user_account' => 'boolean',
            'username' => 'required_if:create_user_account,1|nullable|string|max:255|unique:users,email',
            'password' => 'required_if:create_user_account,1|nullable|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $userId = null;
            
            // Create user account if requested
            if ($request->create_user_account) {
                $user = User::create([
                    'name' => $request->full_name,
                    'email' => $request->username,
                    'password' => Hash::make($request->password),
                    // Add other user fields as needed
                ]);
                
                // Assign housekeeping role if you have role management
                // $user->assignRole('housekeeping-staff');
                
                $userId = $user->id;
            }

            $staff = HousekeepingStaff::create([
                'user_id' => $userId,
                'property_id' => $request->property_id,
                'staff_code' => $request->staff_code,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'employment_type' => $request->employment_type,
                'shift' => $request->shift,
                'joining_date' => $request->joining_date,
                'max_rooms_per_day' => $request->max_rooms_per_day ?? 12,
                'specializations' => $request->specializations ?? [],
                'is_supervisor' => $request->is_supervisor ?? false,
                'is_active' => $request->is_active ?? true,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Staff member added successfully',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add staff: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single staff
    public function show($id)
    {
        $staff = HousekeepingStaff::with(['property', 'user'])->findOrFail($id);
        
        $staff->joining_date_display = $staff->joining_date ? $staff->joining_date->format('Y-m-d') : null;
        $staff->leaving_date_display = $staff->leaving_date ? $staff->leaving_date->format('Y-m-d') : null;
        
        return response()->json($staff);
    }

    // Update staff
    public function update(Request $request, $id)
    {
        $staff = HousekeepingStaff::findOrFail($id);

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'staff_code' => 'required|string|max:50|unique:housekeeping_staff,staff_code,'.$id,
            'full_name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:20',
            'employment_type' => 'required|in:full-time,part-time,contract,temporary',
            'shift' => 'required|in:morning,afternoon,evening,night,rotating',
            'joining_date' => 'nullable|date',
            'leaving_date' => 'nullable|date|after:joining_date',
            'max_rooms_per_day' => 'nullable|integer|min:1|max:30',
            'specializations' => 'nullable|array',
            'is_supervisor' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $staff->update([
                'property_id' => $request->property_id,
                'staff_code' => $request->staff_code,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'employment_type' => $request->employment_type,
                'shift' => $request->shift,
                'joining_date' => $request->joining_date,
                'leaving_date' => $request->leaving_date,
                'max_rooms_per_day' => $request->max_rooms_per_day ?? 12,
                'specializations' => $request->specializations ?? [],
                'is_supervisor' => $request->is_supervisor ?? false,
                'is_active' => $request->is_active ?? true,
                'notes' => $request->notes,
            ]);

            // Update linked user account if exists
            if ($staff->user) {
                $staff->user->update([
                    'name' => $request->full_name,
                    'email' => $request->email,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Staff member updated successfully',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update staff: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete staff
    public function destroy($id)
    {
        $staff = HousekeepingStaff::find($id);

        if (!$staff) {
            return response()->json([
                'status' => false,
                'message' => 'Staff member not found'
            ], 404);
        }

        // Check if staff has active tasks
        $activeTasks = $staff->tasks()
            ->whereIn('status', ['assigned', 'in-progress'])
            ->count();

        if ($activeTasks > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete staff member with active tasks. Please reassign or complete tasks first.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $staff->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Staff member deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete staff: ' . $e->getMessage()
            ], 500);
        }
    }

    // Toggle staff active status
    public function toggleStatus(Request $request)
    {
        $staff = HousekeepingStaff::findOrFail($request->staff_id);

        DB::beginTransaction();
        try {
            $newStatus = !$staff->is_active;
            $staff->update(['is_active' => $newStatus]);

            // If deactivating, unassign from pending tasks
            if (!$newStatus) {
                HousekeepingTask::where('assigned_to', $staff->id)
                    ->whereIn('status', ['pending', 'assigned'])
                    ->update([
                        'assigned_to' => null,
                        'status' => 'pending'
                    ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Staff status updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mark attendance
    public function markAttendance(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:housekeeping_staff,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,half-day,leave,sick',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'notes' => 'nullable|string',
        ]);

        $staff = HousekeepingStaff::findOrFail($request->staff_id);

        DB::beginTransaction();
        try {
            $attendance = HousekeepingAttendance::updateOrCreate(
                [
                    'staff_id' => $request->staff_id,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'property_id' => $staff->property_id,
                    'status' => $request->status,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'notes' => $request->notes,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Attendance marked successfully',
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to mark attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get staff workload
    public function getWorkload($id)
    {
        $staff = HousekeepingStaff::with(['todayTasks.room'])->findOrFail($id);

        $workload = [
            'today_total' => $staff->todayTasks->count(),
            'today_pending' => $staff->todayTasks->where('status', 'pending')->count(),
            'today_assigned' => $staff->todayTasks->where('status', 'assigned')->count(),
            'today_in_progress' => $staff->todayTasks->where('status', 'in-progress')->count(),
            'today_completed' => $staff->todayTasks->whereIn('status', ['completed', 'inspected'])->count(),
            'max_rooms' => $staff->max_rooms_per_day,
            'can_take_more' => $staff->canTakeMoreRooms(),
            'tasks' => $staff->todayTasks->map(function($task) {
                return [
                    'id' => $task->id,
                    'room_number' => $task->room->room_number,
                    'task_type' => $task->task_type,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'started_at' => $task->started_at ? $task->started_at->format('H:i') : null,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'data' => $workload
        ]);
    }

    // Get staff performance
    public function getPerformance(Request $request, $id)
    {
        $staff = HousekeepingStaff::findOrFail($id);
        
        $startDate = $request->input('start_date', now()->subMonth());
        $endDate = $request->input('end_date', now());

        $tasks = HousekeepingTask::where('assigned_to', $id)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->get();

        $performance = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->whereIn('status', ['completed', 'inspected'])->count(),
            'rejected_tasks' => $tasks->where('status', 'rejected')->count(),
            'average_rating' => $tasks->whereNotNull('quality_rating')->avg('quality_rating'),
            'average_duration' => $tasks->whereNotNull('actual_duration_minutes')->avg('actual_duration_minutes'),
            'on_time_completion' => $tasks->filter(function($task) {
                return $task->completed_at && 
                       $task->completed_at->lte($task->scheduled_date->endOfDay());
            })->count(),
            'attendance_days' => HousekeepingAttendance::where('staff_id', $id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => $performance
        ]);
    }

    // Get available staff for shift
    public function getAvailableStaff(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'shift' => 'nullable|in:morning,afternoon,evening,night,rotating',
            'date' => 'nullable|date',
        ]);

        $date = $request->input('date', today());
        $query = HousekeepingStaff::active()
            ->where('property_id', $request->property_id);

        if ($request->filled('shift')) {
            $query->where(function($q) use ($request) {
                $q->where('shift', $request->shift)
                  ->orWhere('shift', 'rotating');
            });
        }

        $staff = $query->get()->map(function($member) use ($date) {
            $todayTasks = HousekeepingTask::where('assigned_to', $member->id)
                ->whereDate('scheduled_date', $date)
                ->count();

            return [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'staff_code' => $member->staff_code,
                'shift' => $member->shift,
                'current_workload' => $todayTasks,
                'max_rooms' => $member->max_rooms_per_day,
                'can_take_more' => $todayTasks < $member->max_rooms_per_day,
                'is_supervisor' => $member->is_supervisor,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $staff
        ]);
    }

    // Bulk assign tasks
    public function bulkAssignTasks(Request $request)
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:housekeeping_tasks,id',
            'staff_id' => 'required|exists:housekeeping_staff,id',
        ]);

        $staff = HousekeepingStaff::findOrFail($request->staff_id);

        // Check if staff can handle the workload
        $currentWorkload = $staff->getTodayWorkload();
        $newTasks = count($request->task_ids);
        
        if (($currentWorkload + $newTasks) > $staff->max_rooms_per_day) {
            return response()->json([
                'status' => false,
                'message' => "Staff workload would exceed maximum ({$staff->max_rooms_per_day} rooms/day). Current: {$currentWorkload}, Attempting to add: {$newTasks}"
            ], 422);
        }

        DB::beginTransaction();
        try {
            HousekeepingTask::whereIn('id', $request->task_ids)
                ->whereIn('status', ['pending', 'assigned'])
                ->update([
                    'assigned_to' => $request->staff_id,
                    'status' => 'assigned',
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($request->task_ids) . ' task(s) assigned successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to assign tasks: ' . $e->getMessage()
            ], 500);
        }
    }
}