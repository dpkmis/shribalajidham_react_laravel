<x-app-layout>
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="version_control_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Device Type</th>
                    <th>Version</th>
                    <th>Log URL</th>
                    <th>Force Update</th>
                    <th>Log Capture Mode</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>


    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {

                let table = $('#version_control_list').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: '{{ route("version-control.get_versions") }}',
                    columns: [
                        { data: 'platform', name: 'platform' },
                        { data: 'version', name: 'version' },
                        { data: 'log_url', name: 'log_url' },
                        { data: 'force_update', name: 'force_update' },
                        { data: 'log_capture', name: 'log_capture' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    searching: false,
                    lengthChange: false,
                    paging: false,
                    createdRow: function (row, data) {
                        // Platform text
                        let platformText = {
                            1: 'Android', 2: 'iOS', 3: 'Website', 4: 'Android TV', 5: 'Fire TV Stick',
                            6: 'Roku', 7: 'iOS TV', 8: 'LG', 9: 'Samsung', 10: 'Jio TV'
                        };
                        $('td', row).eq(0).html(platformText[data.platform] || 'N/A');

                        // Version & Log URL as text inputs
                        $('td', row).eq(1).html(`<input type="text" class="form-control form-control-sm version-input" value="${data.version}" />`);
                        $('td', row).eq(2).html(`<input type="text" class="form-control form-control-sm logurl-input" value="${data.log_url}" />`);

                        // Force Update select
                        let forceUpdateSelect = `
                            <select class="form-select form-select-sm force-update-select">
                                <option value="0" ${data.force_update == 0 ? 'selected' : ''}>No</option>
                                <option value="1" ${data.force_update == 1 ? 'selected' : ''}>Yes</option>
                            </select>
                        `;
                        $('td', row).eq(3).html(forceUpdateSelect);

                        // Log Capture Mode select
                        let logCaptureSelect = `
                            <select class="form-select form-select-sm log-capture-select">
                                <option value="0" ${data.log_capture == 0 ? 'selected' : ''}>No</option>
                                <option value="1" ${data.log_capture == 1 ? 'selected' : ''}>Yes</option>
                            </select>
                        `;
                        $('td', row).eq(4).html(logCaptureSelect);

                        // Action buttons (hidden by default, icons only)
                        $('td', row).eq(5).html(`
                            <div class="action-buttons mt-3">
                                <button class="btn btn-sm btn-transparent save-version d-none" data-id="${data.id}">
                                    <i class="fa fa-check text-success"></i>
                                </button>
                                <button class="btn btn-sm btn-transparent cancel-version d-none" data-id="${data.id}">
                                    <i class="fa fa-times text-danger"></i>
                                </button>
                            </div>
                        `);

                    }

                });

                // Detect change in any input/select inside a row
                $(document).on('input change', '.version-input, .logurl-input, .force-update-select, .log-capture-select', function () {
                    let row = $(this).closest('tr');
                    row.find('.save-version, .cancel-version').removeClass('d-none'); // show buttons
                });


                // Save button click
                $(document).on('click', '.save-version', function () {
                    let row = $(this).closest('tr');
                    let id = $(this).data('id');

                    let formData = {
                        _token: '{{ csrf_token() }}',
                        version: row.find('.version-input').val(),
                        log_url: row.find('.logurl-input').val(),
                        force_update: row.find('.force-update-select').val(),
                        log_capture: row.find('.log-capture-select').val()
                    };

                    $.ajax({
                        url: `/version-control/${id}`,
                        type: 'PUT',
                        data: formData,
                        success: function (res) {
                            success_noti(res.message || 'Saved successfully!');
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message || 'Something went wrong!');
                        }
                    });
                });

                // Cancel button click
                $(document).on('click', '.cancel-version', function () {
                    let row = $(this).closest('tr');
                    let tableRow = $('#version_control_list').DataTable().row(row).data(); // get original row data

                    // Reset fields to original data
                    row.find('.version-input').val(tableRow.version);
                    row.find('.logurl-input').val(tableRow.log_url);
                    row.find('.force-update-select').val(tableRow.force_update);
                    row.find('.log-capture-select').val(tableRow.log_capture);

                    // Hide buttons again
                    row.find('.save-version, .cancel-version').addClass('d-none');
                });


            });
        </script>
    @endpush
</x-app-layout>