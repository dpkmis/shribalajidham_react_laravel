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
        <table id="permissionTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Module</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade custom-modal" id="roleModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 id="permissionLabel">Add Permission</h5>
                </div>

                <div class="modal-body">
                    <input id="permName" class="form-control mb-2" placeholder="Permission Name">
                    <input id="permSlug" class="form-control" placeholder="module.action">
                </div>

                <div class="modal-footer">
                    <button id="savePermission" class="btn btn-primary">Save</button>
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
                $('#permissionTable').DataTable({
                    ajax: "{{ route('permissions.ajax') }}",
                    columns: [
                        { data: 'DT_RowIndex', orderable: false },
                        { data: 'module' },
                        { data: 'name' },
                        { data: 'slug' },
                        { data: 'action', orderable: false }
                    ]
                });
            });
        </script>

        <script>
            $('#savePermission').click(function () {

                let id = $('#permissionModal').data('id');

                let payload = {
                    _token: "{{ csrf_token() }}",
                    _method: id ? 'PUT' : 'POST',
                    name: $('#permName').val(),
                    slug: $('#permSlug').val()
                };

                let url = id
                    ? "{{ route('permissions.update', ':id') }}".replace(':id', id)
                    : "{{ route('permissions.store') }}";

                $.ajax({
                    url, type: 'POST', data: payload,
                    success(res) {
                        success_noti(res.message);
                        $('#permissionModal').modal('hide');
                        $('#permissionTable').DataTable().ajax.reload();
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
            $(document).on('click', '.delete-permission', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Delete permission?',
                    icon: 'warning',
                    showCancelButton: true
                }).then(r => {
                    if (!r.isConfirmed) return;

                    $.post("{{ route('permissions.destroy', ':id') }}".replace(':id', id), {
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE'
                    }, () => {
                        success_noti('Permission deleted');
                        $('#permissionTable').DataTable().ajax.reload();
                    });
                });
            });

        </script>

    @endpush
</x-app-layout>