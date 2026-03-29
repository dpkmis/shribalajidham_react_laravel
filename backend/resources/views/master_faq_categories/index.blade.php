<x-app-layout>
    
   <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table id="faq_category_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Language</th>
                    <th>Display Name</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>                                                                                
        </table>
    </div>


    <!-- Add/Edit Faq Category Modal -->
    <div class="modal fade custom-modal" id="addFaqCategoryModal" tabindex="-1" aria-labelledby="addFaqCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="addFaqCategoryLabel">Add Faq Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Display Title -->
                        <div class="col-12">
                            <input type="text" class="form-control" name="displaytitle" id="displaytitle" placeholder="Enter Display Title">
                        </div>

                        <!-- Category Select Field -->
                        <div class="col-12">
                            <select id="contentLanguageSelect" class="form-select" name="content_language_id">
                                <option value="">Select Language</option>
                                <!-- Options will be injected dynamically via JS -->
                            </select>
                        </div>

                        <!-- Category Titles + Languages (Dynamic Fields) -->
                        <div class="col-12">
                            <label class="form-label">Language Titles</label>
                            <div id="titleContainer"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addLanguageBtn">
                                + Add Language
                            </button>
                        </div>

                        <!-- Status -->
                        <div class="col-2 d-flex align-items-center mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="0" name="status" id="inputStatus">
                                <label class="form-check-label" for="inputStatus">Status</label>
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
                        <div class="col-md-8">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Display Title</h6>
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
                    </div>

                    <!-- Titles per Language -->
                    <div class="card shadow-sm border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-uppercase">Category Titles (Languages)</h6>
                        </div>
                        <div class="card-body" id="viewTitleContainer" style="min-height: 100px;">
                            <!-- Dynamic language titles will be populated here -->
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
        var table = $('#faq_category_list').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-faq_categories.get_faqCategories') }}",
                data: function(d) {
                    // Send filter values to server
                    d.status = $('.statusFilter').val(); // Status filter
                    d.lang_id = $('.languageFilter').val(); // Language filter
                    d.display_name = $('.filter-display_name').val(); // Example text input filter
                    d.position = $('.filter-position').val(); // Example number filter
                    d.modified_at = $('.dateFilter').val(); // Date range
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex',filter: 'none', orderable: false, searchable: false },
                { data: 'language_identifier', name: 'lang_id' },
                { data: 'display_name', name: 'display_name' },
                { data: 'position', name: 'position' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'modified_at', name: 'modified_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[3, 'asc']], // initial order by position
            columnDefs: [
                { targets: [0, -1], className: 'text-center' },
                { targets: 0, width: "100px", className: 'text-center' },
                { targets: -1, width: "80px", className: 'text-center' }
            ],
            createdRow: function(row, data) {
                $(row).attr('data-id', data.id);

                // Status badge
                let statusBadge = '';
                if (data.status == 'Enable') {
                    statusBadge = '<span class="badge bg-success">Enable</span>';
                } else if (data.status == 'Disable') {
                    statusBadge = '<span class="badge bg-danger">Disable</span>';
                } else if (data.status == 'Deleted') {
                    statusBadge = '<span class="badge bg-secondary">Deleted</span>';
                }

                $('td', row).eq(4).html(statusBadge).addClass('font-weight-bold');

            },
            dom: "<'d-flex justify-content-end'B>rtip",
            buttons: [
                {
                    text: 'Add Faq Category',
                    className: 'btn btn-primary',
                    action: function() {
                        populateLanguageSelect();
                        $('#addFaqCategoryModal').modal('show');
                    }
                },
                {
                    text: 'Save Positions',
                    className: 'btn btn-success d-none',
                    attr: { id: 'savePositionsBtn' },
                    action: function() {
                        if (table.search() || table.columns().search().filter(Boolean).length > 0) {
                            error_noti('Cannot save positions while filters are applied!');
                            return;
                        }
                        let positions = [];
                        $('#faq_category_list tbody tr').each(function(index) {
                            positions.push({ id: $(this).data('id'), position: index + 1 });
                        });

                        $.ajax({
                            url: "{{ route('master-faq_categories.savePositions') }}",
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
                    className: 'btn btn-danger d-none',
                    attr: { id: 'discardPositionsBtn' },
                    action: function() {
                        table.ajax.reload(null, false);
                        $('#savePositionsBtn, #discardPositionsBtn').addClass('d-none');
                    }
                },
                {
                    text: 'Filter',
                    className: 'btn btn-outline-secondary',
                    action: function(e, dt, node) {
                        let $btn = $(node);
                        if ($("#faq_category_list thead tr.filter-row").length) {
                            $("#faq_category_list thead tr.filter-row").toggle();
                            $btn.toggleClass("btn-secondary active btn-outline-secondary");
                            return;
                        }

                        // Add filter row
                        let filterRow = $('<tr class="filter-row"></tr>');
                        $('#faq_category_list thead tr th').each(function() {
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
                                            <option value="0">Enable</option>
                                            <option value="1">Disable</option>
                                            <option value="2">Deleted</option>
                                        </select>
                                    </th>
                                `);
                            }
                            else if (title === "Language") {
                                let options = '<option value="">All</option>';
                                availableLanguages.forEach(lang => {
                                    options += `<option value="${lang.id}">${lang.identifier}</option>`;
                                });
                                filterRow.append(`
                                    <th>
                                        <select class="form-select form-select-sm languageFilter" style="width:100%">
                                            ${options}
                                        </select>
                                    </th>
                                `);
                            }
                            else {
                                filterRow.append('<th><input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" /></th>');
                            }
                        });

                        $('#faq_category_list thead').append(filterRow);
                        $btn.removeClass("btn-outline-secondary").addClass("btn-secondary active");
                        // Initialize select2 for dropdown filters
                        $('.statusFilter').select2({ allowClear: true, minimumResultsForSearch: Infinity })
                        .on('change', function() {
                            let colIndex = $(this).closest('th').index();
                            let value = this.value;
                            if(value !== "") {
                                table.column(colIndex).search('^' + value + '$', true, false).draw();
                            } else {
                                table.column(colIndex).search('').draw();
                            }
                        });

                        $('.languageFilter').select2({ allowClear: true, minimumResultsForSearch: Infinity })
                        .on('change', function() {
                            table.column($(this).parent().index()).search(this.value).draw();
                        });


                        // Initialize date range picker
                        $('.dateFilter').daterangepicker({
                            autoUpdateInput: false,
                            opens: 'right',
                            locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' }
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
                        columns: ':not(:last-child)', // exclude last column (Action)
                    }
                },
                'print'
            ],
            drawCallback: function() {
                $('#faq_category_list tbody tr.loader-row').remove();
                $('#faq_category_list tbody tr').show();
            }
        });

        // Enable sortable
        $('#faq_category_list tbody').sortable({
            cursor: 'move',
            helper: function(e, ui) {
                ui.children().each(function() { $(this).width($(this).width()); });
                return ui;
            },
            update: function() {
                $('#savePositionsBtn').removeClass('d-none');
            }
        }).disableSelection();
    });

    $('#submitBtn').click(function() {
        let id = $('#addFaqCategoryModal').data('id'); // if editing
        let displayTitle = $('#displaytitle').val();
        let contentLanguageId = $('#contentLanguageSelect').val(); // ✅ main content language
        let status = $('#inputStatus').is(':checked') ? 0 : 1;

        if (!displayTitle) {
            error_noti('Display Title is required!');
            return;
        }
        if (!contentLanguageId) {
            error_noti('Please select a main language!');
            return;
        }

        // Collect all titles from dynamic fields
        let titles = [];
        $('#titleContainer .input-group').each(function() {
            let content = $(this).find('input[name*="[content]"]').val();
            // Updated: check for select or input
            let language = $(this).find('select[name*="[language]"]').val() || $(this).find('input[name*="[language]"]').val();

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
            ? "/master-faq_categories/" + id // update
            : "{{ route('master-faq_categories.store') }}"; // create
        let type = id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: type,
            data: {
                _token: "{{ csrf_token() }}",
                display_title: displayTitle,
                content_language_id: contentLanguageId, // ✅ send main language id
                title: titles, // ✅ translations
                status: status
            },
            success: function(response) {
                success_noti(response.message);
                $('#addFaqCategoryModal').modal('hide');
                $('#addFaqCategoryModal').removeData('id');
                $('#addFaqCategoryLabel').text('Add Faq Category');
                $('#faq_category_list').DataTable().ajax.reload(null, false);
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

    // View Category// View Category
    $(document).on('click', '.view-faq_category', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        $.ajax({
            url: '/master-faq_categories/' + id,
            type: 'GET',
            success: function(data) {
                $('#viewDisplayTitle').text(data.display_name);
                let statusBadge = '';
                if (data.status == 0) {
                    statusBadge = '<span class="badge bg-success">Enable</span>';
                } else if (data.status == 1) {
                    statusBadge = '<span class="badge bg-danger">Disable</span>';
                } else if (data.status == 2) {
                    statusBadge = '<span class="badge bg-secondary">Deleted</span>';
                }

                $('#viewStatus').html(statusBadge);


                // Clear previous
                $('#viewTitleContainer').html('');

                // Parse JSON category_name
                let titles = JSON.parse(data.category_name);
                let html = '';
                for (const [lang, content] of Object.entries(titles)) {
                    html += `
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="${content}" readonly>
                            <input type="text" class="form-control" value="${lang}" readonly>
                        </div>
                    `;
                }
                $('#viewTitleContainer').html(html);

                $('#viewCategoryModal').modal('show');
            },
            error: function(xhr) {
                error_noti('Could not fetch data!');
            }
        });
    });
    // Edit category
    $(document).on('click', '.edit-faq_category', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        $.ajax({
            url: '/master-faq_categories/' + id,
            type: 'GET',
            success: function(data) {
                console.log(data);

                // Display main fields
                $('#displaytitle').val(data.display_name);
                $('#inputStatus').prop('checked', data.status == 0);
                $('#addFaqCategoryModal').data('id', data.id);
                $('#addFaqCategoryLabel').text('Edit Category');

                // Populate main language select
                populateLanguageSelect(data.lang_id);

                // Clear previous titles
                $('#titleContainer').html('');

                // Parse category_name JSON
                let titles = JSON.parse(data.category_name);

                // Populate title fields as input + select
                for (const [lang, content] of Object.entries(titles)) {
                    let options = '<option value="">Select Language</option>';
                    availableLanguages.forEach(function(l) {
                        let selected = l.identifier === lang ? 'selected' : '';
                        options += `<option value="${l.identifier}" ${selected}>${l.identifier}</option>`;
                    });

                    let fieldHtml = `
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="title[content][]" value="${content}" placeholder="Content">
                            <select class="form-select" name="title[language][]">
                                ${options}
                            </select>
                            <button type="button" class="btn btn-danger removeLanguageBtn">×</button>
                        </div>
                    `;
                    $('#titleContainer').append(fieldHtml);
                }

                $('#addFaqCategoryModal').modal('show');
            },
            error: function(xhr) {
                error_noti('Could not fetch data!');
            }
        });
    });


    function populateLanguageSelect(selectedId = null) {
        let select = $('#contentLanguageSelect');
        select.empty();
        select.append('<option value="">Select Language</option>');

        availableLanguages.forEach(lang => {
            select.append(`
                <option value="${lang.id}" ${lang.id == selectedId ? 'selected' : ''}>
                    ${lang.identifier}
                </option>
            `);
        });
    }


    // Add Language Button
    $(document).on('click', '#addLanguageBtn', function() {
        // Build options for the select dynamically
        let options = '<option value="">Select Language</option>';
        availableLanguages.forEach(lang => {
            options += `<option value="${lang.identifier}">${lang.identifier}</option>`;
        });

        // Build input group with select instead of text input for language
        let fieldHtml = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="title[content][]" placeholder="Content">
                <select class="form-select" name="title[language][]">
                    ${options}
                </select>
                <button type="button" class="btn btn-danger removeLanguageBtn">×</button>
            </div>
        `;
        $('#titleContainer').append(fieldHtml);
    });

    // Remove language row
    $(document).on('click', '.removeLanguageBtn', function() {
        $(this).closest('.input-group').remove();
    });


            // Delete Category
            $('#faq_category_list').on('click', '.delete-faq_category', function () {
                let categoryId = $(this).data('id');

                if (confirm('Are you sure you want to delete this FAQ category?')) {
                    $.ajax({
                        url: '/master-faq_categories/' + categoryId, // your route for delete
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            success_noti(response.message); // your success notification function
                            $('#faq_category_list').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            error_noti(xhr.responseJSON?.message || 'Something went wrong!');
                        }
                    });
                }
            });

    let positionsChanged = false;

    function makeTableSortable() {
        $('#faq_category_list tbody').sortable({
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
    </script>
    @endpush
</x-app-layout>
