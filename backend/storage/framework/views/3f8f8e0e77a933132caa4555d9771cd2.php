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
            .feature-icon {
                font-size: 1.2rem;
                margin: 0 2px;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <div class="card p-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <table id="roomsTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Room Number</th>
                    <th>Type</th>
                    <th>Floor</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Housekeeping</th>
                    <th>Features</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Room Modal -->
    <div class="modal fade custom-modal" id="roomModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="roomModalLabel">Add Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Property -->
                        <div class="col-md-6">
                            <label>Property <span class="text-danger">*</span></label>
                            <select id="roomProperty" class="form-control">
                                <option value="">Select Property</option>
                                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger d-none" id="roomPropertyError">Property is required</small>
                        </div>

                        <!-- Room Number -->
                        <div class="col-md-6">
                            <label>Room Number <span class="text-danger">*</span></label>
                            <input type="text" id="roomNumber" class="form-control" placeholder="101, A-201, etc.">
                            <small class="text-danger d-none" id="roomNumberError">Room number is required</small>
                        </div>

                        <!-- Room Type -->
                        <div class="col-md-6">
                            <label>Room Type <span class="text-danger">*</span></label>
                            <select id="roomType" class="form-control">
                                <option value="">Select Room Type</option>
                                <?php $__currentLoopData = $roomTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($rt->id); ?>"><?php echo e($rt->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger d-none" id="roomTypeError">Room type is required</small>
                        </div>

                        <!-- Floor -->
                        <div class="col-md-6">
                            <label>Floor</label>
                            <input type="number" id="roomFloor" class="form-control" placeholder="1, 2, 3..." min="0">
                        </div>

                        <!-- Block -->
                        <div class="col-md-6">
                            <label>Block/Building</label>
                            <input type="text" id="roomBlock" class="form-control" placeholder="Block A, Main Building">
                        </div>

                        <!-- Wing -->
                        <div class="col-md-6">
                            <label>Wing/Section</label>
                            <input type="text" id="roomWing" class="form-control" placeholder="East Wing, North Section">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label>Room Status <span class="text-danger">*</span></label>
                            <select id="roomStatus" class="form-control">
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Housekeeping Status -->
                        <div class="col-md-6">
                            <label>Housekeeping Status <span class="text-danger">*</span></label>
                            <select id="housekeepingStatus" class="form-control">
                                <?php $__currentLoopData = $housekeepingOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Price Override -->
                        <div class="col-md-6">
                            <label>Price Override (₹)</label>
                            <input type="number" id="priceOverride" class="form-control" placeholder="Leave empty to use default rate" step="0.01" min="0">
                            <small class="text-muted">Override the default room type rate</small>
                        </div>

                        <!-- Features Row -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="isSmoking"> Smoking Allowed
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="isAccessible"> Accessible Room
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="isConnecting"> Has Connecting Door
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Connecting Room -->
                        <div class="col-md-6 d-none" id="connectingRoomWrapper">
                            <label>Connecting Room</label>
                            <select id="connectingRoom" class="form-control">
                                <option value="">Select Room</option>
                            </select>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-6">
                            <label class="d-block">
                                <input type="checkbox" id="isActive" checked> Active
                            </label>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label>Notes</label>
                            <textarea id="roomNotes" class="form-control" rows="3" placeholder="Maintenance notes, special instructions..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitRoomBtn" class="btn btn-primary">Save Room</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
                    selector: "#roomsTable",
                    ajaxUrl: "<?php echo e(route('rooms.ajax')); ?>",
                    moduleName: "Add Room",
                    modalSelector: "#roomModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'room_number', filter: 'text' },
                        { data: 'roomType.name', filter: 'text' },
                        { data: 'floor_display', filter: 'none' },
                        { data: 'rate_display', filter: 'none' }, 
                        {
                            data: 'status_badge', filter: 'select', options: [
                                { value: "", label: "All" },                                
                                { value: "available", label: "Available" },
                                { value: "occupied", label: "Occupied" },
                                { value: "reserved", label: "Reserved" },
                                { value: "maintenance", label: "Maintenance" },
                                { value: "out-of-order", label: "Out of Order" },
                                { value: "blocked", label: "Blocked" }
                            ]
                        },
                        { data: 'housekeeping_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "clean", label: "Clean" },
                                { value: "dirty", label: "Dirty" },
                                { value: "maintenance", label: "Maintenance" }
                            ]
                        },                                                                       
                        { data: 'features_display', filter: 'none' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: [4, 5, 6, 7, 8], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });

                // Show/hide connecting room field
                $('#isConnecting').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#connectingRoomWrapper').removeClass('d-none');
                        loadConnectingRooms();
                    } else {
                        $('#connectingRoomWrapper').addClass('d-none');
                    }
                });

                // Load connecting rooms based on property
                function loadConnectingRooms() {
                    let propertyId = $('#roomProperty').val();
                    let currentRoomId = $('#roomModal').data('id');
                    
                    if (!propertyId) return;

                    // In real implementation, fetch via AJAX
                    // For now, this is a placeholder
                }
            });
        </script>

        <!-- Submit Room -->
        <script>
            $('#submitRoomBtn').on('click', function () {
                let id = $('#roomModal').data('id');
                
                // Clear previous errors
                $('.text-danger').addClass('d-none');

                // Get values
                let propertyId = $('#roomProperty').val();
                let roomNumber = $('#roomNumber').val().trim();
                let roomTypeId = $('#roomType').val();
                let floor = $('#roomFloor').val();
                let block = $('#roomBlock').val().trim();
                let wing = $('#roomWing').val().trim();
                let status = $('#roomStatus').val();
                let housekeepingStatus = $('#housekeepingStatus').val();
                let priceOverride = $('#priceOverride').val();
                let isSmoking = $('#isSmoking').is(':checked') ? 1 : 0;
                let isAccessible = $('#isAccessible').is(':checked') ? 1 : 0;
                let isConnecting = $('#isConnecting').is(':checked') ? 1 : 0;
                let connectingRoomId = $('#connectingRoom').val();
                let isActive = $('#isActive').is(':checked') ? 1 : 0;
                let notes = $('#roomNotes').val().trim();

                // Frontend Validation
                let isValid = true;
                if (!propertyId) {
                    $('#roomPropertyError').removeClass('d-none');
                    isValid = false;
                }
                if (!roomNumber) {
                    $('#roomNumberError').removeClass('d-none');
                    isValid = false;
                }
                if (!roomTypeId) {
                    $('#roomTypeError').removeClass('d-none');
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
                    room_number: roomNumber,
                    room_type_id: roomTypeId,
                    floor: floor || null,
                    block: block || null,
                    wing: wing || null,
                    status: status,
                    housekeeping_status: housekeepingStatus,
                    price_override: priceOverride || null,
                    is_smoking: isSmoking,
                    is_accessible: isAccessible,
                    is_connecting: isConnecting,
                    connecting_room_id: connectingRoomId || null,
                    is_active: isActive,
                    notes: notes || null
                };

                let url = id
                    ? "<?php echo e(route('rooms.update', ':id')); ?>".replace(':id', id)
                    : "<?php echo e(route('rooms.store')); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#roomModal').modal('hide');
                        resetRoomModal();
                        $('#roomsTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save room';
                        if (xhr.responseJSON?.errors) {
                            let errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- Edit Room -->
        <script>
            $(document).on('click', '.edit-room', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('rooms.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#roomProperty').val(data.property_id);
                        $('#roomNumber').val(data.room_number);
                        $('#roomType').val(data.room_type_id);
                        $('#roomFloor').val(data.floor || '');
                        $('#roomBlock').val(data.block || '');
                        $('#roomWing').val(data.wing || '');
                        $('#roomStatus').val(data.status);
                        $('#housekeepingStatus').val(data.housekeeping_status);
                        $('#priceOverride').val(data.price_override_display || '');
                        $('#isSmoking').prop('checked', data.is_smoking);
                        $('#isAccessible').prop('checked', data.is_accessible);
                        $('#isConnecting').prop('checked', data.is_connecting);
                        $('#connectingRoom').val(data.connecting_room_id || '');
                        $('#isActive').prop('checked', data.is_active);
                        $('#roomNotes').val(data.notes || '');

                        if (data.is_connecting) {
                            $('#connectingRoomWrapper').removeClass('d-none');
                        }

                        $('#roomModal')
                            .data('id', data.id)
                            .modal('show');

                        $('#roomModalLabel').text('Edit Room');
                    },
                    error: function () {
                        error_noti('Unable to load room details');
                    }
                });
            });
        </script>

        <!-- Delete Room -->
        <script>
            $(document).on('click', '.delete-room', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This room will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "<?php echo e(route('rooms.destroy', ':id')); ?>".replace(':id', id);
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
                            $('#roomsTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: xhr.responseJSON?.message ?? 'Failed to delete room'
                            });
                        }
                    });
                });
            });
        </script>

        <!-- Reset Modal -->
        <script>
            function resetRoomModal() {
                $('#roomProperty').val('');
                $('#roomNumber').val('');
                $('#roomType').val('');
                $('#roomFloor').val('');
                $('#roomBlock').val('');
                $('#roomWing').val('');
                $('#roomStatus').val('available');
                $('#housekeepingStatus').val('clean');
                $('#priceOverride').val('');
                $('#isSmoking').prop('checked', false);
                $('#isAccessible').prop('checked', false);
                $('#isConnecting').prop('checked', false);
                $('#connectingRoom').val('');
                $('#isActive').prop('checked', true);
                $('#roomNotes').val('');
                $('#connectingRoomWrapper').addClass('d-none');
                
                $('.text-danger').addClass('d-none');
                $('#roomModal').removeData('id');
                $('#roomModalLabel').text('Add Room');
            }

            $('#roomModal').on('hidden.bs.modal', resetRoomModal);
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
<?php endif; ?><?php /**PATH /var/www/html/wavestube/resources/views/rooms/index.blade.php ENDPATH**/ ?>