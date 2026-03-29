<x-app-layout>

    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="log_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>User name</th>
                    <th>Module</th>
                    <th>Type</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>


    <div id="view_json_detail" class="modal">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><strong>Detail</strong> </h5>
                    <button aria-hidden="true" class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="panel-body" style="overflow: auto;max-height: 400px">
                        <!-- <div class="text-end">
                            <p><strong>Status:-</strong> <b>0</b>- Active , <b>1</b>- In-Active, <b>2</b>- Delete</p>
                        </div> -->
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>User Id</strong>
                                    </td>
                                    <td id="user_id"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>User Name</strong>
                                    </td>
                                    <td id="user_detail"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Module</strong>
                                    </td>
                                    <td id="area_detail"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Action</strong>
                                    </td>
                                    <td id="activity_detail"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Time</strong>
                                    </td>
                                    <td id="time_detail"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>IP</strong>
                                    </td>
                                    <td id="ip_detail"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>MAC</strong>
                                    </td>
                                    <td id="mac_address"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Browser Details</strong>
                                    </td>
                                    <td id="device_detail"></td>
                                </tr>
                                <!-- <tr>
                                    <td>
                                        <strong>OS Details</strong>
                                    </td>
                                    <td id="os_detail"></td>
                                </tr> -->
                                <tr>
                                    <td>
                                        <strong>Session ID</strong>
                                    </td>
                                    <td id="session_id"></td>
                                </tr>
                                <!-- <tr>
                                    <td>
                                        <strong>Previous State</strong>
                                    </td>
                                    <td>
                                        <pre id="json_request_previous_state"></pre>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td>
                                        <strong>New State</strong>
                                    </td>
                                    <td>
                                        <pre id="json_request_parameter"></pre>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            $(function () {
                let controllerOptions = @json($log_dropdown);
                var table = $('#log_list').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: "{{ route('master-logs.get_log') }}",
                    columns: [
                        // { data: 'id', name: 'id', orderable: false, searchable: false },
                        { data: null, name: 'id',  orderable: false, searchable: false,
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        { data: 'user_name', name: 'user_name' },
                        { data: 'controller', name: 'module' },
                        { data: 'method', name: 'module' },
                        { data: 'updated_at', name: 'updated_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    columnDefs: [
                        { targets: [0, -1], className: 'text-center' },
                        { targets: 0, width: "80px" },
                        { targets: -1, width: "100px" }
                    ],
                    dom: "<'d-flex justify-content-end'B>rtip",
                    buttons: [
                        {
                            text: 'Filter',
                            className: 'btn btn-outline-secondary',
                            action: function (e, dt, node) {
                                let $btn = $(node);

                                // toggle filter row
                                if ($("#log_list thead tr.filter-row").length) {
                                    $("#log_list thead tr.filter-row").toggle();
                                    $btn.toggleClass("btn-secondary active btn-outline-secondary");
                                    return;
                                }

                                // add filter row
                                let filterRow = $('<tr class="filter-row"></tr>');
                                $('#log_list thead tr th').each(function () {
                                    let title = $(this).text();
                                    if (title === "Action" || title === "Sr. No.") {
                                        filterRow.append('<th></th>');
                                    } else if (title === "Last Modified") {
                                        filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Select Date Range" id="dateFilter" autocomplete="off" /></th>');
                                    } else if (title.toLowerCase() === "module") {
                                        // Dropdown filter for controller
                                        let dropdown = '<select class="form-control form-control-sm controller-filter">' +
                                            '<option value="">All</option>';
                                        $.each(controllerOptions, function (key, value) {
                                            dropdown += `<option value="${key}">${value}</option>`;
                                        });
                                        dropdown += '</select>';
                                        filterRow.append('<th>' + dropdown + '</th>');
                                    } else {
                                        filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" /></th>');
                                    }
                                });
                                $('#log_list thead').append(filterRow);
                                // Apply controller filter
                                $(document).on('change', '.controller-filter', function () {
                                    table.column(2).search(this.value).draw(); // 2 = Module column index
                                });

                                $btn.removeClass("btn-outline-secondary").addClass("btn-secondary active");

                                // Initialize date range picker for dynamic input
                                $('#dateFilter').daterangepicker({
                                    autoUpdateInput: false,
                                    opens: 'right',
                                    startDate: moment('1970-01-01'),
                                    endDate: moment(),
                                    locale: { format: 'YYYY-MM-DD' },
                                    ranges: {
                                        'All Time': [moment('1970-01-01'), moment()],
                                        'Today': [moment(), moment()],
                                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                        'This Week': [moment().startOf('week'), moment().endOf('week')],
                                        'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                                        'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                                    }
                                });

                                // Apply / cancel events
                                $('#dateFilter').on('apply.daterangepicker', function (ev, picker) {
                                    if (picker.chosenLabel === 'All Time') {
                                        $(this).val('');
                                        table.column(3).search('').draw();
                                    } else {
                                        let val = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
                                        $(this).val(val);
                                        table.column(3).search(val).draw();
                                    }
                                });

                                $('#dateFilter').on('cancel.daterangepicker', function (ev, picker) {
                                    $(this).val('');
                                    table.column(3).search('').draw();
                                });

                                // Search on Enter for text filters
                                table.columns().every(function (index) {
                                    $('input', $('.filter-row th').eq(index)).on('keypress', function (e) {
                                        if (e.which === 13) {
                                            table.column(index).search(this.value).draw();
                                        }
                                    });
                                });
                            }
                        }
                    ]
                });
            });

            $(document).on('click', '.view_logs', function () {
                let id = $(this).attr('data-id');
                jQuery.ajax({
                    url: "{{ route('master-logs.get_user_log') }}",
                    method: 'GET',
                    dataType: 'json',
                    async: false,
                    data: {
                        "id": id
                    },
                    success: function (data) {
                        // console.log(data);
                        var response = data[0];
                        $("#view_json_detail").modal("show");

                        // entity_name = JSON.stringify(response.request_data, null, '\t');
                        entity_name = response.request_data;
                        let controller = response.module || '';
                        let parts = controller.split('@');
                        let modules = parts[0] || '';
                        let action = parts[1] || '';

                        $('#user_id').html(response.user_id);
                        $('#user_detail').html(response.user_name);
                        $('#area_detail').html(modules);
                        $('#activity_detail').html(action);
                        $('#time_detail').html(response.updated_at);
                        $('#ip_detail').html(response.ip_address);
                        $('#mac_address').html(response.user_agent);
                        $('#device_detail').html(response.machine_id);
                        // $('#os_detail').html(response.machine_id);
                        $('#session_id').html(response.session_id);
                        $('#json_request_parameter').html(entity_name);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>