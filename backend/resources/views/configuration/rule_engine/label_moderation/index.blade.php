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
        <!-- <button id="updateChangesBtn" class="btn btn-primary mb-3">Update Changes</button> -->

        <table id="language_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Label Name</th>
                    <th>First Level Category Name(L1)</th>
                    <th>Second Level Category Name(L2)</th>
                    <th>Label Category Moderation (L1)</th>
                    <th>Label Category Moderation (L2)</th>
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
                            <a download href="{{ asset('assets/custom/label.moderation.csv') }}" type="button" class="btn btn-info px-3 btn-sm dwn-btn-margin"><i class="bx bx-cloud-download mr-1"></i>Sample</a>                            
                        </div>
                        <div id="responseMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')     
        <script>           
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#language_list",
                    ajaxUrl: "{{ route('label-moderation.video_moderation_data') }}",
                    moduleName: "Import File",                    
                    modalSelector: "#importDataModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'label_name', filter: 'text' },
                        { data: 'first_level_category_nameL1', filter: 'text' },
                        { data: 'second_level_category_nameL2', filter: 'text' },
                        { data: 'label_category_L1_moderation', filter: 'text' },
                        { data: 'label_category_L2_moderation', filter: 'text' },
                        { data: 'confidence_score', filter: 'text' },
                        { data: 'updated_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    createdRow: function (row, data) {
                        // 🔹 L1 dropdown
                        const selectedL1 = (data.label_category_L1_moderation || '').toLowerCase();
                        const labelL1 = `
                            <select class="form-select form-select-sm moderation-select"
                                    data-id="${data.id}"
                                    data-type="l1">
                                <option value="Yes" ${selectedL1 === 'yes' ? 'selected' : ''}>Yes</option>
                                <option value="No" ${selectedL1 === 'no' ? 'selected' : ''}>No</option>
                            </select>`;

                        // 🔹 L2 dropdown
                        const selectedL2 = (data.label_category_L2_moderation || '').toLowerCase();
                        const labelL2 = `
                            <select class="form-select form-select-sm moderation-select"
                                    data-id="${data.id}"
                                    data-type="l2">
                                <option value="Yes" ${selectedL2 === 'yes' ? 'selected' : ''}>Yes</option>
                                <option value="No" ${selectedL2 === 'no' ? 'selected' : ''}>No</option>
                            </select>`;

                        // 🔹 Confidence input
                        const input = `
                            <input type="number" min="0" max="100" step="0.01"
                                class="form-control confidence-input custom-padding"
                                value="${data.confidence_score ?? ''}"
                                data-id="${data.id}"
                                readonly />`;


                        // Insert all controls into the row
                        $('td', row).eq(4).html(labelL1);
                        $('td', row).eq(5).html(labelL2);
                        $('td', row).eq(6).html(`
                            <div class="d-flex gap-2 align-items-center">
                                ${input}                          
                            </div>
                        `);
                    }
                });

                // 🔹 Make confidence editable
                $(document).on('click', '.confidence-input[readonly]', function () {
                    $(this).prop('readonly', false).focus();
                });

                // 🔹 When confidence changes, enable that row’s update button
                $(document).on('input', '.confidence-input', function () {
                    const id = $(this).data('id');
                    enableUpdateButton(id);
                });

                // 🔹 When moderation dropdown changes, enable that row’s update button
                $(document).on('change', '.moderation-select', function () {
                    const id = $(this).data('id');
                    enableUpdateButton(id);
                });

                // 🔹 Enable button for the row
                function enableUpdateButton(id) {
                    const row = $(`#language_list tr:has([data-id="${id}"])`);
                    row.find('.update-btn').prop('disabled', false);
                    row.addClass('table-warning');
                }
                $(document).on('click', '.update-btn', function () {
                    const id = $(this).data('id');

                    const row = $(`#language_list tr:has([data-id="${id}"])`);
                    const l1 = row.find('select[data-type="l1"]').val();
                    const l2 = row.find('select[data-type="l2"]').val();
                    const score = row.find('.confidence-input').val();

                    $.ajax({
                        url: "{{ route('label-moderation.updateSingleRow') }}",
                        type: "POST",
                        data: {
                            id: id,
                            label_category_L1_moderation: l1,
                            label_category_L2_moderation: l2,
                            confidence_score: score,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            success_noti(response.message);
                            row.removeClass('table-warning').addClass('table-success');
                            row.find('.update-btn').prop('disabled', true);
                            setTimeout(() => row.removeClass('table-success'), 1200);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message || "Something went wrong.");
                        }
                    });
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

                fetch('{{ route('label-moderation.import') }}', {
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
                        url: '{{ route('label-moderation.delete') }}',
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