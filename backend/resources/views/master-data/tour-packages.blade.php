<x-app-layout>
    @push('styles')
    <style>
        .master-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .master-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 24px;
            color: #fff;
        }
        .master-card-header h5 {
            font-size: 1.15rem;
            font-weight: 600;
            margin: 0;
        }
        .master-card-header p {
            font-size: 0.82rem;
            opacity: 0.85;
            margin: 4px 0 0;
        }
        .master-card-body { padding: 24px; }
        .master-card .table thead th {
            background: #f8f9fc;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
            padding: 12px 14px;
            white-space: nowrap;
        }
        .master-card .table tbody td {
            vertical-align: middle;
            padding: 12px 14px;
            font-size: 0.88rem;
            color: #444;
        }
        .master-card .table tbody tr { transition: background 0.2s; }
        .master-card .table tbody tr:hover { background: #f5f7ff; }
        .modal-content { border: none; border-radius: 16px; overflow: hidden; }
        .modal-header.master-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 18px 24px;
            border: none;
        }
        .modal-header.master-modal-header .modal-title {
            font-weight: 600;
            font-size: 1.05rem;
        }
        .modal-body { padding: 24px; }
        .modal-body label {
            font-weight: 600;
            font-size: 0.82rem;
            color: #555;
            margin-bottom: 4px;
            display: block;
        }
        .modal-body .form-control, .modal-body .form-select {
            border-radius: 8px;
            border: 1.5px solid #e0e3eb;
            padding: 9px 14px;
            font-size: 0.88rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .modal-body .form-control:focus, .modal-body .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.12);
        }
        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 14px 24px;
        }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 24px;
            font-weight: 600;
        }
        .modal-footer .btn-light {
            border-radius: 8px;
            border: 1.5px solid #e0e3eb;
        }
        .btn-add-new {
            background: rgba(255,255,255,0.2);
            border: 1.5px solid rgba(255,255,255,0.4);
            color: #fff;
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-add-new:hover { background: rgba(255,255,255,0.3); color: #fff; }
        .form-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #667eea;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #f0f2ff;
        }
        .img-preview-box {
            width: 100px; height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .form-check-styled { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #f8f9fc; border-radius: 8px; margin-top: 8px; }
        .form-check-styled input[type="checkbox"] { width: 18px; height: 18px; accent-color: #667eea; }
        .form-check-styled label { margin: 0; font-weight: 500; color: #444; }
    </style>
    @endpush

    <div class="master-card">
        <div class="master-card-header d-flex justify-content-between align-items-center">
            <div>
                <h5><i class="bx bx-map-alt me-2"></i>Tour Packages</h5>
                <p>Manage tour packages displayed on the website</p>
            </div>
        </div>
        <div class="master-card-body">
            <table id="dataTable" class="table table-hover mt-1">
                <thead><tr>
                    <th>Sr.</th><th>Image</th><th>Name</th><th>Duration</th><th>Price</th><th>Popular</th><th>Status</th><th>Action</th>
                </tr></thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header master-modal-header text-white">
                    <h5 class="modal-title text-white" id="modalTitle"><i class="bx bx-map-alt me-2"></i>Add Tour Package</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height:72vh;overflow-y:auto;">
                    <div class="form-section-title"><i class="bx bx-info-circle me-1"></i> Basic Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" id="f_name" class="form-control" placeholder="Enter package name" required>
                        </div>
                        <div class="col-md-6">
                            <label>Duration <span class="text-danger">*</span></label>
                            <input type="text" id="f_duration" class="form-control" placeholder="e.g. 1 Day / 3 Days - 2 Nights">
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="f_description" class="form-control" rows="3" placeholder="Brief description of the tour package..."></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-rupee me-1"></i> Pricing & Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label>Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" id="f_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label>Price Label</label>
                            <input type="text" id="f_price_label" class="form-control" value="per person">
                        </div>
                        <div class="col-md-4">
                            <label>Group Size</label>
                            <input type="text" id="f_group_size" class="form-control" placeholder="e.g. 2-10 people">
                        </div>
                        <div class="col-md-12">
                            <label>Places Covered</label>
                            <input type="text" id="f_places_covered" class="form-control" placeholder="e.g. Mathura, Vrindavan, Govardhan">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-list-check me-1"></i> Includes</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label>Package Includes <small class="text-muted">(one item per line)</small></label>
                            <textarea id="f_includes_text" class="form-control" rows="5" placeholder="Krishna Janmabhoomi Temple&#10;AC Transport & Guide&#10;Vegetarian Lunch"></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-cog me-1"></i> Settings</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Image</label>
                            <input type="file" id="f_image" class="form-control" accept="image/*">
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check-styled">
                                <input type="checkbox" id="f_is_popular">
                                <label for="f_is_popular">Most Popular</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check-styled">
                                <input type="checkbox" id="f_is_active" checked>
                                <label for="f_is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Sort Order</label>
                            <input type="number" id="f_sort_order" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="submitBtn" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Package</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        let table = initDataTable({
            selector: "#dataTable", ajaxUrl: "{{ route('master.tour-packages.ajax') }}",
            moduleName: "Add Tour Package", modalSelector: "#formModal",
            columns: [
                { data: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                { data: 'image_display', filter: 'none', orderable: false },
                { data: 'name', filter: 'text' },
                { data: 'duration', filter: 'text' },
                { data: 'price_display', filter: 'none' },
                { data: 'popular', filter: 'none' },
                { data: 'status', filter: 'select', options: [{value:'',label:'All'},{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}] },
                { data: 'action', filter: 'none' }
            ],
            columnDefs: [{ targets: 0, width: '60px', className: 'text-center' }, { targets: -1, width: '80px', className: 'text-center' }]
        });
    });

    const fields = ['name','duration','price','price_label','group_size','places_covered','description','includes_text','is_popular','is_active','sort_order'];
    const storeUrl = "{{ route('master.tour-packages.store') }}";
    const updateUrl = "{{ route('master.tour-packages.update', ':id') }}";
    const showUrl = "{{ route('master.tour-packages.show', ':id') }}";
    const deleteUrl = "{{ route('master.tour-packages.destroy', ':id') }}";
    const editClass = 'edit-tour-package';
    const deleteClass = 'delete-tour-package';
    const modalTitle = 'Tour Package';

    @include('master-data._shared_js')
    </script>
    @endpush
</x-app-layout>
