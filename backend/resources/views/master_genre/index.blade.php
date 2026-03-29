<x-app-layout>
    
   <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="genre_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Category Title</th>
                    <th>Genre Title</th>
                    <th>Is Filter Tag</th>
                    <th>Status</th>
                    <th>Position</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>                                                                                
        </table>
    </div>

    <!-- Add Genre Modal -->
    <div class="modal fade custom-modal" id="addGenreModal" tabindex="-1" aria-labelledby="addGenreLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="addGenreLabel">Add Genre</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="row g-3">
                    <!-- Identifier -->
                    <!-- <div class="col-12">
                        <input type="text" class="form-control" name="displaytitle" id="displaytitle" placeholder="Enter Display Title">
                    </div> -->

                    <div class="col-12">
                        <select id="categorySelect" class="form-select" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->display_title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Title + Language -->
                    <!-- <div class="col-12">
                        <div class="input-group">
                            <input type="text" class="form-control" name="title" id="inputTitle" placeholder="Enter Content title" style="flex: 0 0 60%;">
                            <select class="form-select" name="language_type" id="languageType" style="flex: 0 0 40%;">
                                <option value="">Optional</option>
                                <option value="default">Default</option>
                            </select>
                        </div>
                    </div> -->

                    <!-- Title + Language Dynamic Fields -->
                    <!-- Title + Language Dynamic Fields -->
                    <div class="col-12">
                        <div id="titleContainer"></div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addLanguageBtn">
                            + Add Language
                        </button>
                    </div>


                    <!-- Status -->
                    <div class="col-2 d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="status" id="inputStatus">
                            <label class="form-check-label" for="inputStatus">Status</label>
                        </div>
                    </div>

                    <div class="col-2 d-flex align-items-center">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="filtertag" id="inputFilter">
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



    <!-- View Genre Modal -->
    <div class="modal fade custom-modal" id="viewGenreModal" tabindex="-1" aria-labelledby="viewGenreLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="viewGenreLabel">View Genre</h5>
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
                            <h6 class="mb-0 text-uppercase">Genre Titles (Languages)</h6>
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
        var table = $('#genre_list').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            ajax: "{{ route('master-genres.get_genres') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex',filter: 'none', orderable: false, searchable: false },
                { data: 'category_title', name: 'category_title' },
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
            createdRow: function(row, data) {
                $(row).attr('data-id', data.id);

                // Status badge
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

                $('td', row).eq(4).html(statusBadge).addClass('font-weight-bold');

                // Filter tag
                let filterTagText = data.is_filter_tag == 'Yes'
                    ? '<span class="badge bg-primary">Yes</span>'
                    : '<span class="badge bg-dark">No</span>';
                $('td', row).eq(3).html(filterTagText).addClass('font-weight-bold');
            },
            dom: "<'d-flex justify-content-end'B>rtip",
            buttons: [
                {
                    text: 'Add Genre',
                    className: 'btn btn-primary',
                    action: function() {
                        $('#addGenreModal').modal('show');
                    }
                },
                {
                    text: 'Save Positions',
                    className: 'btn btn-success d-none', // hidden initially
                    attr: { id: 'savePositionsBtn' },
                    action: function() {
                        // Block if filters applied
                        if (table.search() || table.columns().search().filter(Boolean).length > 0) {
                            error_noti('Cannot save positions while filters are applied!');
                            return;
                        }

                        let positions = [];
                        $('#genre_list tbody tr').each(function(index) {
                            positions.push({ id: $(this).data('id'), position: index + 1 });
                        });

                        $.ajax({
                            url: "{{ route('master-genres.savePositions') }}",
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}", positions: positions },
                            success: function(response) {
                                success_noti(response.message);
                                $('#savePositionsBtn, #discardPositionsBtn').addClass('d-none');
                                table.ajax.reload(null, false);
                            },
                            error: function() {
                                error_noti('Something went wrong!');
                            }
                        });
                    }
                },
                {
                    text: 'Discard',
                    className: 'btn btn-danger d-none', // hidden initially
                    attr: { id: 'discardPositionsBtn' },
                    action: function() {
                        // reset table to original data
                        table.ajax.reload(null, false);

                        // hide both buttons again
                        $('#savePositionsBtn, #discardPositionsBtn').addClass('d-none');
                    }
                },
                {
                    text: 'Filter',
                    className: 'btn btn-outline-secondary',
                    action: function(e, dt, node) {
                        let $btn = $(node);

                        // Toggle filter row if already exists
                        if ($("#genre_list thead tr.filter-row").length) {
                            $("#genre_list thead tr.filter-row").toggle();
                            $btn.toggleClass("btn-secondary active btn-outline-secondary");
                            return;
                        }

                        // Add filter row
                        let filterRow = $('<tr class="filter-row"></tr>');
                        $('#genre_list thead tr th').each(function() {
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
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </th>
                                `);
                            }
                            else {
                                filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" /></th>');
                            }
                        });

                        // Append filter row to table
                        $('#genre_list thead').append(filterRow);
                        $btn.removeClass("btn-outline-secondary").addClass("btn-secondary active");

                        // Initialize select2 for dropdown filters
                        $('.statusFilter').select2({ 
                            placeholder: "Select Status", 
                            allowClear: true, 
                            minimumResultsForSearch: Infinity 
                        }).on('change', function() {
                            table.column($(this).parent().index()).search(this.value).draw();
                        });

                        $('.tagFilter').select2({ 
                            placeholder: "Select Tag", 
                            allowClear: true, 
                            minimumResultsForSearch: Infinity 
                        }).on('change', function() {
                            table.column($(this).parent().index()).search(this.value).draw();
                        });

                        // Initialize date range picker
                        $('.dateFilter').daterangepicker({
                            autoUpdateInput: false,
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
                        }).on('apply.daterangepicker', function(ev, picker) {
                            let start = picker.startDate.format('YYYY-MM-DD');
                            let end = picker.endDate.format('YYYY-MM-DD');
                            $(this).val(`${start} - ${end}`);
                            table.column($(this).parent().index()).search(`${start} - ${end}`).draw();
                        }).on('cancel.daterangepicker', function() {
                            $(this).val('');
                            table.column($(this).parent().index()).search('').draw();
                        });

                        // Apply search on Enter for text inputs
                        table.columns().every(function(index) {
                            $('input', $('.filter-row th').eq(index)).on('keypress', function(e) {
                                if (e.which === 13) { // Enter key
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
            drawCallback: function() {
                $('#genre_list tbody tr.loader-row').remove();
                $('#genre_list tbody tr').show();
            }
        });
        // Enable sortable
        $('#genre_list tbody').sortable({
            cursor: 'move',
            helper: function(e, ui) {
                ui.children().each(function() { $(this).width($(this).width()); });
                return ui;
            },
            update: function() {
                $('#savePositionsBtn').removeClass('d-none'); // show save button
            }
        }).disableSelection();

    });


    
    $('#submitBtn').click(function() {
        let id = $('#addGenreModal').data('id'); // if editing
        // let displayTitle = $('#displaytitle').val();
        let status = $('#inputStatus').is(':checked') ? 1 : 0;
        let categoryId = $('#categorySelect').val();
        let filterTag = $('#inputFilter').is(':checked') ? 1 : 0;
        // if (!displayTitle) {
        //     error_noti('Display Title is required!');
        //     return;
        // }
        if (!categoryId) {
            error_noti('Please select a category!');
            return;
        }
        // Collect all titles from dynamic fields
        let titles = [];
        $('#titleContainer .input-group').each(function() {
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
            ? "/master-genres/" + id // update
            : "{{ route('master-genres.store') }}"; // create
        let type = id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: type,
            data: {
                _token: "{{ csrf_token() }}",
                // display_title: displayTitle,
                title: titles,  // send as JSON string
                category_id: categoryId,
                status: status,
                is_filter_tag: filterTag
            },
            success: function(response) {
                success_noti(response.message);
                $('#addGenreModal').modal('hide');
                $('#addGenreModal').removeData('id');
                $('#addGenreLabel').text('Add Genre');
                $('#genre_list').DataTable().ajax.reload(null, false);
            },
            error: function(xhr) {
                error_noti(xhr.responseJSON?.message ?? 'Something went wrong!');
            }
        });
    });




    let languageIndex = 0; // keeps track of dynamic input index

    // When editing, set the correct starting index
    function populateExistingTitles(titles) {
        $('#titleContainer').empty();
        titles.forEach(function(item, index) {
            languageIndex = index + 1; // next new input will have this index

            // Build select options dynamically
            let options = '<option value="">Select Language</option>';
            availableLanguages.forEach(function(lang) {
                let selected = lang.identifier === item.language ? 'selected' : '';
                options += `<option value="${lang.identifier}" ${selected}>${lang.identifier}</option>`;
            });

            let field = `
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="titles[${index}][content]" value="${item.content}" placeholder="Enter content">
                    <select class="form-select" name="titles[${index}][language]">
                        ${options}
                    </select>
                </div>
            `;
            $('#titleContainer').append(field);
        });
    }

    // View Category
    $(document).on('click', '.view-genre', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        $.ajax({
            url: '/master-genres/' + id,
            type: 'GET',
            success: function(data) {
                $('#viewDisplayTitle').text(data.display_title);
                $('#viewStatus').text(data.status == 1 ? 'Active' : 'Inactive');
                $('#viewFilterTag').text(data.is_filter_tag == 1 ? 'Yes' : 'No');

                // Parse JSON title and populate
                let titles = JSON.parse(data.title);
                let html = '';
                titles.forEach(function(item) {
                    html += `
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="${item.content}" readonly>
                            <input type="text" class="form-control" value="${item.language}" readonly>
                        </div>
                    `;
                });
                $('#viewTitleContainer').html(html);

                $('#viewGenreModal').modal('show');
            },
            error: function(xhr) {
                error_noti('Could not fetch data!');
            }
        });
    });

    // Edit Genre
    $(document).on('click', '.edit-genre', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.ajax({
            url: '/master-genres/' + id,
            type: 'GET',
            success: function(data) {
                // $('#displaytitle').val(data.display_title);
                $('#inputStatus').prop('checked', data.status == 1);
                $('#inputFilter').prop('checked', data.is_filter_tag == 1);
                $('#categorySelect').val(data.category_id).trigger('change');
                $('#addGenreModal').data('id', data.id);
                $('#addGenreLabel').text('Edit Genre');

                // Parse JSON title
                let titles = JSON.parse(data.title);
                populateExistingTitles(titles);

                $('#addGenreModal').modal('show');
            },
            error: function(xhr) {
                error_noti('Could not fetch data!');
            }
        });
    });

    // Add new language input dynamically
    $('#addLanguageBtn').click(function() {
        // Build options
        let options = '<option value="">Select Language</option>';
        availableLanguages.forEach(function(lang) {
            options += `<option value="${lang.identifier}">${lang.identifier}</option>`;
        });

        let field = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="titles[${languageIndex}][content]" placeholder="Enter content">
                <select class="form-select" name="titles[${languageIndex}][language]">
                    ${options}
                </select>
            </div>
        `;
        $('#titleContainer').append(field);
        languageIndex++;
    });


    let positionsChanged = false;

    function makeTableSortable() {
        $('#genre_list tbody').sortable({
            cursor: 'move',
            helper: function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            },
            update: function(event, ui) {
                console.log('Row positions changed!');
                $('#savePositionsBtn').removeClass('d-none');
                $('#discardPositionsBtn').removeClass('d-none');
            }
        }).disableSelection();
    }

    $(document).ready(function() {
        makeTableSortable();
    });



            // Delete Category
            $('#genre_list').on('click', '.delete-genre', function () {
                let categoryId = $(this).data('id');

                if (confirm('Are you sure you want to delete this category?')) {
                    $.ajax({
                        url: '/master-genres/' + categoryId, // your route for delete
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            success_noti(response.message); // your success notification function
                            $('#genre_list').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message || 'Something went wrong!');
                        }
                    });
                }
            });

    </script>
    @endpush
</x-app-layout>
