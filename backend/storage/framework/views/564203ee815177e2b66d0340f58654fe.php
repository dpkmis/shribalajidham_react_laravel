<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('style'); ?>
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                overflow-y: auto;
            }
            .stats-card {
                transition: transform 0.2s;
            }
            .stats-card:hover {
                transform: translateY(-2px);
            }
        </style>
    <?php $__env->stopPush(); ?>

    <div class="card p-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Dashboard Stats Row -->
        <div class="row mb-3">
            <div class="col-md-2">
                <div class="card bg-primary text-white stats-card">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-calendar"></i> Today: <span id="todayTotal">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white stats-card">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-time"></i> Pending: <span id="todayPending">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white stats-card">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-loader"></i> Progress: <span id="todayProgress">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white stats-card">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-check"></i> Done: <span id="todayCompleted">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white stats-card">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-error"></i> Overdue: <span id="overdueCount">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white stats-card">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-up-arrow-alt"></i> Priority: <span id="highPriority">0</span></h6>
                    </div>
                </div>
            </div>
        </div>

        <table id="tasksTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Room</th>
                    <th>Task Type</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Schedule</th>
                    <th>Duration</th>
                    <th>Flags</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Task Modal -->
    <div class="modal fade custom-modal" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="taskModalLabel">Add Housekeeping Task</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Property -->
                        <div class="col-md-6">
                            <label>Property <span class="text-danger">*</span></label>
                            <select id="taskProperty" class="form-control">
                                <option value="">Select Property</option>
                                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger d-none" id="taskPropertyError">Property is required</small>
                        </div>

                        <!-- Room -->
                        <div class="col-md-6">
                            <label>Room <span class="text-danger">*</span></label>
                            <select id="taskRoom" class="form-control">
                                <option value="">Select Room</option>
                            </select>
                            <small class="text-danger d-none" id="taskRoomError">Room is required</small>
                        </div>

                        <!-- Task Type -->
                        <div class="col-md-6">
                            <label>Task Type <span class="text-danger">*</span></label>
                            <select id="taskType" class="form-control">
                                <option value="">Select Task Type</option>
                                <?php $__currentLoopData = $taskTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger d-none" id="taskTypeError">Task type is required</small>
                        </div>

                        <!-- Assigned To -->
                        <div class="col-md-6">
                            <label>Assign To Staff</label>
                            <select id="assignedTo" class="form-control">
                                <option value="">Unassigned</option>
                                <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s->id); ?>"><?php echo e($s->full_name); ?> (<?php echo e(ucfirst($s->shift)); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Leave empty to assign later</small>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <label>Priority <span class="text-danger">*</span></label>
                            <select id="taskPriority" class="form-control">
                                <?php $__currentLoopData = $priorityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e($key === 'normal' ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Scheduled Date -->
                        <div class="col-md-4">
                            <label>Scheduled Date <span class="text-danger">*</span></label>
                            <input type="date" id="scheduledDate" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                            <small class="text-danger d-none" id="scheduledDateError">Date is required</small>
                        </div>

                        <!-- Scheduled Time -->
                        <div class="col-md-4">
                            <label>Scheduled Time</label>
                            <input type="time" id="scheduledTime" class="form-control">
                            <small class="text-muted">Optional: Leave empty for any time</small>
                        </div>

                        <!-- Estimated Duration -->
                        <div class="col-md-4">
                            <label>Duration (minutes)</label>
                            <input type="number" id="estimatedDuration" class="form-control" placeholder="30" min="1" max="480" value="30">
                        </div>

                        <!-- Room Status Flags -->
                        <div class="col-12">
                            <label class="d-block mb-2">Room Status</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="isOccupied"> Room is Occupied
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="guestPresent"> Guest Present
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="doNotDisturb"> Do Not Disturb
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Special Instructions -->
                        <div class="col-12">
                            <label>Special Instructions</label>
                            <textarea id="specialInstructions" class="form-control" rows="3" placeholder="Any special instructions or notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitTaskBtn" class="btn btn-primary">Save Task</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspect Task Modal -->
    <div class="modal fade" id="inspectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Inspect Task</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="inspectTaskId">
                    
                    <div class="mb-3">
                        <label>Room: <strong id="inspectRoomNumber"></strong></label>
                        <div class="text-muted">Staff: <span id="inspectStaffName"></span></div>
                    </div>

                    <div class="mb-3">
                        <label>Quality Rating <span class="text-danger">*</span></label>
                        <div class="rating-stars">
                            <i class="bx bx-star star-rating" data-rating="1"></i>
                            <i class="bx bx-star star-rating" data-rating="2"></i>
                            <i class="bx bx-star star-rating" data-rating="3"></i>
                            <i class="bx bx-star star-rating" data-rating="4"></i>
                            <i class="bx bx-star star-rating" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="qualityRating" value="0">
                    </div>

                    <div class="mb-3">
                        <label>Action <span class="text-danger">*</span></label>
                        <select id="inspectionAction" class="form-control">
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Inspection Notes</label>
                        <textarea id="inspectionNotes" class="form-control" rows="3" placeholder="Quality check notes..."></textarea>
                    </div>

                    <div class="mb-3 d-none" id="rejectionReasonWrapper">
                        <label>Rejection Reason <span class="text-danger">*</span></label>
                        <textarea id="rejectionReason" class="form-control" rows="2" placeholder="Why is this task rejected?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitInspectionBtn" class="btn btn-primary">Submit Inspection</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Task Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Loading details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <?php if(session('notify')): ?>
            <script>
                alert_box('success', "<?php echo e(session('notify')); ?>");
            </script>
        <?php endif; ?>

        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#tasksTable",
                    ajaxUrl: "<?php echo e(route('housekeeping.ajax')); ?>",
                    moduleName: "Add Task",
                    modalSelector: "#taskModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'room_display', filter: 'none' },
                        { data: 'task_type_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "checkout-cleaning", label: "Checkout Cleaning" },
                                { value: "daily-cleaning", label: "Daily Cleaning" },
                                { value: "deep-cleaning", label: "Deep Cleaning" },
                                { value: "turndown-service", label: "Turndown Service" },
                                { value: "maintenance-cleaning", label: "Maintenance" },
                                { value: "inspection", label: "Inspection" }
                            ]
                        },
                        { data: 'staff_display', filter: 'none' },
                        { data: 'status_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "pending", label: "Pending" },
                                { value: "assigned", label: "Assigned" },
                                { value: "in-progress", label: "In Progress" },
                                { value: "completed", label: "Completed" },
                                { value: "inspected", label: "Inspected" },
                                { value: "rejected", label: "Rejected" },
                                { value: "cancelled", label: "Cancelled" }
                            ]
                        },
                        { data: 'priority_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "low", label: "Low" },
                                { value: "normal", label: "Normal" },
                                { value: "high", label: "High" },
                                { value: "urgent", label: "Urgent" }
                            ]
                        },
                        { data: 'schedule_display', filter: 'none' },
                        { data: 'duration_display', filter: 'none' },
                        { data: 'flags_display', filter: 'none' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "70px", className: "text-center" },
                        { targets: [3, 5, 6, 7, 8, 9], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ],
                    drawCallback: function() {
                        updateDashboardStats();
                    }
                });

                // Load rooms when property changes
                $('#taskProperty').on('change', function() {
                    let propertyId = $(this).val();
                    $('#taskRoom').html('<option value="">Loading rooms...</option>');
                    
                    if (!propertyId) {
                        $('#taskRoom').html('<option value="">Select Room</option>');
                        return;
                    }

                    $.get("<?php echo e(route('housekeeping.rooms-by-property')); ?>", { property_id: propertyId }, function(res) {
                        let options = '<option value="">Select Room</option>';
                        res.data.forEach(function(room) {
                            let status = room.housekeeping_status ? ' - ' + room.housekeeping_status : '';
                            options += `<option value="${room.id}">${room.room_number}${status}</option>`;
                        });
                        $('#taskRoom').html(options);
                    });
                });

                // Update dashboard stats
                function updateDashboardStats() {
                    $.get("<?php echo e(route('housekeeping.dashboard-stats')); ?>", function(res) {
                        $('#todayTotal').text(res.data.today_total);
                        $('#todayPending').text(res.data.today_pending);
                        $('#todayProgress').text(res.data.today_in_progress);
                        $('#todayCompleted').text(res.data.today_completed);
                        $('#overdueCount').text(res.data.overdue);
                        $('#highPriority').text(res.data.high_priority);
                    });
                }

                updateDashboardStats();
                setInterval(updateDashboardStats, 60000); // Refresh every minute
            });
        </script>

        <!-- Submit Task -->
        <script>
            $('#submitTaskBtn').on('click', function () {
                let id = $('#taskModal').data('id');
                
                $('.text-danger').addClass('d-none');

                let propertyId = $('#taskProperty').val();
                let roomId = $('#taskRoom').val();
                let taskType = $('#taskType').val();
                let assignedTo = $('#assignedTo').val();
                let priority = $('#taskPriority').val();
                let scheduledDate = $('#scheduledDate').val();
                let scheduledTime = $('#scheduledTime').val();
                let estimatedDuration = $('#estimatedDuration').val();
                let isOccupied = $('#isOccupied').is(':checked') ? 1 : 0;
                let guestPresent = $('#guestPresent').is(':checked') ? 1 : 0;
                let doNotDisturb = $('#doNotDisturb').is(':checked') ? 1 : 0;
                let specialInstructions = $('#specialInstructions').val().trim();

                // Validation
                let isValid = true;
                if (!propertyId) {
                    $('#taskPropertyError').removeClass('d-none');
                    isValid = false;
                }
                if (!roomId) {
                    $('#taskRoomError').removeClass('d-none');
                    isValid = false;
                }
                if (!taskType) {
                    $('#taskTypeError').removeClass('d-none');
                    isValid = false;
                }
                if (!scheduledDate) {
                    $('#scheduledDateError').removeClass('d-none');
                    isValid = false;
                }

                if (!isValid) {
                    error_noti('Please fill all required fields');
                    return;
                }

                let payload = {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: id ? 'PUT' : 'POST',
                    property_id: propertyId,
                    room_id: roomId,
                    task_type: taskType,
                    assigned_to: assignedTo || null,
                    priority: priority,
                    scheduled_date: scheduledDate,
                    scheduled_time: scheduledTime || null,
                    estimated_duration_minutes: estimatedDuration || 30,
                    is_occupied: isOccupied,
                    guest_present: guestPresent,
                    do_not_disturb: doNotDisturb,
                    special_instructions: specialInstructions || null
                };

                let url = id
                    ? "<?php echo e(route('housekeeping.update', ':id')); ?>".replace(':id', id)
                    : "<?php echo e(route('housekeeping.store')); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#taskModal').modal('hide');
                        resetTaskModal();
                        $('#tasksTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save task';
                        if (xhr.responseJSON?.errors) {
                            let errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- Edit Task -->
        <script>
            $(document).on('click', '.edit-task', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('housekeeping.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#taskProperty').val(data.property_id).trigger('change');
                        
                        setTimeout(function() {
                            $('#taskRoom').val(data.room_id);
                        }, 300);

                        $('#taskType').val(data.task_type);
                        $('#assignedTo').val(data.assigned_to || '');
                        $('#taskPriority').val(data.priority);
                        $('#scheduledDate').val(data.scheduled_date_display);
                        $('#scheduledTime').val(data.scheduled_time_display || '');
                        $('#estimatedDuration').val(data.estimated_duration_minutes);
                        $('#isOccupied').prop('checked', data.is_occupied);
                        $('#guestPresent').prop('checked', data.guest_present);
                        $('#doNotDisturb').prop('checked', data.do_not_disturb);
                        $('#specialInstructions').val(data.special_instructions || '');

                        $('#taskModal')
                            .data('id', data.id)
                            .modal('show');

                        $('#taskModalLabel').text('Edit Task');
                    },
                    error: function () {
                        error_noti('Unable to load task details');
                    }
                });
            });
        </script>

        <!-- Start Task -->
        <script>
            $(document).on('click', '.start-task', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Start this task?',
                    text: "The task status will be updated to In Progress",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, start it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "<?php echo e(route('housekeeping.start-task')); ?>",
                        type: "POST",
                        data: {
                            _token: "<?php echo e(csrf_token()); ?>",
                            task_id: id
                        },
                        success: function (res) {
                            success_noti(res.message);
                            $('#tasksTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message ?? 'Failed to start task');
                        }
                    });
                });
            });
        </script>

        <!-- Complete Task -->
        <script>
            $(document).on('click', '.complete-task', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Complete this task?',
                    input: 'textarea',
                    inputLabel: 'Staff Notes (Optional)',
                    inputPlaceholder: 'Any notes or observations...',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, complete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "<?php echo e(route('housekeeping.complete-task')); ?>",
                        type: "POST",
                        data: {
                            _token: "<?php echo e(csrf_token()); ?>",
                            task_id: id,
                            staff_notes: result.value || null
                        },
                        success: function (res) {
                            success_noti(res.message);
                            $('#tasksTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message ?? 'Failed to complete task');
                        }
                    });
                });
            });
        </script>

        <!-- Inspect Task -->
        <script>
            // Star rating functionality
            $(document).on('click', '.star-rating', function() {
                let rating = $(this).data('rating');
                $('#qualityRating').val(rating);
                
                $('.star-rating').each(function() {
                    if ($(this).data('rating') <= rating) {
                        $(this).removeClass('bx-star').addClass('bxs-star text-warning');
                    } else {
                        $(this).removeClass('bxs-star text-warning').addClass('bx-star');
                    }
                });
            });

            // Show/hide rejection reason
            $('#inspectionAction').on('change', function() {
                if ($(this).val() === 'reject') {
                    $('#rejectionReasonWrapper').removeClass('d-none');
                } else {
                    $('#rejectionReasonWrapper').addClass('d-none');
                }
            });

            $(document).on('click', '.inspect-task', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('housekeeping.show', ':id')); ?>".replace(':id', id);
                
                $.get(url, function(data) {
                    $('#inspectTaskId').val(data.id);
                    $('#inspectRoomNumber').text(data.room.room_number);
                    $('#inspectStaffName').text(data.staff ? data.staff.full_name : 'N/A');
                    $('#qualityRating').val(0);
                    $('#inspectionAction').val('approve');
                    $('#inspectionNotes').val('');
                    $('#rejectionReason').val('');
                    $('#rejectionReasonWrapper').addClass('d-none');
                    
                    $('.star-rating').removeClass('bxs-star text-warning').addClass('bx-star');
                    
                    $('#inspectModal').modal('show');
                });
            });

            $('#submitInspectionBtn').on('click', function() {
                let taskId = $('#inspectTaskId').val();
                let rating = $('#qualityRating').val();
                let action = $('#inspectionAction').val();
                let notes = $('#inspectionNotes').val().trim();
                let rejectionReason = $('#rejectionReason').val().trim();

                if (rating == 0) {
                    error_noti('Please select a quality rating');
                    return;
                }

                if (action === 'reject' && !rejectionReason) {
                    error_noti('Please provide rejection reason');
                    return;
                }

                $.ajax({
                    url: "<?php echo e(route('housekeeping.inspect-task')); ?>",
                    type: "POST",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
                        task_id: taskId,
                        quality_rating: rating,
                        action: action,
                        inspection_notes: notes || null,
                        rejection_reason: rejectionReason || null
                    },
                    success: function (res) {
                        success_noti(res.message);
                        $('#inspectModal').modal('hide');
                        $('#tasksTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to inspect task');
                    }
                });
            });
        </script>

        <!-- View Details -->
        <script>
            $(document).on('click', '.view-details', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('housekeeping.show', ':id')); ?>".replace(':id', id);
                
                $('#detailsContent').html('<div class="text-center py-4"><div class="spinner-border"></div><p class="mt-2">Loading...</p></div>');
                $('#detailsModal').modal('show');

                $.get(url, function(data) {
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Property:</strong> ${data.property.name}</p>
                                <p><strong>Room:</strong> ${data.room.room_number}</p>
                                <p><strong>Task Type:</strong> ${data.task_type.replace('-', ' ')}</p>
                                <p><strong>Status:</strong> <span class="badge bg-primary">${data.status}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Assigned To:</strong> ${data.staff ? data.staff.full_name : 'Unassigned'}</p>
                                <p><strong>Priority:</strong> ${data.priority}</p>
                                <p><strong>Scheduled:</strong> ${data.scheduled_date_display} ${data.scheduled_time_display || ''}</p>
                                <p><strong>Duration:</strong> ${data.estimated_duration_minutes} min</p>
                            </div>
                        </div>
                        ${data.special_instructions ? '<p><strong>Instructions:</strong><br>' + data.special_instructions + '</p>' : ''}
                        ${data.staff_notes ? '<p><strong>Staff Notes:</strong><br>' + data.staff_notes + '</p>' : ''}
                        ${data.inspection_notes ? '<p><strong>Inspection Notes:</strong><br>' + data.inspection_notes + '</p>' : ''}
                        ${data.quality_rating ? '<p><strong>Quality Rating:</strong> ' + data.quality_rating + '/5 ⭐</p>' : ''}
                    `;
                    $('#detailsContent').html(html);
                });
            });
        </script>

        <!-- Delete Task -->
        <script>
            $(document).on('click', '.delete-task', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This task will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "<?php echo e(route('housekeeping.destroy', ':id')); ?>".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "<?php echo e(csrf_token()); ?>",
                            _method: "DELETE"
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#tasksTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: xhr.responseJSON?.message ?? 'Failed to delete task'
                            });
                        }
                    });
                });
            });
        </script>

        <!-- Reset Modal -->
        <script>
            function resetTaskModal() {
                $('#taskProperty').val('');
                $('#taskRoom').html('<option value="">Select Room</option>');
                $('#taskType').val('');
                $('#assignedTo').val('');
                $('#taskPriority').val('normal');
                $('#scheduledDate').val('<?php echo e(date("Y-m-d")); ?>');
                $('#scheduledTime').val('');
                $('#estimatedDuration').val('30');
                $('#isOccupied').prop('checked', false);
                $('#guestPresent').prop('checked', false);
                $('#doNotDisturb').prop('checked', false);
                $('#specialInstructions').val('');
                
                $('.text-danger').addClass('d-none');
                $('#taskModal').removeData('id');
                $('#taskModalLabel').text('Add Housekeeping Task');
            }

            $('#taskModal').on('hidden.bs.modal', resetTaskModal);
        </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/housekeeping/index.blade.php ENDPATH**/ ?>