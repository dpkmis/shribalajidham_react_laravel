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
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
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
        .master-card .table tbody tr:hover { background: #faf5ff; }
        .modal-content { border: none; border-radius: 16px; overflow: hidden; }
        .modal-header.master-modal-header {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
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
            border-color: #a6c1ee; box-shadow: 0 0 0 3px rgba(166,193,238,0.2);
        }
        .modal-footer { border-top: 1px solid #f0f0f0; padding: 14px 24px; }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600; color: #fff;
        }
        .modal-footer .btn-light { border-radius: 8px; border: 1.5px solid #e0e3eb; }
        .form-section-title {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: #8e7cc3; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #f3efff;
        }
        .form-check-styled { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #f8f9fc; border-radius: 8px; margin-top: 8px; }
        .form-check-styled input[type="checkbox"] { width: 18px; height: 18px; accent-color: #a6c1ee; }
        .form-check-styled label { margin: 0; font-weight: 500; color: #444; }
        .star-rating-input { display: flex; gap: 4px; }
        .star-rating-input .star-btn {
            background: none; border: none; font-size: 1.5rem; color: #ddd; cursor: pointer; transition: color 0.2s; padding: 0;
        }
        .star-rating-input .star-btn.active, .star-rating-input .star-btn:hover { color: #f7c948; }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="master-card">
        <div class="master-card-header d-flex justify-content-between align-items-center">
            <div>
                <h5><i class="bx bx-message-rounded-dots me-2"></i>Testimonials</h5>
                <p>Manage guest reviews and testimonials</p>
            </div>
        </div>
        <div class="master-card-body">
            <table id="dataTable" class="table table-hover mt-1">
                <thead><tr><th>Sr.</th><th>Guest</th><th>Location</th><th>Rating</th><th>Source</th><th>Featured</th><th>Status</th><th>Action</th></tr></thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header master-modal-header text-white">
                    <h5 class="modal-title text-white" id="modalTitle"><i class="bx bx-message-rounded-dots me-2"></i>Add Testimonial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height:72vh;overflow-y:auto;">
                    <div class="form-section-title"><i class="bx bx-user me-1"></i> Guest Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label>Guest Name <span class="text-danger">*</span></label>
                            <input type="text" id="f_guest_name" class="form-control" placeholder="e.g. Rajesh Kumar">
                        </div>
                        <div class="col-md-6">
                            <label>Location</label>
                            <input type="text" id="f_guest_location" class="form-control" placeholder="e.g. New Delhi">
                        </div>
                        <div class="col-md-4">
                            <label>Stay Date</label>
                            <input type="date" id="f_stay_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Source</label>
                            <select id="f_source" class="form-control">
                                <option value="Google">Google</option>
                                <option value="TripAdvisor">TripAdvisor</option>
                                <option value="Direct">Direct</option>
                                <option value="MakeMyTrip">MakeMyTrip</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Rating <span class="text-danger">*</span></label>
                            <select id="f_rating" class="form-control" style="display:none;">
                                <option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option>
                            </select>
                            <div class="star-rating-input mt-1">
                                <button type="button" class="star-btn active" data-val="1"><i class="bx bxs-star"></i></button>
                                <button type="button" class="star-btn active" data-val="2"><i class="bx bxs-star"></i></button>
                                <button type="button" class="star-btn active" data-val="3"><i class="bx bxs-star"></i></button>
                                <button type="button" class="star-btn active" data-val="4"><i class="bx bxs-star"></i></button>
                                <button type="button" class="star-btn active" data-val="5"><i class="bx bxs-star"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-chat me-1"></i> Review</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label>Review Text <span class="text-danger">*</span></label>
                            <textarea id="f_review_text" class="form-control" rows="4" placeholder="What did the guest say about their experience..."></textarea>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="bx bx-cog me-1"></i> Settings</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check-styled">
                                <input type="checkbox" id="f_is_featured">
                                <label for="f_is_featured">Featured</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check-styled">
                                <input type="checkbox" id="f_is_active" checked>
                                <label for="f_is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Sort Order</label>
                            <input type="number" id="f_sort_order" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="submitBtn" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Testimonial</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    $(document).ready(function() {
        initDataTable({
            selector: "#dataTable", ajaxUrl: "<?php echo e(route('master.testimonials.ajax')); ?>",
            moduleName: "Add Testimonial", modalSelector: "#formModal",
            columns: [
                { data: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                { data: 'guest_name', filter: 'text' }, { data: 'guest_location', filter: 'text' },
                { data: 'stars', filter: 'none' }, { data: 'source', filter: 'text' },
                { data: 'featured', filter: 'none' },
                { data: 'status', filter: 'select', options: [{value:'',label:'All'},{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}] },
                { data: 'action', filter: 'none' }
            ],
            columnDefs: [{ targets: 0, width: '60px', className: 'text-center' }, { targets: -1, width: '80px', className: 'text-center' }]
        });

        // Star rating click handler
        $(document).on('click', '.star-btn', function() {
            let val = $(this).data('val');
            $('#f_rating').val(val);
            $('.star-btn').each(function() {
                $(this).toggleClass('active', $(this).data('val') <= val);
            });
        });
    });
    const fields = ['guest_name','guest_location','rating','review_text','stay_date','source','is_featured','is_active','sort_order'];
    const storeUrl = "<?php echo e(route('master.testimonials.store')); ?>";
    const updateUrl = "<?php echo e(route('master.testimonials.update', ':id')); ?>";
    const showUrl = "<?php echo e(route('master.testimonials.show', ':id')); ?>";
    const deleteUrl = "<?php echo e(route('master.testimonials.destroy', ':id')); ?>";
    const editClass = 'edit-testimonial';
    const deleteClass = 'delete-testimonial';
    const modalTitle = 'Testimonial';
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
<?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/master-data/testimonials.blade.php ENDPATH**/ ?>