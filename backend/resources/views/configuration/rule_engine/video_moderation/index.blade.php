<x-app-layout>
     @push('styles')
        <style>
            /* Container alignment */
            .d-flex {
                gap: 0.5rem;
                align-items: center;
                justify-content: center;
            }

            /* Custom hover effects */
            .btn-success {
                background: linear-gradient(135deg, #198754, #28a745);
                border: none;
                box-shadow: 0 3px 6px rgba(25, 135, 84, 0.3);
                transition: all 0.25s ease;
            }

            .btn-success:hover:not(:disabled) {
                background: linear-gradient(135deg, #157347, #1e7e34);
                transform: translateY(-2px);
            }

            .btn-success:disabled {
                background: #9ad1b2;
                box-shadow: none;
                cursor: not-allowed;
            }

            .btn-danger {
                background: linear-gradient(135deg, #dc3545, #e55353);
                border: none;
                box-shadow: 0 3px 6px rgba(220, 53, 69, 0.3);
                transition: all 0.25s ease;
            }

            .btn-danger:hover {
                background: linear-gradient(135deg, #bb2d3b, #c82333);
                transform: translateY(-2px);
            }

            /* Optional: make buttons circular (for icon-only style) */
            .btn-icon {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
            }
            .custom-padding {
                padding: 0.150rem .75rem !important;
            }
            .dwn-btn-margin{
                margin-top: 32px !important;
            }
        </style>
    @endpush
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="language_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Top Level Category L1</th>
                    <th>Second Level Category L2</th>
                    <th>Third Level Category L3</th>
                    <th>Definitions</th>
                    <th>Confidence Score</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>



    <!-- Add Language Modal -->
    <div class="modal fade custom-model" id="importDataModal" tabindex="-1" aria-labelledby="importFileLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="importFileLabel">Import File</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-10">                             
                            <form id="csvImportForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Upload CSV</label>
                                    <input type="file" name="csv_file" class="form-control" accept=".csv">
                                    <small class="text-muted">Only .csv files allowed (max 10MB)</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Import</button>
                            </form>
                        </div>
                        <div class="col-md-2">                             
                            <a download href="{{ asset('assets/custom/video_moderation.csv') }}" type="button" class="btn btn-info px-3 btn-sm dwn-btn-margin"><i class="bx bx-cloud-download mr-1"></i>Sample</a>                            
                        </div>
                        <div id="responseMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#language_list",
                    ajaxUrl: "{{ route('video-moderation.video_moderation_data') }}",
                    moduleName: "Import File",
                    modalSelector: "#importDataModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'top_level_category_L1', filter: 'text' },
                        { data: 'second_level_category_L2', filter: 'text' },
                        { data: 'third_level_category_L3', filter: 'text' },
                        { data: 'definitions', filter: 'text' },
                        { data: 'confidence_score', filter: 'text' },
                        { data: 'updated_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    createdRow: function (row, data) {
                        // Editable input for confidence_score
                        const input = `<input type="text" 
                                         class="form-control confidence-input" 
                                         value="${data.confidence_score ?? ''}" 
                                         data-id="${data.id}" 
                                         readonly />`;

                        // Update button (disabled by default)
                        const updateBtn = `<button class="btn btn-sm btn-success update-btn" 
                                              data-id="${data.id}" 
                                              disabled>Update</button>`;

                        // Put input + button in same cell (6th column)
                        $('td', row).eq(5).html(`
                        <div class="d-flex gap-2 align-items-center">
                            ${input}

                        </div>
                    `);
                    }
                });

                // Event delegation (for dynamically created elements)
                $(document).on('input', '.confidence-input', function () {
                    let id = $(this).data('id');
                    $(`.update-btn[data-id="${id}"]`).prop('disabled', false);
                });

                // Handle update click
                $(document).on('click', '.update-btn', function () {
                    let id = $(this).data('id');
                    let newValue = $(`.confidence-input[data-id="${id}"]`).val();

                    $.ajax({
                        url: "{{ route('video-moderation.updateConfidence') }}",
                        type: "POST",
                        data: {
                            id: id,
                            confidence_score: newValue,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            success_noti(response.message);
                            $('#language_list').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message || "Something went wrong.");
                        }
                    });
                });

                $(document).on('click', '.confidence-input[readonly]', function () {
                    $(this).prop('readonly', false).focus();
                });

            });

        </script>

        <script>
            document.getElementById('csvImportForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btn = this.querySelector('button');
                btn.disabled = true;
                btn.textContent = 'Importing...';

                fetch('{{ route('video-moderation.import') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
                })
                    .then(response => response.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.textContent = 'Import';

                        const resDiv = document.getElementById('responseMessage');
                        resDiv.innerHTML = '';

                        if (data.status === 'validation_error') {
                            let errList = '<ul>';
                            Object.keys(data.errors).forEach(key => {
                                errList += `<li>${data.errors[key][0]}</li>`;
                            });
                            errList += '</ul>';
                            resDiv.innerHTML = `<div class="alert alert-danger">${errList}</div>`;
                        } else if (data.status === 'success') {
                            success_noti(data.message);
                            $('#importDataModal').modal('hide');
                            $('#language_list').DataTable().ajax.reload();
                            // resDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        } else {
                            success_noti(data.message);
                            $('#importDataModal').modal('hide');
                        }
                    })
                    .catch(error => {
                        btn.disabled = false;
                        btn.textContent = 'Import';
                        document.getElementById('responseMessage').innerHTML =
                            `<div class="alert alert-danger">Error: ${error.message}</div>`;
                    });
            });


            function deleteRecord(id) {
                if (confirm("Are you sure you want to delete this record?")) {
                    $.ajax({
                        url: '{{ route('video-moderation.delete') }}',
                        type: "POST", // <-- Change DELETE to POST
                        data: {
                            id: id,
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

    @endpush
</x-app-layout>