<x-app-layout>
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="language_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Identifier</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>



    <!-- Add Language Modal -->
    <div class="modal fade" id="addLanguageModal" tabindex="-1" aria-labelledby="addLanguageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="addLanguageLabel">Add Language</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row g-3">
                        <!-- Identifier -->
                        <div class="col-12">
                            <input type="text" class="form-control" name="identifier" id="inputIdentifier"
                                placeholder="Enter Identifier">
                        </div>

                        <!-- Title + Type -->
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" class="form-control" name="title" id="inputTitle"
                                    placeholder="Enter Language title" style="flex: 0 0 60%;">
                                <select class="form-select" name="language_type" id="languageType"
                                    style="flex: 0 0 40%;">
                                    <option value="">Optional</option>
                                    <option value="default">Default</option>
                                </select>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-6 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="status"
                                    id="inputStatus">
                                <label class="form-check-label" for="inputStatus">Active</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary px-4" id="submitBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#language_list",
                    ajaxUrl: "{{ route('master-language.get_languages') }}",
                    moduleName: "Language",
                    modalSelector: "#addLanguageModal",
                    columns: [
                        { data: 'id', filter: 'none' },
                        { data: 'identifier', filter: 'text' },
                        { data: 'title', filter: 'text' },
                        {
                            data: 'type', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "default", label: "Default" },
                                { value: "other", label: "Other" }
                            ]
                        },
                        {
                            data: 'status', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "1", label: "Active" },
                                { value: "0", label: "Inactive" }
                            ]
                        },
                        { data: 'modified_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: 3, width: "150px", className: "text-center" },
                        { targets: 4, width: "150px", className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ],
                    createdRow: function (row, data) {
                        let statusBadge = data.status == 1
                            ? '<span class="badge bg-success">Active</span>'
                            : data.status == 0
                                ? '<span class="badge bg-danger">Inactive</span>'
                                : '<span class="badge bg-secondary">' + data.status + '</span>';
                        $('td', row).eq(4).html(statusBadge);

                        let typeBadge = data.type?.toLowerCase() === 'default'
                            ? '<span class="badge bg-primary">Default</span>'
                            : data.type
                                ? '<span class="badge bg-secondary">' + data.type + '</span>'
                                : '<span class="badge bg-light text-dark">N/A</span>';
                        $('td', row).eq(3).html(typeBadge);
                    }
                });
            });
        </script>




        <script>
            $('#submitBtn').click(function () {
                let id = $('#addLanguageModal').data('id'); // if editing
                let identifier = $('#inputIdentifier').val();
                let titleValue = $('#inputTitle').val();
                let languageType = $('#languageType').val();
                let status = $('#inputStatus').is(':checked') ? 1 : 0;

                if (!identifier || !titleValue) {
                    error_noti('Identifier and Title are required!');
                    return;
                }
                let title = [
                    {
                        title: titleValue,
                        language: languageType || ''  // ensure empty string if not selected
                    }
                ];
                let url = id
                    ? "/master-language/" + id // update
                    : "{{ route('master-language.store') }}"; // create
                let type = id ? "PUT" : "POST";
                $.ajax({
                    url: url,
                    type: type,
                    data: {
                        _token: "{{ csrf_token() }}",
                        identifier: identifier,
                        title: title,
                        status: status
                    },
                    success: function (response) {
                        success_noti(response.message);
                        $('#addLanguageModal').modal('hide');
                        $('#addLanguageModal').removeData('id');
                        $('#addLanguageLabel').text('Add Language');
                        $('#language_list').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Something went wrong!');
                    }
                });
            });


            $(document).on('click', '.edit-language', function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url: '/master-language/' + id,
                    type: 'GET',
                    success: function (data) {
                        console.log(data);
                        // When opening modal for editing
                        $('#inputIdentifier').val(data.identifier);
                        $('#inputTitle').val(data.title);

                        // Set the select value and trigger change for select2
                        $('#languageType').val(data.type || '').trigger('change');

                        $('#inputStatus').prop('checked', data.status == 1);
                        $('#addLanguageModal').data('id', data.id);
                        $('#addLanguageLabel').text('Edit Language');
                        $('#addLanguageModal').modal('show');
                    },
                    error: function (xhr) {
                        error_noti('Could not fetch data!');
                    }
                });
            });
            $('#languageType').select2({
                theme: "bootstrap-5",
                dropdownParent: $('#addLanguageModal'),
                allowClear: true,
            });

            // Reset modal inputs when it is closed
            $('#addLanguageModal').on('hidden.bs.modal', function () {
                // Clear all inputs
                $('#inputIdentifier').val('');
                $('#inputTitle').val('');
                $('#languageType').val('').trigger('change'); // for select2
                $('#inputStatus').prop('checked', false);

                // Reset modal label and remove any stored id
                $('#addLanguageLabel').text('Add Language');
                $(this).removeData('id');
            });


            function deleteRecord(id) {
                if (confirm("Are you sure you want to delete this record?")) {
                    $.ajax({
                        url: "/master-language/" + id,
                        type: "POST", // <-- Change DELETE to POST
                        data: {
                            _method: 'DELETE', // method spoofing for Laravel
                            _token: "{{ csrf_token() }}",
                        },
                        success: function (response) {
                            success_noti(response.message);
                            $('#language_list').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            error_noti("Error: " + xhr.responseJSON.message);
                        }
                    });
                }
            }


        </script>

        @if(!empty($openModal) && $openModal === true)
            <script>
                $(document).ready(function () {
                    $('#addLanguageModal').modal('show');
                });
            </script>
        @endif


    @endpush
</x-app-layout>