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
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
            padding: 20px 24px;
            color: #fff;
        }
        .master-card-header h5 { font-size: 1.15rem; font-weight: 600; margin: 0; }
        .master-card-header p { font-size: 0.82rem; opacity: 0.85; margin: 4px 0 0; }
        .master-card-body { padding: 24px; }
        .master-card .table thead th {
            background: #f8f9fc; border-bottom: 2px solid #e9ecef; font-weight: 600;
            font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #555;
            padding: 12px 14px; white-space: nowrap;
        }
        .master-card .table tbody td { vertical-align: middle; padding: 12px 14px; font-size: 0.88rem; color: #444; }
        .master-card .table tbody tr { transition: background 0.2s; }
        .master-card .table tbody tr:hover { background: #f2faff; }
        .modal-content { border: none; border-radius: 16px; overflow: hidden; }
        .modal-header.master-modal-header {
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
            padding: 18px 24px; border: none;
        }
        .modal-header.master-modal-header .modal-title { font-weight: 600; font-size: 1.05rem; }
        .modal-body { padding: 24px; }
        .modal-body label { font-weight: 600; font-size: 0.82rem; color: #555; margin-bottom: 4px; display: block; }
        .modal-body .form-control, .modal-body .form-select {
            border-radius: 8px; border: 1.5px solid #e0e3eb; padding: 9px 14px; font-size: 0.88rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .modal-body .form-control:focus, .modal-body .form-select:focus {
            border-color: #66a6ff; box-shadow: 0 0 0 3px rgba(102,166,255,0.15);
        }
        .modal-footer { border-top: 1px solid #f0f0f0; padding: 14px 24px; }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
            border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600; color: #fff;
        }
        .modal-footer .btn-light { border-radius: 8px; border: 1.5px solid #e0e3eb; }
        .form-section-title {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: #4a90d9; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #e8f2ff;
        }
        .form-check-styled { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #f8f9fc; border-radius: 8px; margin-top: 8px; }
        .form-check-styled input[type="checkbox"] { width: 18px; height: 18px; accent-color: #66a6ff; }
        .form-check-styled label { margin: 0; font-weight: 500; color: #444; }
        .upload-zone {
            border: 2px dashed #c8d6e5;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            background: #fafbfd;
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }
        .upload-zone:hover { border-color: #66a6ff; background: #f0f7ff; }
        .upload-zone i { font-size: 2rem; color: #66a6ff; margin-bottom: 8px; }
        .upload-zone p { margin: 0; font-size: 0.85rem; color: #888; }
    </style>
    @endpush

    <div class="master-card">
        <div class="master-card-header d-flex justify-content-between align-items-center">
            <div>
                <h5><i class="bx bx-images me-2"></i>Gallery</h5>
                <p>Manage photo gallery images for the website</p>
            </div>
        </div>
        <div class="master-card-body">
            <table id="dataTable" class="table table-hover mt-1">
                <thead><tr><th>Sr.</th><th>Image</th><th>Title</th><th>Caption</th><th>Category</th><th>Status</th><th>Action</th></tr></thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header master-modal-header text-white">
                    <h5 class="modal-title text-white" id="modalTitle"><i class="bx bx-images me-2"></i>Add Gallery Image</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height:72vh;overflow-y:auto;">
                    <div class="form-section-title"><i class="bx bx-image me-1"></i> Image Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" id="f_title" class="form-control" placeholder="e.g. Hotel Entrance">
                        </div>
                        <div class="col-md-6">
                            <label>Category</label>
                            <select id="f_category" class="form-control">
                                <option value="hotel">Hotel</option>
                                <option value="rooms">Rooms</option>
                                <option value="mathura">Mathura</option>
                                <option value="tours">Tours</option>
                                <option value="food">Food</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>Caption</label>
                            <input type="text" id="f_caption" class="form-control" placeholder="Optional image caption or description">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-upload me-1"></i> Upload</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label>Image <span class="text-danger">*</span></label>
                            <div class="upload-zone" onclick="document.getElementById('f_image').click()">
                                <i class="bx bx-cloud-upload d-block"></i>
                                <p>Click to upload or drag and drop</p>
                                <small class="text-muted">Supports JPG, PNG, WebP</small>
                            </div>
                            <input type="file" id="f_image" class="form-control d-none" accept="image/*">
                            <div id="imagePreview" class="mt-2 text-center"></div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-cog me-1"></i> Settings</div>
                    <div class="row g-3">
                        <div class="col-md-6">
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
                    <button type="button" id="submitBtn" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Image</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function() {
        initDataTable({
            selector: "#dataTable", ajaxUrl: "{{ route('master.gallery.ajax') }}",
            moduleName: "Add Gallery Image", modalSelector: "#formModal",
            columns: [
                { data: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                { data: 'image_display', filter: 'none', orderable: false },
                { data: 'title', filter: 'text' }, { data: 'caption', filter: 'text' },
                { data: 'category', filter: 'select', options: [{value:'',label:'All'},{value:'hotel',label:'Hotel'},{value:'rooms',label:'Rooms'},{value:'mathura',label:'Mathura'},{value:'tours',label:'Tours'},{value:'food',label:'Food'}] },
                { data: 'status', filter: 'select', options: [{value:'',label:'All'},{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}] },
                { data: 'action', filter: 'none' }
            ],
            columnDefs: [{ targets: 0, width: '60px', className: 'text-center' }, { targets: -1, width: '80px', className: 'text-center' }]
        });
    });
    const fields = ['title','caption','category','is_active','sort_order'];
    const storeUrl = "{{ route('master.gallery.store') }}";
    const updateUrl = "{{ route('master.gallery.update', ':id') }}";
    const showUrl = "{{ route('master.gallery.show', ':id') }}";
    const deleteUrl = "{{ route('master.gallery.destroy', ':id') }}";
    const editClass = 'edit-gallery';
    const deleteClass = 'delete-gallery';
    const modalTitle = 'Gallery Image';
    @include('master-data._shared_js')
    </script>
    @endpush
</x-app-layout>
