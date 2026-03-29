<x-app-layout>
    
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="pages_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Language</th>
                    <th>Page Title</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Last Modified</th>
                    <th>Action</th>
                </tr>
            </thead>                                                                      
        </table>
    </div>
    <!-- Add/Edit Page Content Modal -->
    <div class="modal fade custom-modal" id="addPageContentModal" tabindex="-1" aria-labelledby="addPageContentLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="addPageContentLabel">Add Page Content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Identifier -->
                        <div class="col-12">
                            <input type="text" class="form-control" name="identifier" id="identifier" placeholder="Enter Page Identifier (e.g. about_us)">
                        </div>

                        <!-- inputLink -->
                        <div class="col-12 mt-2">
                            <input type="text" class="form-control" name="inputLink" id="inputLink" placeholder="Enter Page Link (e.g. https://example.com/about-us)">
                        </div>

                        <!-- Language Select -->
                        <div class="col-12 mt-2">
                            <select id="contentLanguageSelect" class="form-select" name="content_language_id">
                                <option value="">Select Language</option>
                                <!-- Inject options dynamically -->
                            </select>
                        </div>

                        <!-- Page Content (Editor) -->
                        <div class="col-12 mt-3">
                            <label class="form-label">Description / Content</label>
                            <div class="card">
                                <div class="card-body">
                                    <div id="editor" style="min-height: 300px;">
                                        <p>
                                           Write your page content here... </br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-2 d-flex align-items-center mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="status" id="inputStatus">
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
    <!-- View Page Content Modal -->
    <div class="modal fade custom-modal" id="viewPageContentModal" tabindex="-1" aria-labelledby="viewPageContentLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info ">
                    <h5 class="modal-title text-white" id="viewPageContentLabel">View Page Content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Identifier</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="card-text fw-bold mb-0" id="viewIdentifier"></p>
                                </div>
                            </div>
                        </div>

                        <!-- inputLink -->
                        <div class="col-md-4">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Hyperlink</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="card-text fw-bold mb-0" id="viewLink"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Language</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="card-text fw-bold mb-0" id="viewLanguage"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mt-3">
                            <div class="card shadow-sm border-light h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-uppercase">Status</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="card-text mb-0" id="viewStatus"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Content -->
                    <div class="card shadow-sm border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-uppercase">Page Description</h6>
                        </div>
                        <div class="card-body" id="viewEditorContent" style="min-height: 250px;">
                            <!-- Content populated by JS -->
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

    @php
        $languages = \DB::table('ugc_language')->select('id', 'identifier')->get();
    @endphp

    <script src="{{ asset('assets/js/jquery-ui.css') }}"></script>

    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <script src="{{ asset('assets/js/ckeditor.js') }}"></script>

    <script>
        let availableLanguages = @json($languages);

        let editorInstance;

        $(document).ready(function() {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: [
                        'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 
                        'blockQuote', 'insertTable', 'undo', 'redo'
                    ]
                })
                .then(editor => {
                    editorInstance = editor;
                })
                .catch(error => console.error(error));
        });

        if(editorInstance){
            editorInstance.setData(data.description || '<p></p>');
        }

    </script>

    <script type="text/javascript">
    let table; 
    $(function () {

        var table = $('#pages_list').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-page-content.get_pages') }}",
                data: function(d) {
                    // Send filter values to server
                    d.status = $('.statusFilter').val(); // Status filter
                    d.lang_id = $('.languageFilter').val(); // Language filter
                    d.display_name = $('.title').val(); // Example text input filter
                    d.position = $('.filter-position').val(); // Example number filter
                    d.modified_at = $('.dateFilter').val(); // Date range
                }
            },
            columns: [
                { 
                    data: null, // use null because it's generated dynamically
                    name: 'sr_no', 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    } 
                },
                { data: 'language_identifier', name: 'lang_id' },
                { data: 'title', name: 'title' },
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
                    text: 'Add Page',
                    className: 'btn btn-primary',
                    action: function() {
                        populateLanguageSelect();
                        $('#addPageContentModal').modal('show');
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
                        $('#pages_list tbody tr').each(function(index) {
                            positions.push({ id: $(this).data('id'), position: index + 1 });
                        });

                        $.ajax({
                            url: "{{ route('master-page-content.savePositions') }}",
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
                        if ($("#pages_list thead tr.filter-row").length) {
                            $("#pages_list thead tr.filter-row").toggle();
                            $btn.toggleClass("btn-secondary active btn-outline-secondary");
                            return;
                        }

                        // Add filter row
                        let filterRow = $('<tr class="filter-row"></tr>');
                        $('#pages_list thead tr th').each(function() {
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
                                            <option value="2">Deleted</option> <!-- added -->
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

                        $('#pages_list thead').append(filterRow);
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
                $('#pages_list tbody tr.loader-row').remove();
                $('#pages_list tbody tr').show();
            }
        });

        // Enable sortable
        $('#pages_list tbody').sortable({
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

    // 🟦 Submit (Add / Edit) Page Content
    $('#submitBtn').click(function () {
        let id = $('#addPageContentModal').data('id'); // if editing
        let identifier = $('#identifier').val().trim();
        let contentLanguageId = $('#contentLanguageSelect').val();
        let status = $('#inputStatus').is(':checked') ? 1 : 0;
        let content = editorInstance.getData(); // get HTML from editor
        let link = ($('#inputLink').val() || '').trim();

        if (!identifier) {
            error_noti('Identifier is required!');
            return;
        }
        if (!contentLanguageId) {
            error_noti('Please select a language!');
            return;
        }
        if (!link) {
            error_noti('Hyperlink is required!!');
            return;
        }
        if (!content || content === '<p><br></p>') {
            error_noti('Content cannot be empty!');
            return;
        }

        // Prepare "title" array for Laravel
        let title = [{
            language: contentLanguageId,
            content: content
        }];

        // Prepare slug for "page" column
        let pageSlug = identifier
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, '') // remove special chars
            .trim()
            .replace(/\s+/g, '-'); // replace spaces with "-"

        let url = id
            ? `/master-page-content/${id}` // update
            : "{{ route('master-page-content.store') }}"; // create
        let type = id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: type,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                display_title: identifier,
                content_language_id: contentLanguageId,
                content: content,
                status: status,
                page: pageSlug,
                link: link 
            }),
            success: function(response){
                success_noti(response.message);
                $('#addPageContentModal').modal('hide');
                $('#addPageContentModal').removeData('id');
                $('#identifier').val('');
                if(editorInstance){
                    editorInstance.setData('<p>Write your page content here...</p>');
                }
                $('#inputStatus').prop('checked', false);
                $('#contentLanguageSelect').val('');
                $('#pages_list').DataTable().ajax.reload(null, false);
            },
            error: function(xhr){
                let err = xhr.responseJSON?.message ?? 'Something went wrong!';
                if(xhr.responseJSON?.errors){
                    let fieldErrors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    err += '<br>' + fieldErrors;
                }
                error_noti(err);
            }
        });

    });



    $(document).on('click', '.view-page', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        $.ajax({
            url: `/master-page-content/${id}`,
            type: 'GET',
            success: function (data) {
                // Title / Identifier
                $('#viewIdentifier').text(data.title || 'N/A');

                // Language
                let languageName = availableLanguages.find(lang => lang.id == data.lang_id)?.identifier || 'N/A';
                $('#viewLanguage').text(languageName);

                // Status
                $('#viewStatus').html(
                    data.status == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>'
                );

                if ($('#viewLink').length) {
                    let linkHtml = data.link
                        ? `<a href="${data.link}" target="_blank">${data.link}</a>`
                        : '<span class="text-muted">No link available.</span>';
                    $('#viewLink').html(linkHtml);
                }
                // Content / Description
                $('#viewEditorContent').html(data.description || '<p class="text-muted">No content available.</p>');

                // Show modal
                $('#viewPageContentModal').modal('show');
            },
            error: function () {
                error_noti('Could not fetch data!');
            },
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

    // 🟦 Edit Page Content
    $(document).on('click', '.edit-page', function (e) {
        e.preventDefault();
        let id = $(this).data('id');

        $.ajax({
            url: `/master-page-content/${id}`,
            type: 'GET',
            success: function (data) {
                $('#addPageContentModal').data('id', data.id);
                $('#identifier').val(data.title); 
                // Link
                $('#inputLink').val(data.link || ''); // <-- added for link field
                // Populate the language dropdown first (pass selected id if present)
                populateLanguageSelect(data.content_language_id ?? data.lang_id);
                $('#contentLanguageSelect').val(data.content_language_id ?? data.lang_id ?? '').trigger('change');
                editorInstance.setData(data.content || data.description || '<p></p>');
                $('#inputStatus').prop('checked', data.status == 1);
                editorInstance.setData(data.description || '<p></p>');
                $('#inputStatus').prop('checked', data.status == 1);

                $('#addPageContentLabel').text('Edit Page Content');
                $('#addPageContentModal').modal('show');
            },
            error: function () {
                error_noti('Could not fetch data!');
            },
        });
    });



    // 🟦 Reset modal on close
    $('#addPageContentModal').on('hidden.bs.modal', function () {
        $(this).removeData('id');
        $('#identifier').val('');
        $('#inputLink').val('');  // <-- reset link
        $('#contentLanguageSelect').val('');
        $('#inputStatus').prop('checked', false);
        if(editorInstance){
            editorInstance.setData('<p>Write your page content here...</p>');
        }
        $('#addPageContentLabel').text('Add Page Content');
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



    let positionsChanged = false;

    function makeTableSortable() {
        $('#pages_list tbody').sortable({
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

    // 🟦 Soft Delete Page Content
    $(document).on('click', '.delete-page', function () {
        let id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this page?')) return;

        $.ajax({
            url: `/master-page-content/${id}`,
            type: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                success_noti(response.message);
                $('#pages_list').DataTable().ajax.reload(null, false);

            },
            error: function (xhr) {
                error_noti(xhr.responseJSON?.message ?? 'Something went wrong!');
            }
        });
    });

    

    $(document).ready(function() {
        makeTableSortable();
    });
    </script>
    @endpush
</x-app-layout>
