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
                /* Adjust as needed */
                overflow-y: auto;
            }
        </style>
    <?php $__env->stopPush(); ?>
    <div class="card p-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <table id="rolesTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Title</th>
                    <th>slug</th>
                    <th>Created on</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade custom-modal" id="roleModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="addLanguageLabel">Add Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="mb-3">
                            <label>Property</label>
                            <select id="roleProperty" class="form-control">
                                <option value="">Global Role</option>
                                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Role Name <span class="text-danger">*</span></label>
                            <input type="text" id="roleName" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" id="roleSlug" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Permissions</label>
                            <div class="row">
                                <?php $__currentLoopData = config('hms_permissions'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $slugs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                    <div class="card-header fw-bold"><?php echo e($module); ?></div>
                                    <div class="card-body">
                                        <?php $__currentLoopData = $permissions->whereIn('slug',$slugs); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="d-block">
                                            <input type="checkbox" class="role-permission" value="<?php echo e($perm->id); ?>">
                                            <?php echo e($perm->name); ?>

                                        </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitBtn" class="btn btn-primary">Save</button>
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
                    selector: "#rolesTable",
                    ajaxUrl: "<?php echo e(route('roles.ajax')); ?>",
                    data: {
                        property_id: 1
                    },
                    moduleName: "Add Role",
                    modalSelector: "#roleModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'name', filter: 'text' },
                        { data: 'slug', filter: 'select', filter: 'text' },
                        { data: 'created_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: 3, width: "150px", className: "text-center" },
                        { targets: 4, width: "150px", className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ],
                    createdRow: function (row, data) {
                        //     let statusBadge = data.status == 1
                        //         ? '<span class="badge bg-success">Active</span>'
                        //         : data.status == 0
                        //             ? '<span class="badge bg-danger">Inactive</span>'
                        //             : '<span class="badge bg-secondary">Deleted</span>';
                        //     $('td', row).eq(4).html(statusBadge);

                        //     let typeBadge = data.type?.toLowerCase() === 'default'
                        //         ? '<span class="badge bg-primary">Default</span>'
                        //         : data.type
                        //             ? '<span class="badge bg-secondary">' + data.type + '</span>'
                        //             : '<span class="badge bg-light text-dark">N/A</span>';
                        //     $('td', row).eq(3).html(typeBadge);
                    }
                });
            });
        </script>

        <script>
            $('#submitBtn').on('click', function () {
                let id = $('#roleModal').data('id');
                let propertyId = $('#roleProperty').val();
                let name = $('#roleName').val().trim();
                let slug = $('#roleSlug').val().trim();
                let permissions = [];
                $('.role-permission:checked').each(function () {
                    permissions.push($(this).val());
                });
                // --------------------
                // Validation
                // --------------------
                if (!name) return error_noti('Role name is required');
                if (!slug) return error_noti('Slug is required');

                let payload = {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: id ? 'PUT' : 'POST',
                    property_id: propertyId || null,
                    name: name,
                    slug: slug,
                    permissions: permissions
                };

                let url = id
                    ? "<?php echo e(route('roles.update', ':id')); ?>".replace(':id', id)
                    : "<?php echo e(route('roles.store')); ?>";

                $.ajax({
                    url: url,
                    type: "POST", // Laravel method spoofing
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#roleModal').modal('hide');
                        resetRoleModal();
                        $('#rolesTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to save role');
                    }
                });
            });

            $(document).on('click', '.edit-role', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('roles.show', ':id')); ?>";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {

                        $('#roleProperty').val(data.property_id);
                        $('#roleName').val(data.name);
                        $('#roleSlug').val(data.slug);

                        $('.role-permission').prop('checked', false);
                        data.permissions.forEach(p => {
                            $('.role-permission[value="' + p.id + '"]').prop('checked', true);
                        });

                        $('#roleModal')
                            .data('id', data.id)
                            .modal('show');

                        $('#roleModalLabel').text('Edit Role');
                    },
                    error: function () {
                        error_noti('Unable to load role');
                    }
                });
            });

            function resetRoleModal() {
                $('#roleProperty').val('');
                $('#roleName').val('');
                $('#roleSlug').val('');
                $('.role-permission').prop('checked', false);

                $('#roleModal').removeData('id');
                $('#roleModalLabel').text('Add Role');
            }

            $('#roleModal').on('hidden.bs.modal', resetRoleModal);

        </script>
        <script>
            $(document).on('click', '.delete-role', function () {

                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This role will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    let url = "<?php echo e(route('roles.destroy', ':id')); ?>".replace(':id', id);
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

                            $('#rolesTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: xhr.responseJSON?.message ?? 'Failed to delete role'
                            });
                        }
                    });

                });
            });
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
<?php endif; ?><?php /**PATH /var/www/html/shribalajidham_react_laravel/backend/resources/views/roles/index.blade.php ENDPATH**/ ?>