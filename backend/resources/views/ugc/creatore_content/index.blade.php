<x-app-layout>
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="language_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Content Type</th>
                    <th>User ID</th>
                    <th>Channel Name</th>
                    <th>Category</th>
                    <th>Language</th>
                    <th>Title</th>
                    <th>Data State</th>
                    <th>Job Status</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#language_list",
                    ajaxUrl: "{{ route('ugc.user_generate_content_list') }}",
                    moduleName: "Language",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, filter: 'none' },
                        {
                            data: 'content_type', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "VOD", label: "VOD" },
                                { value: "Shorts", label: "Shorts" }
                            ]
                        },
                        { data: 'user_id', filter: 'text' },
                        { data: 'region.channel_name', filter: 'none' },
                        { data: 'category.display_title', filter: 'none' },
                        { data: 'content_langauge.identifier', filter: 'none' },
                        { data: 'title', filter: 'text' },
                        {
                            data: 'data_state', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "approved", label: "Approved" },
                                { value: "in_progress", label: "In Progress" },
                                { value: "scheduled", label: "Scheduled" },
                                { value: "rejected", label: "Rejected" },
                            ]

                        },
                        { data: 'vc_status', filter: 'text' },
                        { data: 'modified_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: 1, width: "50px", className: "text-center" },
                        { targets: 7, width: "150px", className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ],
                    createdRow: function (row, data) {
                        if (data.data_state == 'approved') {
                            statusBadge = '<div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class="bx bxs-circle me-1"></i>Approved</div>';
                        } else if (data.data_state == 'in_progress') {
                            statusBadge = '<div class="badge rounded-pill text-primary bg-light-primary p-2 text-uppercase px-3"><i class="bx bxs-circle me-1"></i>In Progress</div>';
                        } else if (data.data_state == 'scheduled') {
                            statusBadge = '<div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class="bx bxs-circle me-1"></i>Scheduled</div>';
                        } else if (data.data_state == 'rejected') {
                            statusBadge = '<div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class="bx bxs-circle me-1"></i>Rejected</div>';
                        } else {
                            statusBadge = '<div class="badge rounded-pill text-secondary bg-light-secondary p-2 text-uppercase px-3"><i class="bx bxs-circle me-1"></i>N/A</div>';
                        }
                        $('td', row).eq(7).html(statusBadge);


                        vc_status = '<div class="badge rounded-pill text-info bg-light-info p-2 text-uppercase px-3"><i class="bx bxs-circle me-1"></i>' + data.vc_status + '</div>';
                        $('td', row).eq(8).html(vc_status);


                        if (data.content_type == 'VOD') {
                            typeBadge = '<h6><i class="text-warning fas fa-video"></i></h6>';
                        }
                        else {
                            typeBadge = '<h6><i class="text-danger fas fa-film"></i></h6>';
                        }
                        $('td', row).eq(1).html(typeBadge);
                    }
                });
            });
        </script>

    @endpush
</x-app-layout>