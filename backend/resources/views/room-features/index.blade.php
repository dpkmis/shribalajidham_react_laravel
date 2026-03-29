<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                overflow-y: auto;
            }
            .icon-preview {
                font-size: 2rem;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                display: inline-block;
                min-width: 60px;
                text-align: center;
            }
            .icon-selector {
                max-height: 300px;
                overflow-y: auto;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 4px;
            }
            .icon-option {
                padding: 8px;
                cursor: pointer;
                border-radius: 4px;
                display: inline-block;
                margin: 3px;
            }
            .icon-option:hover {
                background-color: #f0f0f0;
            }
            .icon-option.selected {
                background-color: #0d6efd;
                color: white;
            }
        </style>
    @endpush

    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table id="featuresTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Created on</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Room Feature Modal -->
    <div class="modal fade custom-modal" id="featureModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="featureModalLabel">Add Room Feature</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Property -->
                        <div class="col-md-12">
                            <label>Property</label>
                            <select id="featureProperty" class="form-control">
                                <option value="">Global Feature</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty for global features available to all properties</small>
                        </div>

                        <!-- Feature Name -->
                        <div class="col-md-12">
                            <label>Feature Name <span class="text-danger">*</span></label>
                            <input type="text" id="featureName" class="form-control" placeholder="Free WiFi, Air Conditioning, etc.">
                            <small class="text-danger d-none" id="featureNameError">Feature name is required</small>
                        </div>

                        <!-- Code -->
                        <div class="col-md-6">
                            <label>Code</label>
                            <input type="text" id="featureCode" class="form-control" placeholder="WIFI, AC, etc.">
                            <small class="text-muted">Unique identifier (auto-generated if empty)</small>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <label>Sort Order</label>
                            <input type="number" id="featureSortOrder" class="form-control" value="0" min="0">
                            <small class="text-muted">Display order (0 = first)</small>
                        </div>

                        <!-- Icon Selection -->
                        <div class="col-md-12">
                            <label>Icon <span class="text-muted">(Optional)</span></label>
                            <div class="mb-2">
                                <div class="icon-preview" id="iconPreview">
                                    <i class="bx bx-question-mark"></i>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="selectIconBtn">Select Icon</button>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="clearIconBtn">Clear</button>
                            </div>
                            <input type="hidden" id="featureIcon">
                            
                            <!-- Icon Selector (Hidden by default) -->
                            <div class="icon-selector d-none" id="iconSelector">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <input type="text" class="form-control form-control-sm" id="iconSearch" placeholder="Search icons...">
                                    </div>
                                </div>
                                <div id="iconGrid">
                                    <!-- Popular icons -->
                                    <div class="icon-option" data-icon="bx bx-wifi" title="WiFi"><i class="bx bx-wifi"></i></div>
                                    <div class="icon-option" data-icon="bx bx-wind" title="AC"><i class="bx bx-wind"></i></div>
                                    <div class="icon-option" data-icon="bx bx-tv" title="TV"><i class="bx bx-tv"></i></div>
                                    <div class="icon-option" data-icon="bx bx-bath" title="Bathtub"><i class="bx bx-bath"></i></div>
                                    <div class="icon-option" data-icon="bx bx-shower" title="Shower"><i class="bx bx-shower"></i></div>
                                    <div class="icon-option" data-icon="bx bx-drink" title="Mini Bar"><i class="bx bx-drink"></i></div>
                                    <div class="icon-option" data-icon="bx bx-coffee" title="Coffee"><i class="bx bx-coffee"></i></div>
                                    <div class="icon-option" data-icon="bx bx-restaurant" title="Restaurant"><i class="bx bx-restaurant"></i></div>
                                    <div class="icon-option" data-icon="bx bx-bed" title="Bed"><i class="bx bx-bed"></i></div>
                                    <div class="icon-option" data-icon="bx bx-lock" title="Safe"><i class="bx bx-lock"></i></div>
                                    <div class="icon-option" data-icon="bx bx-lock-alt" title="Lock"><i class="bx bx-lock-alt"></i></div>
                                    <div class="icon-option" data-icon="bx bx-key" title="Key"><i class="bx bx-key"></i></div>
                                    <div class="icon-option" data-icon="bx bx-door-open" title="Door"><i class="bx bx-door-open"></i></div>
                                    <div class="icon-option" data-icon="bx bx-home" title="Home"><i class="bx bx-home"></i></div>
                                    <div class="icon-option" data-icon="bx bx-building" title="Building"><i class="bx bx-building"></i></div>
                                    <div class="icon-option" data-icon="bx bx-water" title="Water"><i class="bx bx-water"></i></div>
                                    <div class="icon-option" data-icon="bx bx-swim" title="Pool"><i class="bx bx-swim"></i></div>
                                    <div class="icon-option" data-icon="bx bx-dumbbell" title="Gym"><i class="bx bx-dumbbell"></i></div>
                                    <div class="icon-option" data-icon="bx bx-parking" title="Parking"><i class="bx bx-parking"></i></div>
                                    <div class="icon-option" data-icon="bx bx-car" title="Car"><i class="bx bx-car"></i></div>
                                    <div class="icon-option" data-icon="bx bx-taxi" title="Taxi"><i class="bx bx-taxi"></i></div>
                                    <div class="icon-option" data-icon="bx bx-bus" title="Bus"><i class="bx bx-bus"></i></div>
                                    <div class="icon-option" data-icon="bx bx-phone" title="Phone"><i class="bx bx-phone"></i></div>
                                    <div class="icon-option" data-icon="bx bx-desktop" title="Desktop"><i class="bx bx-desktop"></i></div>
                                    <div class="icon-option" data-icon="bx bx-laptop" title="Laptop"><i class="bx bx-laptop"></i></div>
                                    <div class="icon-option" data-icon="bx bx-handicap" title="Accessible"><i class="bx bx-handicap"></i></div>
                                    <div class="icon-option" data-icon="bx bx-first-aid" title="First Aid"><i class="bx bx-first-aid"></i></div>
                                    <div class="icon-option" data-icon="bx bx-smoke" title="Smoking"><i class="bx bx-smoke"></i></div>
                                    <div class="icon-option" data-icon="bx bx-no-smoking" title="No Smoking"><i class="bx bx-no-smoking"></i></div>
                                    <div class="icon-option" data-icon="bx bx-sun" title="Sunny"><i class="bx bx-sun"></i></div>
                                    <div class="icon-option" data-icon="bx bx-moon" title="Moon"><i class="bx bx-moon"></i></div>
                                    <div class="icon-option" data-icon="bx bx-star" title="Star"><i class="bx bx-star"></i></div>
                                    <div class="icon-option" data-icon="bx bx-heart" title="Heart"><i class="bx bx-heart"></i></div>
                                    <div class="icon-option" data-icon="bx bx-camera" title="Camera"><i class="bx bx-camera"></i></div>
                                    <div class="icon-option" data-icon="bx bx-video" title="Video"><i class="bx bx-video"></i></div>
                                    <div class="icon-option" data-icon="bx bx-music" title="Music"><i class="bx bx-music"></i></div>
                                    <div class="icon-option" data-icon="bx bx-volume-full" title="Sound"><i class="bx bx-volume-full"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="featureDescription" class="form-control" rows="3" placeholder="Brief description of this feature..."></textarea>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-12">
                            <label class="d-block">
                                <input type="checkbox" id="featureIsActive" checked> Active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitFeatureBtn" class="btn btn-primary">Save Feature</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @if(session('notify'))
            <script>
                alert_box('success', "{{ session('notify') }}");
            </script>
        @endif

        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#featuresTable",
                    ajaxUrl: "{{ route('room-features.ajax') }}",
                    moduleName: "Add Feature",
                    modalSelector: "#featureModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'icon_display', filter: 'none' },
                        { data: 'name', filter: 'text' },
                        { data: 'code', filter: 'text' },
                        { data: 'status', filter: 'select', options: [
                            { value: "", label: "All" },
                            { value: "active", label: "Active" },
                            { value: "inactive", label: "Inactive" }
                        ]},
                        { data: 'created_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: [2, 5], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });
            });
        </script>

        <!-- Icon Selection Logic -->
        <script>
            // Show/Hide icon selector
            $('#selectIconBtn').on('click', function() {
                $('#iconSelector').toggleClass('d-none');
            });

            // Icon selection
            $(document).on('click', '.icon-option', function() {
                $('.icon-option').removeClass('selected');
                $(this).addClass('selected');
                
                let iconClass = $(this).data('icon');
                $('#featureIcon').val(iconClass);
                $('#iconPreview').html('<i class="' + iconClass + '"></i>');
            });

            // Clear icon
            $('#clearIconBtn').on('click', function() {
                $('#featureIcon').val('');
                $('#iconPreview').html('<i class="bx bx-question-mark"></i>');
                $('.icon-option').removeClass('selected');
            });

            // Icon search
            $('#iconSearch').on('keyup', function() {
                let searchTerm = $(this).val().toLowerCase();
                $('.icon-option').each(function() {
                    let title = $(this).attr('title').toLowerCase();
                    if (title.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        </script>

        <!-- Submit Feature -->
        <script>
            $('#submitFeatureBtn').on('click', function () {
                let id = $('#featureModal').data('id');
                
                // Clear previous errors
                $('.text-danger').addClass('d-none');

                // Get values
                let propertyId = $('#featureProperty').val() || null;
                let name = $('#featureName').val().trim();
                let code = $('#featureCode').val().trim();
                let icon = $('#featureIcon').val();
                let description = $('#featureDescription').val().trim();
                let sortOrder = $('#featureSortOrder').val();
                let isActive = $('#featureIsActive').is(':checked') ? 1 : 0;

                // Frontend Validation
                let isValid = true;
                if (!name) {
                    $('#featureNameError').removeClass('d-none');
                    error_noti('Feature name is required');
                    isValid = false;
                }

                if (!isValid) return;

                let payload = {
                    _token: "{{ csrf_token() }}",
                    _method: id ? 'PUT' : 'POST',
                    property_id: propertyId,
                    name: name,
                    code: code || null,
                    icon: icon || null,
                    description: description || null,
                    sort_order: sortOrder,
                    is_active: isActive
                };

                let url = id
                    ? "{{ route('room-features.update', ':id') }}".replace(':id', id)
                    : "{{ route('room-features.store') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#featureModal').modal('hide');
                        resetFeatureModal();
                        $('#featuresTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save feature';
                        if (xhr.responseJSON?.errors) {
                            let errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- Edit Feature -->
        <script>
            $(document).on('click', '.edit-feature', function () {
                let id = $(this).data('id');
                let url = "{{ route('room-features.show', ':id') }}".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#featureProperty').val(data.property_id || '');
                        $('#featureName').val(data.name);
                        $('#featureCode').val(data.code || '');
                        $('#featureIcon').val(data.icon || '');
                        $('#featureDescription').val(data.description || '');
                        $('#featureSortOrder').val(data.sort_order);
                        $('#featureIsActive').prop('checked', data.is_active);

                        // Update icon preview
                        if (data.icon) {
                            $('#iconPreview').html('<i class="' + data.icon + '"></i>');
                            $('.icon-option[data-icon="' + data.icon + '"]').addClass('selected');
                        } else {
                            $('#iconPreview').html('<i class="bx bx-question-mark"></i>');
                        }

                        $('#featureModal')
                            .data('id', data.id)
                            .modal('show');

                        $('#featureModalLabel').text('Edit Room Feature');
                    },
                    error: function () {
                        error_noti('Unable to load feature details');
                    }
                });
            });
        </script>

        <!-- Delete Feature -->
        <script>
            $(document).on('click', '.delete-feature', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This feature will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "{{ route('room-features.destroy', ':id') }}".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#featuresTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: xhr.responseJSON?.message ?? 'Failed to delete feature'
                            });
                        }
                    });
                });
            });
        </script>

        <!-- Reset Modal -->
        <script>
            function resetFeatureModal() {
                $('#featureProperty').val('');
                $('#featureName').val('');
                $('#featureCode').val('');
                $('#featureIcon').val('');
                $('#featureDescription').val('');
                $('#featureSortOrder').val('0');
                $('#featureIsActive').prop('checked', true);
                $('#iconPreview').html('<i class="bx bx-question-mark"></i>');
                $('.icon-option').removeClass('selected');
                $('#iconSelector').addClass('d-none');
                $('#iconSearch').val('');
                $('.icon-option').show();
                
                $('.text-danger').addClass('d-none');
                $('#featureModal').removeData('id');
                $('#featureModalLabel').text('Add Room Feature');
            }

            $('#featureModal').on('hidden.bs.modal', resetFeatureModal);
        </script>
    @endpush
</x-app-layout>