<x-app-layout>

    <!-- Basic Layout for view  -->

    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="category_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <!-- <th>Title</th> -->
                    <th>Display Title</th>
                    <th>Is Filter Tag</th>
                    <th>Status</th>
                    <th>Position</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Basic Layout for view  -->

    <!-- Add Category Modal -->
    <div class="modal fade custom-modal" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="addCategoryLabel">Add Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row g-3">
                        <!-- Title + Language Dynamic Fields -->
                        <div class="col-12">
                            <div id="titleContainer"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addLanguageBtn">
                                + Add Language
                            </button>
                        </div>
                        <!-- Status -->
                        <div class="col-4 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="status"
                                    id="inputStatus">
                                <label class="form-check-label" for="inputStatus">Status</label>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="filtertag"
                                    id="inputFilter">
                                <label class="form-check-label" for="inputFilter">Is Filter Tag</label>
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

    <!-- View Category Modal -->
    <div class="modal fade custom-modal" id="viewCategoryModal" tabindex="-1" aria-labelledby="viewCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="viewCategoryLabel">View Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <!-- Display Title -->
                        <div class="col-md-4">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Title</h6>
                                </div>
                                <div class="card-body pt-2 px-3">
                                    <p class="card-text fw-bold mb-0" id="viewDisplayTitle"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Status</h6>
                                </div>
                                <div class="card-body pt-2 px-3">
                                    <p class="card-text fw-bold mb-0" id="viewStatus"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Is Filter Tag -->
                        <div class="col-md-4">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Is Filter Tag</h6>
                                </div>
                                <div class="card-body pt-2 px-3">
                                    <p class="card-text fw-bold mb-0" id="viewFilterTag"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Titles Section -->
                    <div class="card shadow-sm border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-uppercase">Category Titles (Languages)</h6>
                        </div>
                        <div class="card-body" id="viewTitleContainer" style="min-height: 100px;">
                            <!-- Dynamic language titles appear here -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

        <script src="{{ asset('assets/js/jquery-ui.css') }}"></script>

        <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

        <script type="text/javascript">
            let availableLanguages = @json($languages);

            $(function () {

                var table = $('#category_list').DataTable({
                    processing: false,
                    serverSide: true,
                    autoWidth: false,
                    ajax: "{{ route('master-categories.get_Categories') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex',filter: 'none', orderable: false, searchable: false },
                        { data: 'display_title', name: 'display_title' },
                        { data: 'is_filter_tag', name: 'is_filter_tag' },
                        { data: 'status', name: 'status', orderable: false, searchable: false },
                        { data: 'position', name: 'position' },
                        { data: 'modified_at', name: 'modified_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    columnDefs: [
                        { targets: [0, -1], className: 'text-center' },
                        { targets: 0, width: "100px", className: 'text-center' },
                        { targets: -1, width: "80px", className: 'text-center' }
                    ],
                    createdRow: function (row, data) {
                        $(row).attr('data-id', data.id);
                        let statusBadge = '';
                        if (data.status == 'Active') {
                            statusBadge = '<span class="badge bg-success">Active</span>';
                        } else if (data.status == 'Inactive') {
                            statusBadge = '<span class="badge bg-danger">Inactive</span>';
                        } else if (data.status == 'Deleted') {
                            statusBadge = '<span class="badge bg-secondary">Deleted</span>';
                        } else {
                            statusBadge = '<span class="badge bg-light text-dark">'+statusBadge+'</span>';
                        }
                        $('td', row).eq(3).html(statusBadge).addClass('font-weight-bold');
                        // Filter tag
                        let filterTagText = data.is_filter_tag == 'Yes'
                            ? '<span class="badge bg-primary">Yes</span>'
                            : '<span class="badge bg-dark">No</span>';
                        $('td', row).eq(2).html(filterTagText).addClass('font-weight-bold');
                    },
                    dom: "<'d-flex justify-content-end'B>rtip",
                    buttons: [
                        {
                            text: 'Add Category',
                            className: 'btn btn-primary',
                            action: function () {
                                $('#addCategoryModal').modal('show');
                            }
                        },
                        {
                            text: 'Save Positions',
                            className: 'btn btn-success d-none', // hidden initially
                            attr: { id: 'savePositionsBtn' },
                            action: function () {
                                // Block if filters applied
                                if (table.search() || table.columns().search().filter(Boolean).length > 0) {
                                    error_noti('Cannot save positions while filters are applied!');
                                    return;
                                }

                                let positions = [];
                                $('#category_list tbody tr').each(function (index) {
                                    positions.push({ id: $(this).data('id'), position: index + 1 });
                                });

                                $.ajax({
                                    url: "{{ route('master-categories.savePositions') }}",
                                    type: 'POST',
                                    data: { _token: "{{ csrf_token() }}", positions: positions },
                                    success: function (response) {
                                        success_noti(response.message);
                                        $('#savePositionsBtn, #discardPositionsBtn').addClass('d-none');
                                        table.ajax.reload(null, false);
                                    },
                                    error: function () {
                                        error_noti('Something went wrong!');
                                    }
                                });
                            }
                        },
                        {
                            text: 'Discard',
                            className: 'btn btn-danger d-none', // hidden initially
                            attr: { id: 'discardPositionsBtn' },
                            action: function () {
                                // reset table to original data
                                table.ajax.reload(null, false);

                                // hide both buttons again
                                $('#savePositionsBtn, #discardPositionsBtn').addClass('d-none');
                            }
                        },
                        {
                            text: 'Filter',
                            className: 'btn btn-outline-secondary',
                            action: function (e, dt, node) {
                                let $btn = $(node);

                                // toggle filter row
                                if ($("#category_list thead tr.filter-row").length) {
                                    $("#category_list thead tr.filter-row").toggle();
                                    $btn.toggleClass("btn-secondary active btn-outline-secondary");
                                    return;
                                }

                                // add filter row
                                let filterRow = $('<tr class="filter-row"></tr>');
                                $('#category_list thead tr th').each(function () {
                                    let title = $(this).text().trim();

                                    if (title === "Action" || title === "Sr. No.") {
                                        filterRow.append('<th></th>');
                                    }
                                    else if (title === "Last Modified") {
                                        filterRow.append('<th><input type="text" class="form-control form-control-sm dateFilter" placeholder="Select Date Range" autocomplete="off" /></th>');
                                    }
                                    else if (title === "Status") {
                                        filterRow.append(`
                                                <th>
                                                    <select class="form-select form-select-sm statusFilter" style="width:100%">
                                                        <option value="">All</option>
                                                        <option value="0">Active</option>
                                                        <option value="1">Inactive</option>
                                                        <option value="2">Deleted</option>
                                                    </select>
                                                </th>
                                            `);
                                    }
                                    else if (title === "Is Filter Tag") {
                                        filterRow.append(`
                                                <th>
                                                    <select class="form-select form-select-sm tagFilter" style="width:100%">
                                                        <option value="">All</option>
                                                        <option value="0">Yes</option>
                                                        <option value="1">No</option>
                                                    </select>
                                                </th>
                                            `);
                                    }
                                    else {
                                        filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" /></th>');
                                    }
                                });

                                $('#category_list thead').append(filterRow);
                                $btn.removeClass("btn-outline-secondary").addClass("btn-secondary active");

                                // Initialize Select2 after row is added
                                $('.statusFilter').select2({ placeholder: "Select Status", allowClear: true, minimumResultsForSearch: Infinity })
                                    .on('change', function () {
                                        table.column($(this).parent().index()).search(this.value).draw();
                                    });

                                $('.tagFilter').select2({ placeholder: "Select Tag", allowClear: true, minimumResultsForSearch: Infinity })
                                    .on('change', function () {
                                        table.column($(this).parent().index()).search(this.value).draw();
                                    });

                                // Initialize Date Range Picker
                                $('.dateFilter').daterangepicker({
                                    autoUpdateInput: false, // ❌ input empty by default
                                    opens: 'right',
                                    locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' },
                                    ranges: {
                                        'Today': [moment(), moment()],
                                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                        'This Week': [moment().startOf('week'), moment().endOf('week')],
                                        'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                                        'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                                    }
                                }).on('apply.daterangepicker', function (ev, picker) {
                                    let start = picker.startDate.format('YYYY-MM-DD');
                                    let end = picker.endDate.format('YYYY-MM-DD');
                                    $(this).val(`${start} - ${end}`); // ✅ manually set input value
                                    table.column($(this).parent().index()).search(`${start} - ${end}`).draw();
                                }).on('cancel.daterangepicker', function () {
                                    $(this).val(''); // clear input
                                    table.column($(this).parent().index()).search('').draw();
                                });


                                // Text input search on Enter
                                table.columns().every(function (index) {
                                    $('input', $('.filter-row th').eq(index)).on('keypress', function (e) {
                                        if (e.which === 13) {
                                            table.column(index).search(this.value).draw();
                                        }
                                    });
                                });
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            exportOptions: {
                                columns: ':not(:last-child)' // exclude Action column
                            }
                        },
                        'print'
                    ],
                    drawCallback: function () {
                        $('#category_list tbody tr.loader-row').remove();
                        $('#category_list tbody tr').show();
                    }
                });

                // Enable sortable
                $('#category_list tbody').sortable({
                    cursor: 'move',
                    helper: function (e, ui) {
                        ui.children().each(function () { $(this).width($(this).width()); });
                        return ui;
                    },
                    update: function () {
                        $('#savePositionsBtn').removeClass('d-none'); // show save button
                    }
                }).disableSelection();

            });


            $('#submitBtn').click(function () {
                let id = $('#addCategoryModal').data('id'); // if editing
                // let displayTitle = $('#displaytitle').val();
                let status = $('#inputStatus').is(':checked') ? 1 : 0;
                let filterTag = $('#inputFilter').is(':checked') ? 1 : 0;

                // if (!displayTitle) {
                //     error_noti('Display Title is required!');
                //     return;
                // }

                // Collect all titles from dynamic fields
                let titles = [];
                $('#titleContainer .input-group').each(function () {
                    let content = $(this).find('input[name*="[content]"]').val();
                    let language = $(this).find('select[name*="[language]"]').val();
                    if (content && language) {
                        titles.push({
                            content: content,
                            language: language
                        });
                    }
                });

                if (titles.length === 0) {
                    error_noti('Please add at least one language content!');
                    return;
                }

                let url = id
                    ? "/master-categories/" + id // update
                    : "{{ route('master-categories.store') }}"; // create
                let type = id ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: type,
                    data: {
                        _token: "{{ csrf_token() }}",
                        // display_title: displayTitle,
                        title: titles,  // send as JSON string
                        status: status,
                        is_filter_tag: filterTag
                    },
                    success: function (response) {
                        success_noti(response.message);
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryModal').removeData('id');
                        $('#addCategoryLabel').text('Add Category');
                        $('#category_list').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Something went wrong!');
                    }
                });
            });

            let languageIndex = 0; // keeps track of dynamic input index

            // 🟦 Function: Populate existing titles when editing
            function populateExistingTitles(titles) {
                $('#titleContainer').empty();
                titles.forEach(function (item, index) {
                    languageIndex = index + 1; // next new input will have this index

                    // Build select options dynamically
                    let options = '<option value="">Select Language</option>';
                    availableLanguages.forEach(function (lang) {
                        let selected = lang.identifier === item.language ? 'selected' : '';
                        options += `<option value="${lang.identifier}" ${selected}>${lang.identifier}</option>`;
                    });

                    let field = `
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="titles[${index}][content]" 
                                value="${item.content}" placeholder="Enter content">
                            <select class="form-select" name="titles[${index}][language]">
                                ${options}
                            </select>
                        </div>
                    `;
                    $('#titleContainer').append(field);
                });
            }

            // 🟩 View Category
            $(document).on('click', '.view-category', function (e) {
                e.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    url: '/master-categories/' + id,
                    type: 'GET',
                    success: function (data) {
                        $('#viewDisplayTitle').text(data.display_title);
                        $('#viewStatus').text(data.status == 1 ? 'Active' : 'Inactive');
                        $('#viewFilterTag').text(data.is_filter_tag == 1 ? 'Yes' : 'No');

                        // Parse JSON title and populate
                        let titles = JSON.parse(data.title);
                        let html = '';
                        titles.forEach(function (item) {
                            html += `
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" value="${item.content}" readonly>
                                    <input type="text" class="form-control" value="${item.language}" readonly>
                                </div>
                            `;
                        });
                        $('#viewTitleContainer').html(html);

                        $('#viewCategoryModal').modal('show');
                    },
                    error: function () {
                        error_noti('Could not fetch data!');
                    }
                });
            });

            // 🟨 Edit Category
            $(document).on('click', '.edit-category', function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url: '/master-categories/' + id,
                    type: 'GET',
                    success: function (data) {
                        $('#inputStatus').prop('checked', data.status == 1);
                        $('#inputFilter').prop('checked', data.is_filter_tag == 1);
                        $('#addCategoryModal').data('id', data.id);
                        $('#addCategoryLabel').text('Edit Category');

                        // Parse JSON title
                        let titles = JSON.parse(data.title);
                        populateExistingTitles(titles);

                        $('#addCategoryModal').modal('show');
                    },
                    error: function () {
                        error_noti('Could not fetch data!');
                    }
                });
            });

            // 🟦 Function: Create a new language input + select field
            function createLanguageField() {
                let options = '<option value="">Select Language</option>';
                availableLanguages.forEach(function (lang) {
                    options += `<option value="${lang.identifier}">${lang.identifier}</option>`;
                });

                return `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="titles[${languageIndex}][content]" placeholder="Enter content">
                        <select class="form-select" name="titles[${languageIndex}][language]">
                            ${options}
                        </select>
                    </div>
                `;
            }

            // 🟩 Handle “Add Category” modal open
            $('#addCategoryModal').on('show.bs.modal', function () {
                const modal = $(this);

                // If not editing (fresh add)
                if (!modal.data('id')) {
                    $('#titleContainer').empty();
                    languageIndex = 0;
                    $('#titleContainer').append(createLanguageField());
                    languageIndex++;
                }
            });

            // 🟧 Reset modal state on close
            $('#addCategoryModal').on('hidden.bs.modal', function () {
                $(this).removeData('id'); // remove edit state
                $('#addCategoryLabel').text('Add Category');
            });

            // 🟦 Add new language dynamically
            $('#addLanguageBtn').off('click').on('click', function () {
                $('#titleContainer').append(createLanguageField());
                languageIndex++;
            });


            // Delete Category
            $('#category_list').on('click', '.delete-category', function () {
                let categoryId = $(this).data('id');

                if (confirm('Are you sure you want to delete this category?')) {
                    $.ajax({
                        url: '/master-categories/' + categoryId, // your route for delete
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            success_noti(response.message); // your success notification function
                            $('#category_list').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message || 'Something went wrong!');
                        }
                    });
                }
            });



            let positionsChanged = false;

            function makeTableSortable() {
                $('#category_list tbody').sortable({
                    cursor: 'move',
                    helper: function (e, ui) {
                        ui.children().each(function () {
                            $(this).width($(this).width());
                        });
                        return ui;
                    },
                    update: function (event, ui) {
                        console.log('Row positions changed!');
                        $('#savePositionsBtn').removeClass('d-none');
                        $('#discardPositionsBtn').removeClass('d-none');
                    }
                }).disableSelection();
            }

            $(document).ready(function () {
                makeTableSortable();
            });
        </script>
    @endpush
</x-app-layout>