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
        </style>
    <?php $__env->stopPush(); ?>

    <div class="card p-4">
        <table id="roomTypesTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Default Rate</th>
                    <th>Occupancy</th>
                    <th>Rooms</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Room Type Modal -->
    <div class="modal fade custom-modal" id="roomTypeModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="roomTypeModalLabel">Add Room Type</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Property</label>
                            <select id="typeProperty" class="form-control">
                                <option value="">Global Type</option>
                                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" id="typeName" class="form-control" placeholder="Deluxe Room, Suite...">
                            <small class="text-danger d-none" id="typeNameError">Name is required</small>
                        </div>

                        <div class="col-md-6">
                            <label>Code</label>
                            <input type="text" id="typeCode" class="form-control" placeholder="DLX, STE...">
                        </div>

                        <div class="col-md-6">
                            <label>Default Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" id="defaultRate" class="form-control" step="0.01" min="0" placeholder="5000.00">
                            <small class="text-danger d-none" id="defaultRateError">Rate is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Max Occupancy <span class="text-danger">*</span></label>
                            <input type="number" id="maxOccupancy" class="form-control" min="1" max="20" value="2">
                        </div>

                        <div class="col-md-4">
                            <label>Max Adults <span class="text-danger">*</span></label>
                            <input type="number" id="maxAdults" class="form-control" min="1" max="20" value="2">
                        </div>

                        <div class="col-md-4">
                            <label>Max Children</label>
                            <input type="number" id="maxChildren" class="form-control" min="0" max="10" value="0">
                        </div>

                        <div class="col-md-4">
                            <label>Number of Beds <span class="text-danger">*</span></label>
                            <input type="number" id="beds" class="form-control" min="1" max="10" value="1">
                        </div>

                        <div class="col-md-4">
                            <label>Bed Type <span class="text-danger">*</span></label>
                            <select id="bedType" class="form-control">
                                <option value="single">Single</option>
                                <option value="double" selected>Double</option>
                                <option value="queen">Queen</option>
                                <option value="king">King</option>
                                <option value="twin">Twin</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Room Size (sqm)</label>
                            <input type="number" id="roomSize" class="form-control" step="0.01" min="0" placeholder="25.50">
                        </div>

                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="typeDescription" class="form-control" rows="3" placeholder="Room type description..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Sort Order</label>
                            <input type="number" id="sortOrder" class="form-control" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="d-block mt-4">
                                <input type="checkbox" id="isActive" checked> Active
                            </label>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h6>Room Features</h6>
                            <div class="row">
                                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-3">
                                    <label class="d-block">
                                        <input type="checkbox" class="room-type-feature" value="<?php echo e($feature->id); ?>">
                                        <?php if($feature->icon): ?> <i class="<?php echo e($feature->icon); ?>"></i> <?php endif; ?>
                                        <?php echo e($feature->name); ?>

                                    </label>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitTypeBtn" class="btn btn-primary">Save Type</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#roomTypesTable",
                    ajaxUrl: "<?php echo e(route('room-types.ajax')); ?>",
                    moduleName: "Add Room Type",
                    modalSelector: "#roomTypeModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'name', filter: 'text' },
                        { data: 'code', filter: 'text' },
                        { data: 'default_rate_display', filter: 'none' },
                        { data: 'occupancy_display', filter: 'none' },
                        { data: 'room_count', filter: 'none' },                      
                        { data: 'status', filter: 'select', options: [
                            { value: "", label: "All" },
                            { value: "active", label: "Active" },
                            { value: "inactive", label: "Inactive" }
                        ]},                                                
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: [4, 5, 6, 7], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });
            });
        </script>

        <script>
            $('#submitTypeBtn').on('click', function () {
                let id = $('#roomTypeModal').data('id');
                $('.text-danger').addClass('d-none');

                let propertyId = $('#typeProperty').val() || null;
                let name = $('#typeName').val().trim();
                let code = $('#typeCode').val().trim();
                let defaultRate = $('#defaultRate').val();
                let maxOccupancy = $('#maxOccupancy').val();
                let maxAdults = $('#maxAdults').val();
                let maxChildren = $('#maxChildren').val();
                let beds = $('#beds').val();
                let bedType = $('#bedType').val();
                let roomSize = $('#roomSize').val();
                let description = $('#typeDescription').val().trim();
                let sortOrder = $('#sortOrder').val();
                let isActive = $('#isActive').is(':checked') ? 1 : 0;

                let features = [];
                $('.room-type-feature:checked').each(function () {
                    features.push($(this).val());
                });

                // Validation
                let isValid = true;
                if (!name) {
                    $('#typeNameError').removeClass('d-none');
                    isValid = false;
                }
                if (!defaultRate || defaultRate <= 0) {
                    $('#defaultRateError').removeClass('d-none');
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
                    name: name,
                    code: code || null,
                    default_rate: defaultRate,
                    max_occupancy: maxOccupancy,
                    max_adults: maxAdults,
                    max_children: maxChildren,
                    beds: beds,
                    bed_type: bedType,
                    room_size_sqm: roomSize || null,
                    description: description || null,
                    sort_order: sortOrder,
                    is_active: isActive,
                    features: features
                };

                let url = id
                    ? "<?php echo e(route('room-types.update', ':id')); ?>".replace(':id', id)
                    : "<?php echo e(route('room-types.store')); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#roomTypeModal').modal('hide');
                        resetTypeModal();
                        $('#roomTypesTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to save room type');
                    }
                });
            });

            $(document).on('click', '.edit-room-type', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('room-types.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#typeProperty').val(data.property_id || '');
                        $('#typeName').val(data.name);
                        $('#typeCode').val(data.code || '');
                        $('#defaultRate').val(data.default_rate_display);
                        $('#maxOccupancy').val(data.max_occupancy);
                        $('#maxAdults').val(data.max_adults);
                        $('#maxChildren').val(data.max_children);
                        $('#beds').val(data.beds);
                        $('#bedType').val(data.bed_type);
                        $('#roomSize').val(data.room_size_sqm || '');
                        $('#typeDescription').val(data.description || '');
                        $('#sortOrder').val(data.sort_order);
                        $('#isActive').prop('checked', data.is_active);

                        $('.room-type-feature').prop('checked', false);
                        if (data.features) {
                            data.features.forEach(f => {
                                $('.room-type-feature[value="' + f.id + '"]').prop('checked', true);
                            });
                        }

                        $('#roomTypeModal').data('id', data.id).modal('show');
                        $('#roomTypeModalLabel').text('Edit Room Type');
                    },
                    error: function () {
                        error_noti('Unable to load room type');
                    }
                });
            });

            $(document).on('click', '.delete-room-type', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This room type will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "<?php echo e(route('room-types.destroy', ':id')); ?>".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: { _token: "<?php echo e(csrf_token()); ?>", _method: "DELETE" },
                        success: function (res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#roomTypesTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message ?? 'Failed to delete', 'error');
                        }
                    });
                });
            });

            function resetTypeModal() {
                $('#typeProperty').val('');
                $('#typeName').val('');
                $('#typeCode').val('');
                $('#defaultRate').val('');
                $('#maxOccupancy').val('2');
                $('#maxAdults').val('2');
                $('#maxChildren').val('0');
                $('#beds').val('1');
                $('#bedType').val('double');
                $('#roomSize').val('');
                $('#typeDescription').val('');
                $('#sortOrder').val('0');
                $('#isActive').prop('checked', true);
                $('.room-type-feature').prop('checked', false);
                $('.text-danger').addClass('d-none');
                $('#roomTypeModal').removeData('id');
                $('#roomTypeModalLabel').text('Add Room Type');
            }

            $('#roomTypeModal').on('hidden.bs.modal', resetTypeModal);
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
<?php endif; ?><?php /**PATH /var/www/html/wavestube/resources/views/room-types/index.blade.php ENDPATH**/ ?>