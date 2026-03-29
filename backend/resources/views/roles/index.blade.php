<x-app-layout>

    @push('style')
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                /* Adjust as needed */
                overflow-y: auto;
            }
        </style>
    @endpush
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
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
                                @foreach(config('hms_permissions') as $module => $slugs)
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                    <div class="card-header fw-bold">{{ $module }}</div>
                                    <div class="card-body">
                                        @foreach($permissions->whereIn('slug',$slugs) as $perm)
                                        <label class="d-block">
                                            <input type="checkbox" class="role-permission" value="{{ $perm->id }}">
                                            {{ $perm->name }}
                                        </label>
                                        @endforeach
                                    </div>
                                    </div>
                                    </div>
                                    @endforeach
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


    @push('scripts')
        @if(session('notify'))
            <script>
                alert_box('success', "{{ session('notify') }}");
            </script>
        @endif



        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#rolesTable",
                    ajaxUrl: "{{ route('roles.ajax') }}",
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
                    _token: "{{ csrf_token() }}",
                    _method: id ? 'PUT' : 'POST',
                    property_id: propertyId || null,
                    name: name,
                    slug: slug,
                    permissions: permissions
                };

                let url = id
                    ? "{{ route('roles.update', ':id') }}".replace(':id', id)
                    : "{{ route('roles.store') }}";

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
                let url = "{{ route('roles.show', ':id') }}";
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
                    let url = "{{ route('roles.destroy', ':id') }}".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
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

    @endpush
</x-app-layout>