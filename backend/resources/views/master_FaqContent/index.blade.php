<x-app-layout>
    <style>
        /* Make modal body scrollable */
        .custom-modal .modal-body {
            height: 80vh; /* Adjust as needed */
            overflow-y: auto;
        }
    </style>
    @push('style')
    
    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <!-- FAQ Content Table -->
        <table id="faq_content_list" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>FAQ Category</th>
                    <th>Display Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>

    </div>
    
    <!-- Add/Edit FAQ Content Modal -->
    <div class="modal fade custom-modal" id="addFaqContentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Add/Edit FAQ Content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="faqContentForm">
                    @csrf
                    <input type="hidden" id="faq_id" name="id">

                    <!-- Scrollable Body -->
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Category -->
                            <div class="col-12">
                                <label for="category_id" class="form-label">Category</label>
                                <select id="category_id" name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Display Name -->
                            <div class="col-12">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" id="display_name" name="display_name" placeholder="Enter display name" class="form-control" required>
                            </div>

                            <!-- Status -->
                            <div class="col-6 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" id="status" name="status" value="0" class="form-check-input">
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>

                        

                        <!-- Dynamic Q&A Container -->
                        <div id="qaContainer" class="mt-3"></div>
                        <!-- Language Selector + Add Button in One Group -->
                        <div class="input-group mt-3">
                            <label class="input-group-text" for="language_selector">Select Language</label>
                            <select id="language_selector" class="form-select">
                                <option value="">-- Select Language --</option>
                                @foreach($languages as $lang)
                                    <option value="{{ $lang->identifier }}">{{ $lang->identifier }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="addQA" class="btn btn-outline-primary">
                                <i class="bx bx-plus"></i> Add Q&A
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="saveFaqBtn" class="btn btn-primary px-4">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Q&A Template -->
    <div id="qaTemplate" class="d-none">
        <div class="qa-item border p-3 mb-3">
            <div class="row g-3 align-items-end">
                <!-- Question -->
                <div class="col-md-6">
                    <label class="form-label">Question</label>
                    <input type="text" name="title[]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Question Language</label>
                    <select name="title_lang[]" class="form-select" required>
                        <option value="">Select Language</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->identifier }}">{{ $lang->identifier }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Answer -->
                <div class="col-md-6">
                    <label class="form-label">Answer</label>
                    <textarea name="description[]" class="form-control" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Answer Language</label>
                    <select name="desc_lang[]" class="form-select" required>
                        <option value="">Select Language</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->identifier }}">{{ $lang->identifier }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Remove Button -->
                <div class="col-12 text-end mt-2">
                    <button type="button" class="btn btn-danger removeQA">&times;</button>
                </div>
            </div>
        </div>
    </div>



    <!-- View FAQ Content Modal -->
    <div class="modal fade custom-modal" id="viewFaqContentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">View FAQ Content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-12">
                            <label class="form-label">Category</label>
                            <input type="text" id="view_category" class="form-control" readonly>
                        </div>

                        <!-- Display Name -->
                        <div class="col-12">
                            <label class="form-label">Display Name</label>
                            <input type="text" id="view_display_name" class="form-control" readonly>
                        </div>

                        <!-- Status -->
                        <div class="col-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" id="view_status" class="form-check-input" disabled>
                                <label class="form-check-label" for="view_status">Active</label>
                            </div>
                        </div>

                        <!-- Q&A Container -->
                        <div id="viewQaContainer" class="mt-3"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Q&A Template for View Modal -->
    <div id="viewQaTemplate" class="d-none">
        <div class="card mb-3 qa-item" data-lang="">
            <div class="card-header fw-bold text-white bg-primary">
                <!-- Language will be set dynamically -->
                <span class="qa-language"></span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Question</label>
                    <input type="text" name="view_title[]" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Answer</label>
                    <textarea name="view_description[]" class="form-control" rows="2" readonly></textarea>
                </div>
            </div>
        </div>
    </div>




    @push('scripts')
    <script type="text/javascript">
    $(document).ready(function(){

        // Initialize FAQ DataTable
        let table = initDataTable({
            selector: "#faq_content_list",
            ajaxUrl: "{{ route('master-faq_content.get_faqContent') }}",
            moduleName: "Add FAQ Content",
            modalSelector: "#addFaqContentModal",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex',filter: 'none', orderable: false, searchable: false },
                { data: 'category_name', filter: 'select', options: [
                    {value:"", label:"All"},
                    @foreach($categories as $cat)
                    {value:"{{ $cat->id }}", label:"{{ $cat->category_name }}"},
                    @endforeach
                ]},
                { data: 'display_name', filter: 'text' },
                { data: 'status', filter: 'select', options: [
                    {value:"", label:"All"},
                    {value:"0", label:"Active"},
                    {value:"1", label:"Inactive"},
                    {value:"2", label:"Deleted"}
                ]},
                { data: 'action', filter: 'none' }
            ],
            createdRow: function(row, data){
                // category FAQ
                let category = data.category_name && data.category_name.trim() !== ''
                    ? data.category_name
                    : 'Unknown FAQ Category';
                $('td', row).eq(1).html(category);
                // Status badge
                let statusBadge = data.status == 0
                    ? '<span class="badge bg-success">Active</span>'
                    : data.status == 2
                        ? '<span class="badge bg-secondary">Deleted</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                $('td', row).eq(3).html(statusBadge);
                $('td', row).eq(4).addClass('text-center');
            }
        });

        // Open Add Modal
        $('#addFaqContentModalBtn').click(function(){
            $('#faqContentForm')[0].reset();
            $('#qaContainer').empty();
            addQA(); // add one QA by default
            $('#addFaqContentModal').modal('show');
        });
        
        // Function to add Q&A block
        function addQA(lang = null, question = '', answer = '') {
            let selectedLang = lang || $('#language_selector').val();
            let fromDropdown = !lang;
            if (!selectedLang && fromDropdown) {
                error_noti('language not found!');
                return;
            }

            if (fromDropdown && $('#qaContainer .qa-item[data-lang="' + selectedLang + '"]').length > 0) {
                error_noti(selectedLang + ' has already been added!');
                return;
            }

            let qaIndex = $('#qaContainer .qa-item').length + 1;

            let qaBlock = `
                <div class="qa-item border rounded p-3 mb-3" ${selectedLang ? `data-lang="${selectedLang}"` : ''}>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">${selectedLang || 'Language not in dropdown'} - Question & Answer #${qaIndex}</h6>
                        <button type="button" class="btn btn-sm btn-danger removeQA">Remove</button>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <label class="form-label">${selectedLang || 'Question'} Question</label>
                            <input type="text" name="title[${selectedLang || ''}]" class="form-control" placeholder="Enter Question" value="${question}">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label">${selectedLang || 'Answer'} Answer</label>
                            <textarea name="description[${selectedLang || ''}]" class="form-control" placeholder="Enter Answer">${answer}</textarea>
                        </div>
                    </div>
                </div>
            `;

            $('#qaContainer').append(qaBlock);

            if (fromDropdown) {
                $('#language_selector option[value="' + selectedLang + '"]').remove();
                $('#language_selector').val('');
            }
        }

        $('#addQA').on('click', function () {
            addQA();
        });

        $(document).on('click', '.removeQA', function () {
            let lang = $(this).closest('.qa-item').data('lang');
            $('#language_selector').append(`<option value="${lang}">${lang}</option>`);
            $(this).closest('.qa-item').remove();
        });

        $('#faqContentForm').submit(function(e){
            e.preventDefault();
            let formData = $(this).serialize();
            let id = $('#faq_id').val();
            let url = id
                ? "/master-faq_content/" + id
                : "{{ route('master-faq_content.store') }}";
            let type = id ? "PUT" : "POST";

            $.ajax({
                url: url,
                type: type,
                data: formData,
                success: function(response){
                    success_noti(response.message || 'Saved successfully!');
                    $('#addFaqContentModal').modal('hide');
                    $('#faqContentForm')[0].reset();
                    $('#faq_id').val('');
                    $('#qaContainer').empty();
                    table.ajax.reload(null, false);
                },
                error: function(xhr){
                    error_noti(xhr.responseJSON?.message ?? 'Something went wrong!');
                }
            });
        });

        $(document).on('click', '.edit-faq', function(e){
            e.preventDefault();
            let id = $(this).data('id');
            $.ajax({
                url: '/master-faq_content/' + id,
                type: 'GET',
                success: function(data){
                    $('#faq_id').val(data.id);
                    $('#display_name').val(data.display_name);
                    $('#category_id').val(data.category_id);
                    $('#status').prop('checked', data.status == 0); // 0 = Active
                    $('#is_filterable').prop('checked', data.is_filterable == 1);
                    $('#qaContainer').empty();

                    if(Array.isArray(data.faq_content) && data.faq_content.length > 0){
                        data.faq_content.forEach(item => {
                            addQA(item.title_lang, item.title, item.description);
                        });

                    } else {
                        addQA();
                    }

                    $('#addFaqContentModal').modal('show');
                },
                error: function(xhr){
                    error_noti('Could not fetch data!');
                }
            });
        });

        $(document).on('click', '.view-faq', function(e) {
            e.preventDefault();
            let faqId = $(this).data('id');

            $.ajax({
                url: '/master-faq_content/' + faqId, 
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const categories = @json($categories); 
                    let categoryName = 'Unknown';
                    if (data.category_id) {
                        let category = categories.find(cat => cat.id == data.category_id);
                        if (category) categoryName = category.category_name;
                    }
                    $('#view_category').val(categoryName);

                    $('#view_display_name').val(data.display_name);
                    $('#view_status').prop('checked', data.status == 0); // 0 = Active

                    $('#viewQaContainer').empty();

                    if (Array.isArray(data.faq_content) && data.faq_content.length > 0) {
                        data.faq_content.forEach(function(item) {
                            let qaClone = $('#viewQaTemplate .qa-item').clone();

                            qaClone.attr('data-lang', item.title_lang || 'N/A');
                            qaClone.find('.qa-language').text(item.title_lang || 'N/A');

                            qaClone.find('input[name="view_title[]"]').val(item.title || '');
                            qaClone.find('textarea[name="view_description[]"]').val(item.description || '');

                            $('#viewQaContainer').append(qaClone);
                        });
                    } else {
                        $('#viewQaContainer').append('<p>No Q&A found.</p>');
                    }
                    $('#viewFaqContentModal').modal('show');
                },
                error: function(xhr) {
                    alert('Failed to fetch FAQ details.');
                }
            });
        });

        $('#faq_content_list').on('click', '.delete-faq_content', function () {
            let categoryId = $(this).data('id');

            if (confirm('Are you sure you want to delete this FAQ Content?')) {
                $.ajax({
                    url: '/master-faq_content/' + categoryId, // your route for delete
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        success_noti(response.message); // your success notification function
                        $('#faq_content_list').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message || 'Something went wrong!');
                    }
                });
            }
        });

        // Reset modal when closed
        $('#addFaqContentModal').on('hidden.bs.modal', function () {
            $('#faqContentForm')[0].reset();
            $('#qaContainer').empty();
            $('#faq_id').val('');

            const languages = @json($languages);
            let $languageSelect = $('#language_selector');
            $languageSelect.empty().append('<option value="">-- Select Language --</option>');
            languages.forEach(lang => {
                $languageSelect.append(`<option value="${lang.identifier}">${lang.identifier}</option>`);
            });
        });

    });
    </script>
    @endpush


</x-app-layout>
