<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
    <style>
        .master-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .master-card-header {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
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
        .master-card .table tbody tr:hover { background: #fffbf5; }
        .modal-content { border: none; border-radius: 16px; overflow: hidden; }
        .modal-header.master-modal-header {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
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
            border-color: #fda085; box-shadow: 0 0 0 3px rgba(253,160,133,0.15);
        }
        .modal-footer { border-top: 1px solid #f0f0f0; padding: 14px 24px; }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600; color: #fff;
        }
        .modal-footer .btn-light { border-radius: 8px; border: 1.5px solid #e0e3eb; }
        .form-section-title {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: #e8873a; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #fef0e4;
        }
        .form-check-styled { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #f8f9fc; border-radius: 8px; margin-top: 8px; }
        .form-check-styled input[type="checkbox"] { width: 18px; height: 18px; accent-color: #fda085; }
        .form-check-styled label { margin: 0; font-weight: 500; color: #444; }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="master-card">
        <div class="master-card-header d-flex justify-content-between align-items-center">
            <div>
                <h5><i class="bx bx-map-pin me-2"></i>Nearby Attractions</h5>
                <p>Manage tourist attractions and places of interest near the property</p>
            </div>
        </div>
        <div class="master-card-body">
            <table id="dataTable" class="table table-hover mt-1">
                <thead><tr><th>Sr.</th><th>Image</th><th>Name</th><th>Distance</th><th>Travel Time</th><th>Category</th><th>Status</th><th>Action</th></tr></thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header master-modal-header text-white">
                    <h5 class="modal-title text-white" id="modalTitle"><i class="bx bx-map-pin me-2"></i>Add Nearby Attraction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height:72vh;overflow-y:auto;">
                    <div class="form-section-title"><i class="bx bx-info-circle me-1"></i> Basic Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" id="f_name" class="form-control" placeholder="e.g. Sri Krishna Janmabhoomi">
                        </div>
                        <div class="col-md-6">
                            <label>Category</label>
                            <select id="f_category" class="form-control">
                                <option value="temple">Temple</option>
                                <option value="ghat">Ghat</option>
                                <option value="museum">Museum</option>
                                <option value="market">Market</option>
                                <option value="park">Park</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="f_description" class="form-control" rows="3" placeholder="Brief description of the attraction..."></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-navigation me-1"></i> Distance & Travel</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label>Distance</label>
                            <input type="text" id="f_distance" class="form-control" placeholder="e.g. 3.5 km">
                        </div>
                        <div class="col-md-4">
                            <label>Travel Time</label>
                            <input type="text" id="f_travel_time" class="form-control" placeholder="e.g. 10 min drive">
                        </div>
                        <div class="col-md-4">
                            <label>Sort Order</label>
                            <input type="number" id="f_sort_order" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-star me-1"></i> Highlights</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label>Key Highlights <small class="text-muted">(one per line)</small></label>
                            <textarea id="f_highlights_text" class="form-control" rows="4" placeholder="Open 24 hours&#10;Free entry&#10;Aarti at 7 PM"></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-cog me-1"></i> Settings</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Image</label>
                            <input type="file" id="f_image" class="form-control" accept="image/*">
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check-styled">
                                <input type="checkbox" id="f_is_active" checked>
                                <label for="f_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="submitBtn" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Attraction</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    $(document).ready(function() {
        initDataTable({
            selector: "#dataTable", ajaxUrl: "<?php echo e(route('master.nearby-attractions.ajax')); ?>",
            moduleName: "Add Nearby Attraction", modalSelector: "#formModal",
            columns: [
                { data: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                { data: 'image_display', filter: 'none', orderable: false },
                { data: 'name', filter: 'text' }, { data: 'distance', filter: 'none' },
                { data: 'travel_time', filter: 'none' },
                { data: 'category', filter: 'select', options: [{value:'',label:'All'},{value:'temple',label:'Temple'},{value:'ghat',label:'Ghat'},{value:'museum',label:'Museum'},{value:'market',label:'Market'}] },
                { data: 'status', filter: 'select', options: [{value:'',label:'All'},{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}] },
                { data: 'action', filter: 'none' }
            ],
            columnDefs: [{ targets: 0, width: '60px', className: 'text-center' }, { targets: -1, width: '80px', className: 'text-center' }]
        });
    });
    const fields = ['name','description','distance','travel_time','category','highlights_text','is_active','sort_order'];
    const storeUrl = "<?php echo e(route('master.nearby-attractions.store')); ?>";
    const updateUrl = "<?php echo e(route('master.nearby-attractions.update', ':id')); ?>";
    const showUrl = "<?php echo e(route('master.nearby-attractions.show', ':id')); ?>";
    const deleteUrl = "<?php echo e(route('master.nearby-attractions.destroy', ':id')); ?>";
    const editClass = 'edit-attraction';
    const deleteClass = 'delete-attraction';
    const modalTitle = 'Nearby Attraction';
    <?php echo $__env->make('master-data._shared_js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/master-data/nearby-attractions.blade.php ENDPATH**/ ?>