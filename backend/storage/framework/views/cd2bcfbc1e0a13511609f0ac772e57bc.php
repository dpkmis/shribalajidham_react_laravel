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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 20px 24px;
            color: #fff;
        }
        .master-card-header h5 { font-size: 1.15rem; font-weight: 600; margin: 0; }
        .master-card-header p { font-size: 0.82rem; opacity: 0.85; margin: 4px 0 0; }
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
        .master-card .table tbody td { vertical-align: middle; padding: 12px 14px; font-size: 0.88rem; color: #444; }
        .master-card .table tbody tr { transition: background 0.2s; }
        .master-card .table tbody tr:hover { background: #fff5f7; }
        .modal-content { border: none; border-radius: 16px; overflow: hidden; }
        .modal-header.master-modal-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 18px 24px;
            border: none;
        }
        .modal-header.master-modal-header .modal-title { font-weight: 600; font-size: 1.05rem; }
        .modal-body { padding: 24px; }
        .modal-body label { font-weight: 600; font-size: 0.82rem; color: #555; margin-bottom: 4px; display: block; }
        .modal-body .form-control, .modal-body .form-select {
            border-radius: 8px; border: 1.5px solid #e0e3eb; padding: 9px 14px; font-size: 0.88rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .modal-body .form-control:focus, .modal-body .form-select:focus {
            border-color: #f5576c; box-shadow: 0 0 0 3px rgba(245,87,108,0.12);
        }
        .modal-footer { border-top: 1px solid #f0f0f0; padding: 14px 24px; }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600;
        }
        .modal-footer .btn-light { border-radius: 8px; border: 1.5px solid #e0e3eb; }
        .form-section-title {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: #f5576c; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #ffeef0;
        }
        .form-check-styled { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #f8f9fc; border-radius: 8px; margin-top: 8px; }
        .form-check-styled input[type="checkbox"] { width: 18px; height: 18px; accent-color: #f5576c; }
        .form-check-styled label { margin: 0; font-weight: 500; color: #444; }
        .color-preview-row { display: flex; align-items: center; gap: 12px; }
        .gradient-preview {
            width: 100%; height: 36px; border-radius: 8px; margin-top: 8px;
            border: 1.5px solid #e0e3eb;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="master-card">
        <div class="master-card-header d-flex justify-content-between align-items-center">
            <div>
                <h5><i class="bx bx-gift me-2"></i>Festival Offers</h5>
                <p>Manage seasonal festival offers and special packages</p>
            </div>
        </div>
        <div class="master-card-body">
            <table id="dataTable" class="table table-hover mt-1">
                <thead><tr><th>Sr.</th><th>Image</th><th>Name</th><th>Month</th><th>Price</th><th>Nights</th><th>Status</th><th>Action</th></tr></thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header master-modal-header text-white">
                    <h5 class="modal-title text-white" id="modalTitle"><i class="bx bx-gift me-2"></i>Add Festival Offer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height:72vh;overflow-y:auto;">
                    <div class="form-section-title"><i class="bx bx-info-circle me-1"></i> Festival Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" id="f_name" class="form-control" placeholder="e.g. Janmashtami 2026">
                        </div>
                        <div class="col-md-6">
                            <label>Hindi Name</label>
                            <input type="text" id="f_hindi_name" class="form-control" placeholder="e.g. जन्माष्टमी">
                        </div>
                        <div class="col-md-4">
                            <label>Festival Month <span class="text-danger">*</span></label>
                            <input type="text" id="f_festival_month" class="form-control" placeholder="e.g. August 2026">
                        </div>
                        <div class="col-md-4">
                            <label>Highlight Badge</label>
                            <input type="text" id="f_highlight_badge" class="form-control" placeholder="e.g. Biggest Festival">
                        </div>
                        <div class="col-md-4">
                            <label>Sort Order</label>
                            <input type="number" id="f_sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea id="f_description" class="form-control" rows="3" placeholder="Describe this festival offer..."></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-rupee me-1"></i> Pricing</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label>Total Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" id="f_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label>Per Night (₹)</label>
                            <input type="number" id="f_per_night" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label>Nights</label>
                            <input type="text" id="f_nights" class="form-control" placeholder="e.g. 2N">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-list-check me-1"></i> Includes</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label>Package Includes <small class="text-muted">(one per line)</small></label>
                            <textarea id="f_includes_text" class="form-control" rows="5" placeholder="Room accommodation&#10;Complimentary breakfast&#10;Aarti darshan arrangement"></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-palette me-1"></i> Appearance</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label>Gradient From</label>
                            <input type="color" id="f_gradient_from" class="form-control form-control-color w-100" value="#ff6b35">
                        </div>
                        <div class="col-md-4">
                            <label>Gradient To</label>
                            <input type="color" id="f_gradient_to" class="form-control form-control-color w-100" value="#f7c948">
                        </div>
                        <div class="col-md-4">
                            <label>Preview</label>
                            <div class="gradient-preview" id="gradientPreview" style="background: linear-gradient(135deg, #ff6b35, #f7c948);"></div>
                        </div>
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
                    <button type="button" id="submitBtn" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Offer</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    $(document).ready(function() {
        initDataTable({
            selector: "#dataTable", ajaxUrl: "<?php echo e(route('master.festival-offers.ajax')); ?>",
            moduleName: "Add Festival Offer", modalSelector: "#formModal",
            columns: [
                { data: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                { data: 'image_display', filter: 'none', orderable: false },
                { data: 'name', filter: 'text' }, { data: 'festival_month', filter: 'text' },
                { data: 'price_display', filter: 'none' }, { data: 'nights', filter: 'none' },
                { data: 'status', filter: 'select', options: [{value:'',label:'All'},{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}] },
                { data: 'action', filter: 'none' }
            ],
            columnDefs: [{ targets: 0, width: '60px', className: 'text-center' }, { targets: -1, width: '80px', className: 'text-center' }]
        });

        // Live gradient preview
        $('#f_gradient_from, #f_gradient_to').on('input', function() {
            $('#gradientPreview').css('background', `linear-gradient(135deg, ${$('#f_gradient_from').val()}, ${$('#f_gradient_to').val()})`);
        });
    });
    const fields = ['name','hindi_name','festival_month','price','per_night','nights','highlight_badge','description','includes_text','gradient_from','gradient_to','is_active','sort_order'];
    const storeUrl = "<?php echo e(route('master.festival-offers.store')); ?>";
    const updateUrl = "<?php echo e(route('master.festival-offers.update', ':id')); ?>";
    const showUrl = "<?php echo e(route('master.festival-offers.show', ':id')); ?>";
    const deleteUrl = "<?php echo e(route('master.festival-offers.destroy', ':id')); ?>";
    const editClass = 'edit-festival-offer';
    const deleteClass = 'delete-festival-offer';
    const modalTitle = 'Festival Offer';
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
<?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/master-data/festival-offers.blade.php ENDPATH**/ ?>