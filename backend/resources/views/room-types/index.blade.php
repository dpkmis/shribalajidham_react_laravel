<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                overflow-y: auto;
            }
        </style>
    @endpush

    <div class="card p-4">
        <table id="roomTypesTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Default Rate</th>
                    <th>Occupancy</th>
                    <th>Rooms</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Room Type Modal -->
    <div class="modal fade custom-modal" id="roomTypeModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="roomTypeModalLabel">Add Room Type</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Property</label>
                            <select id="typeProperty" class="form-control">
                                <option value="">Global Type</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" id="typeName" class="form-control" placeholder="Deluxe Room, Suite...">
                            <small class="text-danger d-none" id="typeNameError">Name is required</small>
                        </div>

                        <div class="col-md-6">
                            <label>Code</label>
                            <input type="text" id="typeCode" class="form-control" placeholder="DLX, STE...">
                        </div>

                        <div class="col-md-6">
                            <label>Default Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" id="defaultRate" class="form-control" step="0.01" min="0" placeholder="5000.00">
                            <small class="text-danger d-none" id="defaultRateError">Rate is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Max Occupancy <span class="text-danger">*</span></label>
                            <input type="number" id="maxOccupancy" class="form-control" min="1" max="20" value="2">
                        </div>

                        <div class="col-md-4">
                            <label>Max Adults <span class="text-danger">*</span></label>
                            <input type="number" id="maxAdults" class="form-control" min="1" max="20" value="2">
                        </div>

                        <div class="col-md-4">
                            <label>Max Children</label>
                            <input type="number" id="maxChildren" class="form-control" min="0" max="10" value="0">
                        </div>

                        <div class="col-md-4">
                            <label>Number of Beds <span class="text-danger">*</span></label>
                            <input type="number" id="beds" class="form-control" min="1" max="10" value="1">
                        </div>

                        <div class="col-md-4">
                            <label>Bed Type <span class="text-danger">*</span></label>
                            <select id="bedType" class="form-control">
                                <option value="single">Single</option>
                                <option value="double" selected>Double</option>
                                <option value="queen">Queen</option>
                                <option value="king">King</option>
                                <option value="twin">Twin</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Room Size (sqm)</label>
                            <input type="number" id="roomSize" class="form-control" step="0.01" min="0" placeholder="25.50">
                        </div>

                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="typeDescription" class="form-control" rows="3" placeholder="Room type description..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Sort Order</label>
                            <input type="number" id="sortOrder" class="form-control" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="d-block mt-4">
                                <input type="checkbox" id="isActive" checked> Active
                            </label>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h6>Room Images <small class="text-muted">(Max 5 images, JPEG/PNG/WebP, max 3MB each)</small></h6>
                            <input type="file" id="roomImages" class="form-control" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
                            <div id="existingImagesContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
                            <input type="hidden" id="removeImages" value="">
                        </div>

                        <div class="col-12">
                            <hr>
                            <h6>Room Features</h6>
                            <div class="row">
                                @foreach($features as $feature)
                                <div class="col-md-3">
                                    <label class="d-block">
                                        <input type="checkbox" class="room-type-feature" value="{{ $feature->id }}">
                                        @if($feature->icon) <i class="{{ $feature->icon }}"></i> @endif
                                        {{ $feature->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitTypeBtn" class="btn btn-primary">Save Type</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#roomTypesTable",
                    ajaxUrl: "{{ route('room-types.ajax') }}",
                    moduleName: "Add Room Type",
                    modalSelector: "#roomTypeModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'name', filter: 'text' },
                        { data: 'code', filter: 'text' },
                        { data: 'default_rate_display', filter: 'none' },
                        { data: 'occupancy_display', filter: 'none' },
                        { data: 'room_count', filter: 'none' },                      
                        { data: 'status', filter: 'select', options: [
                            { value: "", label: "All" },
                            { value: "active", label: "Active" },
                            { value: "inactive", label: "Inactive" }
                        ]},                                                
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "80px", className: "text-center" },
                        { targets: [4, 5, 6, 7], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });
            });
        </script>

        <script>
            // Image preview on file select
            $('#roomImages').on('change', function() {
                let container = $('#imagePreviewContainer');
                container.empty();
                let files = this.files;
                if (files.length > 5) {
                    error_noti('Maximum 5 images allowed');
                    this.value = '';
                    return;
                }
                for (let i = 0; i < files.length; i++) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        container.append(`
                            <div class="position-relative" style="width:100px;height:100px;">
                                <img src="${e.target.result}" class="rounded border" style="width:100px;height:100px;object-fit:cover;">
                                <span class="badge bg-info position-absolute top-0 start-0">New</span>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(files[i]);
                }
            });

            // Remove existing image
            $(document).on('click', '.remove-existing-img', function() {
                let idx = $(this).data('idx');
                $(this).closest('.existing-img-wrap').hide();
                let current = $('#removeImages').val();
                let arr = current ? current.split(',') : [];
                arr.push(idx);
                $('#removeImages').val(arr.join(','));
            });

            $('#submitTypeBtn').on('click', function () {
                let id = $('#roomTypeModal').data('id');
                $('.text-danger').addClass('d-none');

                let name = $('#typeName').val().trim();
                let defaultRate = $('#defaultRate').val();

                // Validation
                let isValid = true;
                if (!name) {
                    $('#typeNameError').removeClass('d-none');
                    isValid = false;
                }
                if (!defaultRate || defaultRate <= 0) {
                    $('#defaultRateError').removeClass('d-none');
                    isValid = false;
                }
                if (!isValid) {
                    error_noti('Please fill all required fields');
                    return;
                }

                // Use FormData for file upload support
                let formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                if (id) formData.append('_method', 'PUT');
                formData.append('property_id', $('#typeProperty').val() || '');
                formData.append('name', name);
                formData.append('code', $('#typeCode').val().trim() || '');
                formData.append('default_rate', defaultRate);
                formData.append('max_occupancy', $('#maxOccupancy').val());
                formData.append('max_adults', $('#maxAdults').val());
                formData.append('max_children', $('#maxChildren').val());
                formData.append('beds', $('#beds').val());
                formData.append('bed_type', $('#bedType').val());
                formData.append('room_size_sqm', $('#roomSize').val() || '');
                formData.append('description', $('#typeDescription').val().trim() || '');
                formData.append('sort_order', $('#sortOrder').val());
                formData.append('is_active', $('#isActive').is(':checked') ? 1 : 0);

                // Features
                $('.room-type-feature:checked').each(function () {
                    formData.append('features[]', $(this).val());
                });

                // Images
                let imageFiles = $('#roomImages')[0].files;
                for (let i = 0; i < imageFiles.length; i++) {
                    formData.append('room_images[]', imageFiles[i]);
                }

                // Images to remove (on edit)
                if ($('#removeImages').val()) {
                    formData.append('remove_images', $('#removeImages').val());
                }

                let url = id
                    ? "{{ route('room-types.update', ':id') }}".replace(':id', id)
                    : "{{ route('room-types.store') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        success_noti(res.message);
                        $('#roomTypeModal').modal('hide');
                        resetTypeModal();
                        $('#roomTypesTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to save room type');
                    }
                });
            });

            $(document).on('click', '.edit-room-type', function () {
                let id = $(this).data('id');
                let url = "{{ route('room-types.show', ':id') }}".replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#typeProperty').val(data.property_id || '');
                        $('#typeName').val(data.name);
                        $('#typeCode').val(data.code || '');
                        $('#defaultRate').val(data.default_rate_display);
                        $('#maxOccupancy').val(data.max_occupancy);
                        $('#maxAdults').val(data.max_adults);
                        $('#maxChildren').val(data.max_children);
                        $('#beds').val(data.beds);
                        $('#bedType').val(data.bed_type);
                        $('#roomSize').val(data.room_size_sqm || '');
                        $('#typeDescription').val(data.description || '');
                        $('#sortOrder').val(data.sort_order);
                        $('#isActive').prop('checked', data.is_active);

                        $('.room-type-feature').prop('checked', false);
                        if (data.features) {
                            data.features.forEach(f => {
                                $('.room-type-feature[value="' + f.id + '"]').prop('checked', true);
                            });
                        }

                        // Show existing images
                        let existingContainer = $('#existingImagesContainer');
                        existingContainer.empty();
                        if (data.images && data.images.length > 0) {
                            data.images.forEach((img, idx) => {
                                existingContainer.append(`
                                    <div class="existing-img-wrap position-relative" style="width:100px;height:100px;">
                                        <img src="${img}" class="rounded border" style="width:100px;height:100px;object-fit:cover;">
                                        <button type="button" class="btn btn-danger btn-sm remove-existing-img position-absolute top-0 end-0" data-idx="${idx}" style="padding:1px 5px;font-size:10px;line-height:1;">×</button>
                                    </div>
                                `);
                            });
                        }

                        $('#roomTypeModal').data('id', data.id).modal('show');
                        $('#roomTypeModalLabel').text('Edit Room Type');
                    },
                    error: function () {
                        error_noti('Unable to load room type');
                    }
                });
            });

            $(document).on('click', '.delete-room-type', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This room type will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "{{ route('room-types.destroy', ':id') }}".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: { _token: "{{ csrf_token() }}", _method: "DELETE" },
                        success: function (res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#roomTypesTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message ?? 'Failed to delete', 'error');
                        }
                    });
                });
            });

            function resetTypeModal() {
                $('#typeProperty').val('');
                $('#typeName').val('');
                $('#typeCode').val('');
                $('#defaultRate').val('');
                $('#maxOccupancy').val('2');
                $('#maxAdults').val('2');
                $('#maxChildren').val('0');
                $('#beds').val('1');
                $('#bedType').val('double');
                $('#roomSize').val('');
                $('#typeDescription').val('');
                $('#sortOrder').val('0');
                $('#isActive').prop('checked', true);
                $('.room-type-feature').prop('checked', false);
                $('.text-danger').addClass('d-none');
                $('#roomImages').val('');
                $('#imagePreviewContainer').empty();
                $('#existingImagesContainer').empty();
                $('#removeImages').val('');
                $('#roomTypeModal').removeData('id');
                $('#roomTypeModalLabel').text('Add Room Type');
            }

            $('#roomTypeModal').on('hidden.bs.modal', resetTypeModal);
        </script>
    @endpush
</x-app-layout>