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
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 20px 24px;
            color: #fff;
        }
        .master-card-header h5 { font-size: 1.15rem; font-weight: 600; margin: 0; }
        .master-card-header p { font-size: 0.82rem; opacity: 0.85; margin: 4px 0 0; }
        .master-card-body { padding: 24px; }

        /* Tabs */
        .meta-tabs { gap: 6px; flex-wrap: wrap; }
        .meta-tabs .nav-link {
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #555;
            transition: all 0.25s;
            border: 1.5px solid transparent;
        }
        .meta-tabs .nav-link:hover { background: #f0f7ff; color: #4facfe; }
        .meta-tabs .nav-link.active {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(79,172,254,0.3);
        }

        /* Group Card */
        .meta-group-card {
            border: 1.5px solid #f0f2f5;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 15px;
            background: #fafbfd;
        }
        .meta-group-card h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #4facfe;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8f4ff;
        }

        /* Fields */
        .meta-field {
            margin-bottom: 16px;
            padding: 12px 16px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #eef0f4;
            transition: box-shadow 0.2s;
        }
        .meta-field:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .meta-field label {
            font-weight: 600;
            font-size: 0.82rem;
            color: #444;
            margin-bottom: 6px;
            display: block;
        }
        .meta-field label small { color: #999; font-weight: 400; }
        .meta-field .form-control {
            border-radius: 8px;
            border: 1.5px solid #e0e3eb;
            padding: 9px 14px;
            font-size: 0.88rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .meta-field .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 3px rgba(79,172,254,0.12);
        }
        .meta-img-preview {
            width: 120px; height: 80px; object-fit: cover;
            border-radius: 8px; border: 2px solid #e9ecef; margin-top: 8px;
        }
        .delete-field-btn {
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .delete-field-btn:hover { background: #dc3545; color: #fff; }

        /* Modal */
        .modal-content { border: none; border-radius: 16px; overflow: hidden; }
        .modal-header.master-modal-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            border-color: #4facfe; box-shadow: 0 0 0 3px rgba(79,172,254,0.12);
        }
        .modal-footer { border-top: 1px solid #f0f0f0; padding: 14px 24px; }
        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none; border-radius: 8px; padding: 8px 24px; font-weight: 600; color: #fff;
        }
        .modal-footer .btn-light { border-radius: 8px; border: 1.5px solid #e0e3eb; }

        /* Action buttons */
        .btn-action-add {
            background: rgba(255,255,255,0.2);
            border: 1.5px solid rgba(255,255,255,0.4);
            color: #fff;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-action-add:hover { background: rgba(255,255,255,0.35); color: #fff; }
        .btn-action-save {
            background: #fff;
            border: none;
            color: #4facfe;
            border-radius: 8px;
            padding: 7px 18px;
            font-size: 0.82rem;
            font-weight: 700;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .btn-action-save:hover { background: #f0f7ff; color: #3a8fd4; }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="master-card">
        <div class="master-card-header d-flex justify-content-between align-items-center">
            <div>
                <h5><i class="bx bx-cog me-2"></i>Site Metadata & Settings</h5>
                <p>Configure global website settings and metadata fields</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-action-add" data-bs-toggle="modal" data-bs-target="#addFieldModal"><i class="bx bx-plus me-1"></i>Add Field</button>
                <button class="btn-action-save" id="saveAllBtn"><i class="bx bx-save me-1"></i>Save All</button>
            </div>
        </div>
        <div class="master-card-body">
            
            <ul class="nav nav-pills meta-tabs mb-4" role="tablist">
                <?php $__currentLoopData = $groupLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gKey => $gInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(isset($groups[$gKey])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e($loop->first ? 'active' : ''); ?>" data-bs-toggle="tab" href="#tab-<?php echo e($gKey); ?>">
                            <i class="<?php echo e($gInfo['icon']); ?> me-1"></i> <?php echo e($gInfo['label']); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>

            
            <form id="metadataForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="tab-content">
                    <?php $__currentLoopData = $groupLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gKey => $gInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(isset($groups[$gKey])): ?>
                        <div class="tab-pane fade <?php echo e($loop->first ? 'show active' : ''); ?>" id="tab-<?php echo e($gKey); ?>">
                            <div class="meta-group-card">
                                <h6><i class="<?php echo e($gInfo['icon']); ?> me-2"></i><?php echo e($gInfo['label']); ?></h6>
                                <div class="row">
                                    <?php $__currentLoopData = $groups[$gKey]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-<?php echo e(in_array($meta->type, ['textarea']) ? '12' : '6'); ?> meta-field">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label><?php echo e($meta->label); ?> <small>(<?php echo e($meta->key); ?>)</small></label>
                                            <button type="button" class="btn btn-outline-danger delete-field-btn" data-id="<?php echo e($meta->id); ?>" title="Delete field"><i class="bx bx-trash-alt"></i></button>
                                        </div>

                                        <?php if($meta->type === 'textarea'): ?>
                                            <textarea name="meta[<?php echo e($meta->id); ?>]" class="form-control" rows="3"><?php echo e($meta->value); ?></textarea>
                                        <?php elseif($meta->type === 'image'): ?>
                                            <input type="file" name="meta_file[<?php echo e($meta->id); ?>]" class="form-control" accept="image/*">
                                            <input type="hidden" name="meta[<?php echo e($meta->id); ?>]" value="<?php echo e($meta->value); ?>">
                                            <?php if($meta->value): ?>
                                                <img src="<?php echo e(url($meta->value)); ?>" class="meta-img-preview" alt="<?php echo e($meta->label); ?>">
                                            <?php endif; ?>
                                        <?php elseif($meta->type === 'color'): ?>
                                            <input type="color" name="meta[<?php echo e($meta->id); ?>]" class="form-control form-control-color w-100" value="<?php echo e($meta->value ?? '#000000'); ?>" style="height: 42px;">
                                        <?php else: ?>
                                            <input type="<?php echo e($meta->type); ?>" name="meta[<?php echo e($meta->id); ?>]" class="form-control" value="<?php echo e($meta->value); ?>"
                                                <?php if($meta->type === 'tel'): ?> placeholder="+91 XXXXX XXXXX" <?php endif; ?>
                                                <?php if($meta->type === 'email'): ?> placeholder="email@example.com" <?php endif; ?>
                                                <?php if($meta->type === 'url'): ?> placeholder="https://" <?php endif; ?>
                                            >
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="addFieldModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header master-modal-header text-white">
                    <h5 class="modal-title text-white"><i class="bx bx-plus-circle me-2"></i>Add Metadata Field</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label>Group <span class="text-danger">*</span></label>
                            <select id="af_group" class="form-control">
                                <?php $__currentLoopData = $groupLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gKey => $gInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gKey); ?>"><?php echo e($gInfo['label']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Key <span class="text-danger">*</span></label>
                            <input type="text" id="af_key" class="form-control" placeholder="e.g. site_name">
                        </div>
                        <div class="col-md-6">
                            <label>Label <span class="text-danger">*</span></label>
                            <input type="text" id="af_label" class="form-control" placeholder="e.g. Site Name">
                        </div>
                        <div class="col-md-6">
                            <label>Type</label>
                            <select id="af_type" class="form-control">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="email">Email</option>
                                <option value="tel">Phone</option>
                                <option value="url">URL</option>
                                <option value="number">Number</option>
                                <option value="time">Time</option>
                                <option value="image">Image</option>
                                <option value="color">Color</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Default Value</label>
                            <input type="text" id="af_value" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="addFieldBtn" class="btn btn-primary"><i class="bx bx-plus me-1"></i>Add Field</button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    // Save All
    $('#saveAllBtn').on('click', function() {
        let btn = $(this);
        btn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...').prop('disabled', true);

        let formData = new FormData($('#metadataForm')[0]);

        $.ajax({
            url: "<?php echo e(route('master.metadata.update')); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                success_noti(res.message);
                btn.html('<i class="bx bx-save me-1"></i>Save All').prop('disabled', false);
            },
            error: function(xhr) {
                error_noti(xhr.responseJSON?.message ?? 'Failed to save');
                btn.html('<i class="bx bx-save me-1"></i>Save All').prop('disabled', false);
            }
        });
    });

    // Add Field
    $('#addFieldBtn').on('click', function() {
        let btn = $(this);
        btn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Adding...').prop('disabled', true);

        $.ajax({
            url: "<?php echo e(route('master.metadata.add-field')); ?>",
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                group: $('#af_group').val(),
                key: $('#af_key').val(),
                label: $('#af_label').val(),
                type: $('#af_type').val(),
                value: $('#af_value').val()
            },
            success: function(res) {
                success_noti(res.message);
                $('#addFieldModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                error_noti(xhr.responseJSON?.message ?? 'Failed to add field');
                btn.html('<i class="bx bx-plus me-1"></i>Add Field').prop('disabled', false);
            }
        });
    });

    // Delete Field
    $(document).on('click', '.delete-field-btn', function() {
        let id = $(this).data('id');
        let row = $(this).closest('.meta-field');

        Swal.fire({
            title: 'Delete this field?',
            text: 'This metadata field will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "<?php echo e(route('master.metadata.delete-field', ':id')); ?>".replace(':id', id),
                type: 'POST',
                data: { _token: '<?php echo e(csrf_token()); ?>', _method: 'DELETE' },
                success: function(res) {
                    row.fadeOut(300, () => row.remove());
                    success_noti(res.message);
                },
                error: function(xhr) {
                    error_noti(xhr.responseJSON?.message ?? 'Failed to delete');
                }
            });
        });
    });
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
<?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/master-data/site-metadata.blade.php ENDPATH**/ ?>