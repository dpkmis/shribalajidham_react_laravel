<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingTask;
use App\Models\HousekeepingStaff;
use App\Models\HousekeepingChecklist;
use App\Models\Room;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HousekeepingController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Housekeeping Management', route('housekeeping.index'));
        Breadcrumbs::add('Tasks');
        
        $properties = Property::all();
        $staff = HousekeepingStaff::active()->ordered()->get();
        $taskTypes = HousekeepingTask::getTaskTypes();
        $statusOptions = HousekeepingTask::getStatusOptions();
        $priorityOptions = HousekeepingTask::getPriorityOptions();

        return view('housekeeping.index', compact('properties', 'staff', 'taskTypes', 'statusOptions', 'priorityOptions'));
    }

    public function ajaxTasks(Request $request)
    {
        $query = HousekeepingTask::with(['property', 'room', 'staff'])
            ->select(['housekeeping_tasks.*'])
            ->orderBy('housekeeping_tasks.priority', 'desc')
            ->orderBy('housekeeping_tasks.scheduled_date')
            ->orderBy('housekeeping_tasks.scheduled_time');

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
                    } elseif ($colName === 'room.room_number') {
                        $query->whereHas('room', function ($q) use ($searchValue) {
                            $q->where('room_number', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'staff.full_name') {
                        $query->whereHas('staff', function ($q) use ($searchValue) {
                            $q->where('full_name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'scheduled_date') {
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('housekeeping_tasks.scheduled_date', [$start, $end]);
                        }
                    } elseif (in_array($colName, ['task_type', 'status', 'priority'])) {
                        $query->where("housekeeping_tasks.{$colName}", $searchValue);
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('room_display', function ($row) {
                $roomInfo = '<div><strong>' . $row->room->room_number . '</strong></div>';
                if ($row->room->roomType) {
                    $roomInfo .= '<small class="text-muted">' . $row->room->roomType->name . '</small>';
                }
                return $roomInfo;
            })
            ->addColumn('staff_display', function ($row) {
                if (!$row->staff) {
                    return '<span class="badge bg-secondary">Unassigned</span>';
                }
                return '<div>' . $row->staff->full_name . '</div><small class="text-muted">' . ucfirst($row->staff->shift) . ' Shift</small>';
            })
            ->addColumn('task_type_badge', function ($row) {
                $colors = [
                    'checkout-cleaning' => 'danger',
                    'daily-cleaning' => 'primary',
                    'deep-cleaning' => 'warning',
                    'turndown-service' => 'info',
                    'maintenance-cleaning' => 'secondary',
                    'inspection' => 'success'
                ];
                $color = $colors[$row->task_type] ?? 'secondary';
                $label = str_replace('-', ' ', ucwords($row->task_type, '-'));
                return '<span class="badge bg-'.$color.'">'.$label.'</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'pending' => 'secondary',
                    'assigned' => 'info',
                    'in-progress' => 'warning',
                    'completed' => 'success',
                    'inspected' => 'primary',
                    'rejected' => 'danger',
                    'cancelled' => 'dark'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                $label = ucfirst(str_replace('-', ' ', $row->status));
                return '<span class="badge bg-'.$color.'">'.$label.'</span>';
            })
            ->addColumn('priority_badge', function ($row) {
                $colors = [
                    'low' => 'secondary',
                    'normal' => 'info',
                    'high' => 'warning',
                    'urgent' => 'danger'
                ];
                $icons = [
                    'low' => 'bx-down-arrow-alt',
                    'normal' => 'bx-minus',
                    'high' => 'bx-up-arrow-alt',
                    'urgent' => 'bx-error'
                ];
                $color = $colors[$row->priority] ?? 'info';
                $icon = $icons[$row->priority] ?? 'bx-minus';
                return '<span class="badge bg-'.$color.'"><i class="bx '.$icon.'"></i> '.ucfirst($row->priority).'</span>';
            })
            ->addColumn('schedule_display', function ($row) {
                $date = $row->scheduled_date->format('d M Y');
                $time = $row->scheduled_time ? $row->scheduled_time->format('h:i A') : 'Any time';
                
                $isOverdue = $row->scheduled_date->isPast() && !in_array($row->status, ['completed', 'inspected', 'cancelled']);
                $class = $isOverdue ? 'text-danger' : '';
                
                return '<div class="'.$class.'"><strong>'.$date.'</strong><br><small>'.$time.'</small></div>';
            })
            ->addColumn('duration_display', function ($row) {
                if ($row->actual_duration_minutes) {
                    return $row->actual_duration_minutes . ' min';
                }
                return '<span class="text-muted">' . $row->estimated_duration_minutes . ' min (est.)</span>';
            })
            ->addColumn('flags_display', function ($row) {
                $flags = [];
                if ($row->is_occupied) $flags[] = '<i class="bx bx-user text-info" title="Occupied"></i>';
                if ($row->guest_present) $flags[] = '<i class="bx bx-home text-warning" title="Guest Present"></i>';
                if ($row->do_not_disturb) $flags[] = '<i class="bx bx-block text-danger" title="Do Not Disturb"></i>';
                if ($row->special_instructions) $flags[] = '<i class="bx bx-note text-primary" title="Special Instructions"></i>';
                return implode(' ', $flags) ?: '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="dropdown ms-auto">
                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-horizontal-rounded text-option"></i>
                    </a>
                    <ul class="dropdown-menu">';
                
                $actions .= '<li><a class="dropdown-item edit-task" href="#" data-id="'.$row->id.'"><i class="bx bx-edit"></i> Edit</a></li>';
                
                if ($row->status === 'pending' || $row->status === 'assigned') {
                    $actions .= '<li><a class="dropdown-item start-task" href="#" data-id="'.$row->id.'"><i class="bx bx-play"></i> Start Task</a></li>';
                }
                
                if ($row->status === 'in-progress') {
                    $actions .= '<li><a class="dropdown-item complete-task" href="#" data-id="'.$row->id.'"><i class="bx bx-check"></i> Complete</a></li>';
                }
                
                if ($row->status === 'completed') {
                    $actions .= '<li><a class="dropdown-item inspect-task" href="#" data-id="'.$row->id.'"><i class="bx bx-search"></i> Inspect</a></li>';
                }
                
                $actions .= '<li><a class="dropdown-item view-details" href="#" data-id="'.$row->id.'"><i class="bx bx-show"></i> View Details</a></li>';
                $actions .= '<li><hr class="dropdown-divider"></li>';
                $actions .= '<li><a href="javascript:void(0);" class="dropdown-item text-danger delete-task" data-id="'.$row->id.'"><i class="bx bx-trash"></i> Delete</a></li>';
                $actions .= '</ul></div>';
                
                return $actions;
            })
            ->rawColumns(['room_display', 'staff_display', 'task_type_badge', 'status_badge', 'priority_badge', 'schedule_display', 'duration_display', 'flags_display', 'action'])
            ->make(true);
    }

    // Store new task
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'required|exists:rooms,id',
            'assigned_to' => 'nullable|exists:housekeeping_staff,id',
            'task_type' => 'required|in:checkout-cleaning,daily-cleaning,deep-cleaning,turndown-service,maintenance-cleaning,inspection',
            'priority' => 'required|in:low,normal,high,urgent',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:480',
            'special_instructions' => 'nullable|string',
            'is_occupied' => 'boolean',
            'guest_present' => 'boolean',
            'do_not_disturb' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $task = HousekeepingTask::create([
                'property_id' => $request->property_id,
                'room_id' => $request->room_id,
                'assigned_to' => $request->assigned_to,
                'assigned_by' => Auth::id(),
                'task_type' => $request->task_type,
                'status' => $request->assigned_to ? 'assigned' : 'pending',
                'priority' => $request->priority,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'estimated_duration_minutes' => $request->estimated_duration_minutes ?? 30,
                'special_instructions' => $request->special_instructions,
                'is_occupied' => $request->is_occupied ?? false,
                'guest_present' => $request->guest_present ?? false,
                'do_not_disturb' => $request->do_not_disturb ?? false,
            ]);

            // Update room housekeeping status
            $room = Room::find($request->room_id);
            if ($room && in_array($room->housekeeping_status, ['clean', 'inspected'])) {
                $room->update(['housekeeping_status' => 'dirty']);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Task created successfully',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single task
    public function show($id)
    {
        $task = HousekeepingTask::with(['property', 'room', 'staff', 'assignedBy', 'inspector'])->findOrFail($id);
        
        $task->scheduled_date_display = $task->scheduled_date->format('Y-m-d');
        $task->scheduled_time_display = $task->scheduled_time ? $task->scheduled_time->format('H:i') : null;
        
        return response()->json($task);
    }

    // Update task
    public function update(Request $request, $id)
    {
        $task = HousekeepingTask::findOrFail($id);

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'required|exists:rooms,id',
            'assigned_to' => 'nullable|exists:housekeeping_staff,id',
            'task_type' => 'required|in:checkout-cleaning,daily-cleaning,deep-cleaning,turndown-service,maintenance-cleaning,inspection',
            'priority' => 'required|in:low,normal,high,urgent',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:480',
            'special_instructions' => 'nullable|string',
            'is_occupied' => 'boolean',
            'guest_present' => 'boolean',
            'do_not_disturb' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $task->update([
                'property_id' => $request->property_id,
                'room_id' => $request->room_id,
                'assigned_to' => $request->assigned_to,
                'task_type' => $request->task_type,
                'status' => $request->assigned_to && $task->status === 'pending' ? 'assigned' : $task->status,
                'priority' => $request->priority,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'estimated_duration_minutes' => $request->estimated_duration_minutes ?? 30,
                'special_instructions' => $request->special_instructions,
                'is_occupied' => $request->is_occupied ?? false,
                'guest_present' => $request->guest_present ?? false,
                'do_not_disturb' => $request->do_not_disturb ?? false,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete task
    public function destroy($id)
    {
        $task = HousekeepingTask::find($id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);
        }

        // Prevent deletion of in-progress or completed tasks
        if (in_array($task->status, ['in-progress', 'completed', 'inspected'])) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete a task that is ' . $task->status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $task->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Start task
    public function startTask(Request $request)
    {
        $task = HousekeepingTask::findOrFail($request->task_id);

        if (!in_array($task->status, ['pending', 'assigned'])) {
            return response()->json([
                'status' => false,
                'message' => 'Task cannot be started in current status'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $task->start();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Task started successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to start task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Complete task
    public function completeTask(Request $request)
    {
        $task = HousekeepingTask::findOrFail($request->task_id);

        if ($task->status !== 'in-progress') {
            return response()->json([
                'status' => false,
                'message' => 'Only in-progress tasks can be completed'
            ], 422);
        }

        $request->validate([
            'staff_notes' => 'nullable|string',
            'completed_items' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $task->complete();
            
            if ($request->filled('staff_notes')) {
                $task->update(['staff_notes' => $request->staff_notes]);
            }
            
            if ($request->filled('completed_items')) {
                $task->update(['completed_items' => $request->completed_items]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Task completed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to complete task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Inspect task
    public function inspectTask(Request $request)
    {
        $task = HousekeepingTask::findOrFail($request->task_id);

        if ($task->status !== 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'Only completed tasks can be inspected'
            ], 422);
        }

        $request->validate([
            'quality_rating' => 'required|integer|min:1|max:5',
            'inspection_notes' => 'nullable|string',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $task->inspect(
                    $request->quality_rating,
                    $request->inspection_notes,
                    Auth::id()
                );
            } else {
                $task->reject($request->rejection_reason);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $request->action === 'approve' ? 'Task approved successfully' : 'Task rejected'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to inspect task: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get rooms for property
    public function getRoomsByProperty(Request $request)
    {
        $rooms = Room::where('property_id', $request->property_id)
            ->where('is_active', true)
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'housekeeping_status']);

        return response()->json([
            'status' => true,
            'data' => $rooms
        ]);
    }

    // Get dashboard stats
    public function getDashboardStats(Request $request)
    {
        $query = HousekeepingTask::query();

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $today = $query->whereDate('scheduled_date', today());

        $stats = [
            'today_total' => (clone $today)->count(),
            'today_pending' => (clone $today)->where('status', 'pending')->count(),
            'today_in_progress' => (clone $today)->where('status', 'in-progress')->count(),
            'today_completed' => (clone $today)->whereIn('status', ['completed', 'inspected'])->count(),
            'overdue' => HousekeepingTask::overdue()
                ->when($request->filled('property_id'), fn($q) => $q->where('property_id', $request->property_id))
                ->count(),
            'high_priority' => (clone $today)->whereIn('priority', ['high', 'urgent'])->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => $stats
        ]);
    }
}