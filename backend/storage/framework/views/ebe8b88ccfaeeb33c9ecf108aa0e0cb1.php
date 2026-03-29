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
    <?php $__env->startPush('style'); ?>
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                overflow-y: auto;
            }
            .stock-badge {
                font-size: 0.85rem;
                padding: 0.35rem 0.65rem;
            }
            .low-stock-alert {
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.6; }
            }
        </style>
    <?php $__env->stopPush(); ?>

    <div class="card p-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Alert Badges Row -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-error"></i> Out of Stock: <span id="outOfStockCount">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-down-arrow-alt"></i> Low Stock: <span id="lowStockCount">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-refresh"></i> Reorder: <span id="reorderCount">0</span></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body py-2">
                        <h6 class="mb-0"><i class="bx bx-time"></i> Expiring Soon: <span id="expiringCount">0</span></h6>
                    </div>
                </div>
            </div>
        </div>

        <table id="inventoryTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Stock Status</th>
                    <th>Value</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Inventory Item Modal -->
    <div class="modal fade custom-modal" id="itemModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="itemModalLabel">Add Inventory Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Information Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2">Basic Information</h6></div>
                        
                        <div class="col-md-4">
                            <label>Property <span class="text-danger">*</span></label>
                            <select id="itemProperty" class="form-control">
                                <option value="">Select Property</option>
                                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger d-none" id="itemPropertyError">Property is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Category <span class="text-danger">*</span></label>
                            <select id="itemCategory" class="form-control">
                                <option value="">Select Category</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-danger d-none" id="itemCategoryError">Category is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Item Code <span class="text-danger">*</span></label>
                            <input type="text" id="itemCode" class="form-control" placeholder="SKU-001">
                            <small class="text-danger d-none" id="itemCodeError">Item code is required</small>
                        </div>

                        <div class="col-md-8">
                            <label>Item Name <span class="text-danger">*</span></label>
                            <input type="text" id="itemName" class="form-control" placeholder="Enter item name">
                            <small class="text-danger d-none" id="itemNameError">Item name is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Unit <span class="text-danger">*</span></label>
                            <select id="itemUnit" class="form-control">
                                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($unit); ?>"><?php echo e($unit); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label>Description</label>
                            <textarea id="itemDescription" class="form-control" rows="2" placeholder="Item description..."></textarea>
                        </div>

                        <!-- Stock Information Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Stock Information</h6></div>

                        <div class="col-md-3">
                            <label>Current Stock <span class="text-danger">*</span></label>
                            <input type="number" id="currentStock" class="form-control" placeholder="0" step="0.01" min="0">
                            <small class="text-danger d-none" id="currentStockError">Current stock is required</small>
                        </div>

                        <div class="col-md-3">
                            <label>Minimum Stock <span class="text-danger">*</span></label>
                            <input type="number" id="minStock" class="form-control" placeholder="0" step="0.01" min="0">
                            <small class="text-danger d-none" id="minStockError">Min stock is required</small>
                        </div>

                        <div class="col-md-3">
                            <label>Maximum Stock <span class="text-danger">*</span></label>
                            <input type="number" id="maxStock" class="form-control" placeholder="0" step="0.01" min="0">
                            <small class="text-danger d-none" id="maxStockError">Max stock is required</small>
                        </div>

                        <div class="col-md-3">
                            <label>Reorder Point</label>
                            <input type="number" id="reorderPoint" class="form-control" placeholder="Auto" step="0.01" min="0">
                            <small class="text-muted">Leave empty for auto (= min stock)</small>
                        </div>

                        <!-- Pricing Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Pricing Information</h6></div>

                        <div class="col-md-6">
                            <label>Unit Price (₹)</label>
                            <input type="number" id="unitPrice" class="form-control" placeholder="0.00" step="0.01" min="0">
                        </div>

                        <!-- Storage & Location Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Storage & Location</h6></div>

                        <div class="col-md-6">
                            <label>Storage Location</label>
                            <input type="text" id="storageLocation" class="form-control" placeholder="e.g., Main Warehouse">
                        </div>

                        <div class="col-md-6">
                            <label>Bin/Shelf Location</label>
                            <input type="text" id="binLocation" class="form-control" placeholder="e.g., A-12-3">
                        </div>

                        <!-- Supplier Information Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Supplier Information</h6></div>

                        <div class="col-md-6">
                            <label>Supplier Name</label>
                            <input type="text" id="supplierName" class="form-control" placeholder="Supplier name">
                        </div>

                        <div class="col-md-6">
                            <label>Supplier Code</label>
                            <input type="text" id="supplierCode" class="form-control" placeholder="Supplier SKU">
                        </div>

                        <!-- Batch & Expiry Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Batch & Expiry</h6></div>

                        <div class="col-md-4">
                            <label class="d-block">
                                <input type="checkbox" id="isPerishable"> Perishable Item
                            </label>
                        </div>

                        <div class="col-md-4">
                            <label>Batch Number</label>
                            <input type="text" id="batchNumber" class="form-control" placeholder="BATCH-001">
                        </div>

                        <div class="col-md-4">
                            <label>Expiry Date</label>
                            <input type="date" id="expiryDate" class="form-control">
                        </div>

                        <!-- Additional Settings Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Additional Settings</h6></div>

                        <div class="col-md-4">
                            <label>Status <span class="text-danger">*</span></label>
                            <select id="itemStatus" class="form-control">
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="d-block">
                                <input type="checkbox" id="requiresApproval"> Requires Approval for Stock Out
                            </label>
                        </div>

                        <div class="col-md-4">
                            <label class="d-block">
                                <input type="checkbox" id="isActive" checked> Active
                            </label>
                        </div>

                        <div class="col-12">
                            <label>Notes</label>
                            <textarea id="itemNotes" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitItemBtn" class="btn btn-primary">Save Item</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock In Modal -->
    <div class="modal fade" id="stockInModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">Stock In</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="stockInItemId">
                    <div class="mb-3">
                        <label>Item: <strong id="stockInItemName"></strong></label>
                        <div class="text-muted">Current Stock: <span id="stockInCurrentStock"></span></div>
                    </div>
                    <div class="mb-3">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="stockInQuantity" class="form-control" placeholder="0" step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label>Unit Price (₹)</label>
                        <input type="number" id="stockInUnitPrice" class="form-control" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label>Batch Number</label>
                        <input type="text" id="stockInBatch" class="form-control" placeholder="BATCH-001">
                    </div>
                    <div class="mb-3">
                        <label>Expiry Date</label>
                        <input type="date" id="stockInExpiry" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea id="stockInRemarks" class="form-control" rows="2" placeholder="Purchase order, supplier details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitStockInBtn" class="btn btn-success">Add Stock</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Out Modal -->
    <div class="modal fade" id="stockOutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Stock Out</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="stockOutItemId">
                    <div class="mb-3">
                        <label>Item: <strong id="stockOutItemName"></strong></label>
                        <div class="text-muted">Current Stock: <span id="stockOutCurrentStock"></span></div>
                    </div>
                    <div class="mb-3">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="stockOutQuantity" class="form-control" placeholder="0" step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label>Reference Type</label>
                        <select id="stockOutRefType" class="form-control">
                            <option value="">Select Type</option>
                            <option value="booking">Booking</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="housekeeping">Housekeeping</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Reference ID</label>
                        <input type="text" id="stockOutRefId" class="form-control" placeholder="Booking/Order ID">
                    </div>
                    <div class="mb-3">
                        <label>Remarks <span class="text-danger">*</span></label>
                        <textarea id="stockOutRemarks" class="form-control" rows="2" placeholder="Reason for stock out..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitStockOutBtn" class="btn btn-danger">Remove Stock</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Adjust Stock Modal -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title text-white">Adjust Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="adjustItemId">
                    <div class="mb-3">
                        <label>Item: <strong id="adjustItemName"></strong></label>
                        <div class="text-muted">Current Stock: <span id="adjustCurrentStock"></span></div>
                    </div>
                    <div class="mb-3">
                        <label>New Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="adjustNewQuantity" class="form-control" placeholder="0" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label>Remarks <span class="text-danger">*</span></label>
                        <textarea id="adjustRemarks" class="form-control" rows="2" placeholder="Reason for adjustment (stock count, damage, etc.)" required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="bx bx-info-circle"></i> This will adjust the stock to the exact quantity specified.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitAdjustBtn" class="btn btn-warning">Adjust Stock</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Transaction History</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <h6 id="historyItemName" class="mb-3"></h6>
                    <div id="historyContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status"></div>
                            <p class="mt-2">Loading history...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <?php if(session('notify')): ?>
            <script>
                alert_box('success', "<?php echo e(session('notify')); ?>");
            </script>
        <?php endif; ?>

        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#inventoryTable",
                    ajaxUrl: "<?php echo e(route('inventory.ajax')); ?>",
                    moduleName: "Add Inventory Item",
                    modalSelector: "#itemModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'item_code', filter: 'text' },
                        { data: 'name', filter: 'text' },
                        { data: 'category.name', filter: 'text' },
                        { data: 'stock_display', filter: 'none' },
                        { data: 'stock_status_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "out_of_stock", label: "Out of Stock" },
                                { value: "low_stock", label: "Low Stock" },
                                { value: "reorder", label: "Reorder" },
                                { value: "overstock", label: "Overstock" },
                                { value: "normal", label: "Normal" }
                            ]
                        },
                        { data: 'value_display', filter: 'none' },
                        { data: 'expiry_display', filter: 'none' },
                        { data: 'status_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "active", label: "Active" },
                                { value: "inactive", label: "Inactive" },
                                { value: "discontinued", label: "Discontinued" }
                            ]
                        },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "70px", className: "text-center" },
                        { targets: [5, 6, 7, 8, 9], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ],
                    drawCallback: function() {
                        updateDashboardCounts();
                    }
                });

                // Update dashboard counts
                function updateDashboardCounts() {
                    $.get("<?php echo e(route('inventory.low-stock')); ?>", function(res) {
                        let outOfStock = res.data.filter(i => i.current_stock <= 0).length;
                        let lowStock = res.data.filter(i => i.current_stock > 0 && i.current_stock <= i.min_stock).length;
                        let reorder = res.data.filter(i => i.current_stock > i.min_stock && i.current_stock <= i.reorder_point).length;
                        
                        $('#outOfStockCount').text(outOfStock);
                        $('#lowStockCount').text(lowStock);
                        $('#reorderCount').text(reorder);
                    });

                    $.get("<?php echo e(route('inventory.expiring')); ?>", function(res) {
                        $('#expiringCount').text(res.count);
                    });
                }

                updateDashboardCounts();
            });
        </script>

        <!-- Submit Item -->
        <script>
            $('#submitItemBtn').on('click', function () {
                let id = $('#itemModal').data('id');
                
                $('.text-danger').addClass('d-none');

                let propertyId = $('#itemProperty').val();
                let categoryId = $('#itemCategory').val();
                let itemCode = $('#itemCode').val().trim();
                let name = $('#itemName').val().trim();
                let description = $('#itemDescription').val().trim();
                let unit = $('#itemUnit').val();
                let currentStock = $('#currentStock').val();
                let minStock = $('#minStock').val();
                let maxStock = $('#maxStock').val();
                let reorderPoint = $('#reorderPoint').val();
                let unitPrice = $('#unitPrice').val();
                let storageLocation = $('#storageLocation').val().trim();
                let binLocation = $('#binLocation').val().trim();
                let supplierName = $('#supplierName').val().trim();
                let supplierCode = $('#supplierCode').val().trim();
                let batchNumber = $('#batchNumber').val().trim();
                let expiryDate = $('#expiryDate').val();
                let isPerishable = $('#isPerishable').is(':checked') ? 1 : 0;
                let requiresApproval = $('#requiresApproval').is(':checked') ? 1 : 0;
                let status = $('#itemStatus').val();
                let isActive = $('#isActive').is(':checked') ? 1 : 0;
                let notes = $('#itemNotes').val().trim();

                // Validation
                let isValid = true;
                if (!propertyId) {
                    $('#itemPropertyError').removeClass('d-none');
                    isValid = false;
                }
                if (!categoryId) {
                    $('#itemCategoryError').removeClass('d-none');
                    isValid = false;
                }
                if (!itemCode) {
                    $('#itemCodeError').removeClass('d-none');
                    isValid = false;
                }
                if (!name) {
                    $('#itemNameError').removeClass('d-none');
                    isValid = false;
                }
                if (!id && !currentStock) {
                    $('#currentStockError').removeClass('d-none');
                    isValid = false;
                }
                if (!minStock) {
                    $('#minStockError').removeClass('d-none');
                    isValid = false;
                }
                if (!maxStock) {
                    $('#maxStockError').removeClass('d-none');
                    isValid = false;
                }

                if (!isValid) {
                    error_noti('Please fill all required fields');
                    return;
                }

                let payload = {
                    _token: "<?php echo e(csrf_token()); ?>",
                    _method: id ? 'PUT' : 'POST',
                    property_id: propertyId,
                    category_id: categoryId,
                    item_code: itemCode,
                    name: name,
                    description: description || null,
                    unit: unit,
                    current_stock: currentStock || 0,
                    min_stock: minStock,
                    max_stock: maxStock,
                    reorder_point: reorderPoint || null,
                    unit_price: unitPrice || null,
                    storage_location: storageLocation || null,
                    bin_location: binLocation || null,
                    supplier_name: supplierName || null,
                    supplier_code: supplierCode || null,
                    batch_number: batchNumber || null,
                    expiry_date: expiryDate || null,
                    is_perishable: isPerishable,
                    requires_approval: requiresApproval,
                    status: status,
                    is_active: isActive,
                    notes: notes || null
                };

                let url = id
                    ? "<?php echo e(route('inventory.update', ':id')); ?>".replace(':id', id)
                    : "<?php echo e(route('inventory.store')); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#itemModal').modal('hide');
                        resetItemModal();
                        $('#inventoryTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save item';
                        if (xhr.responseJSON?.errors) {
                            let errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- Edit Item -->
        <script>
            $(document).on('click', '.edit-item', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('inventory.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#itemProperty').val(data.property_id);
                        $('#itemCategory').val(data.category_id);
                        $('#itemCode').val(data.item_code);
                        $('#itemName').val(data.name);
                        $('#itemDescription').val(data.description || '');
                        $('#itemUnit').val(data.unit);
                        $('#currentStock').val(data.current_stock).prop('disabled', true);
                        $('#minStock').val(data.min_stock);
                        $('#maxStock').val(data.max_stock);
                        $('#reorderPoint').val(data.reorder_point || '');
                        $('#unitPrice').val(data.unit_price_display || '');
                        $('#storageLocation').val(data.storage_location || '');
                        $('#binLocation').val(data.bin_location || '');
                        $('#supplierName').val(data.supplier_name || '');
                        $('#supplierCode').val(data.supplier_code || '');
                        $('#batchNumber').val(data.batch_number || '');
                        $('#expiryDate').val(data.expiry_date_display || '');
                        $('#isPerishable').prop('checked', data.is_perishable);
                        $('#requiresApproval').prop('checked', data.requires_approval);
                        $('#itemStatus').val(data.status);
                        $('#isActive').prop('checked', data.is_active);
                        $('#itemNotes').val(data.notes || '');

                        $('#itemModal')
                            .data('id', data.id)
                            .modal('show');

                        $('#itemModalLabel').text('Edit Inventory Item');
                    },
                    error: function () {
                        error_noti('Unable to load item details');
                    }
                });
            });
        </script>

        <!-- Stock In -->
        <script>
            $(document).on('click', '.stock-in', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('inventory.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#stockInItemId').val(data.id);
                        $('#stockInItemName').text(data.name);
                        $('#stockInCurrentStock').text(data.current_stock + ' ' + data.unit);
                        $('#stockInQuantity').val('');
                        $('#stockInUnitPrice').val(data.unit_price_display || '');
                        $('#stockInBatch').val(data.batch_number || '');
                        $('#stockInExpiry').val(data.expiry_date_display || '');
                        $('#stockInRemarks').val('');
                        
                        $('#stockInModal').modal('show');
                    }
                });
            });

            $('#submitStockInBtn').on('click', function () {
                let itemId = $('#stockInItemId').val();
                let quantity = $('#stockInQuantity').val();
                let unitPrice = $('#stockInUnitPrice').val();
                let batchNumber = $('#stockInBatch').val().trim();
                let expiryDate = $('#stockInExpiry').val();
                let remarks = $('#stockInRemarks').val().trim();

                if (!quantity || quantity <= 0) {
                    error_noti('Please enter valid quantity');
                    return;
                }

                $.ajax({
                    url: "<?php echo e(route('inventory.stock-in')); ?>",
                    type: "POST",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
                        item_id: itemId,
                        quantity: quantity,
                        unit_price: unitPrice || null,
                        batch_number: batchNumber || null,
                        expiry_date: expiryDate || null,
                        remarks: remarks || null
                    },
                    success: function (res) {
                        success_noti(res.message);
                        $('#stockInModal').modal('hide');
                        $('#inventoryTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to add stock');
                    }
                });
            });
        </script>

        <!-- Stock Out -->
        <script>
            $(document).on('click', '.stock-out', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('inventory.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#stockOutItemId').val(data.id);
                        $('#stockOutItemName').text(data.name);
                        $('#stockOutCurrentStock').text(data.current_stock + ' ' + data.unit);
                        $('#stockOutQuantity').val('');
                        $('#stockOutRefType').val('');
                        $('#stockOutRefId').val('');
                        $('#stockOutRemarks').val('');
                        
                        $('#stockOutModal').modal('show');
                    }
                });
            });

            $('#submitStockOutBtn').on('click', function () {
                let itemId = $('#stockOutItemId').val();
                let quantity = $('#stockOutQuantity').val();
                let refType = $('#stockOutRefType').val();
                let refId = $('#stockOutRefId').val().trim();
                let remarks = $('#stockOutRemarks').val().trim();

                if (!quantity || quantity <= 0) {
                    error_noti('Please enter valid quantity');
                    return;
                }

                if (!remarks) {
                    error_noti('Please enter remarks');
                    return;
                }

                $.ajax({
                    url: "<?php echo e(route('inventory.stock-out')); ?>",
                    type: "POST",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
                        item_id: itemId,
                        quantity: quantity,
                        reference_type: refType || null,
                        reference_id: refId || null,
                        remarks: remarks
                    },
                    success: function (res) {
                        success_noti(res.message);
                        $('#stockOutModal').modal('hide');
                        $('#inventoryTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to remove stock');
                    }
                });
            });
        </script>

        <!-- Adjust Stock -->
        <script>
            $(document).on('click', '.adjust-stock', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('inventory.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#adjustItemId').val(data.id);
                        $('#adjustItemName').text(data.name);
                        $('#adjustCurrentStock').text(data.current_stock + ' ' + data.unit);
                        $('#adjustNewQuantity').val(data.current_stock);
                        $('#adjustRemarks').val('');
                        
                        $('#adjustStockModal').modal('show');
                    }
                });
            });

            $('#submitAdjustBtn').on('click', function () {
                let itemId = $('#adjustItemId').val();
                let newQuantity = $('#adjustNewQuantity').val();
                let remarks = $('#adjustRemarks').val().trim();

                if (!newQuantity || newQuantity < 0) {
                    error_noti('Please enter valid quantity');
                    return;
                }

                if (!remarks) {
                    error_noti('Please enter remarks for adjustment');
                    return;
                }

                $.ajax({
                    url: "<?php echo e(route('inventory.adjust-stock')); ?>",
                    type: "POST",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
                        item_id: itemId,
                        new_quantity: newQuantity,
                        remarks: remarks
                    },
                    success: function (res) {
                        success_noti(res.message);
                        $('#adjustStockModal').modal('hide');
                        $('#inventoryTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to adjust stock');
                    }
                });
            });
        </script>

        <!-- View History -->
        <script>
            $(document).on('click', '.view-history', function () {
                let id = $(this).data('id');
                let itemUrl = "<?php echo e(route('inventory.show', ':id')); ?>".replace(':id', id);
                let historyUrl = "<?php echo e(route('inventory.history', ':id')); ?>".replace(':id', id);
                
                // Get item details
                $.get(itemUrl, function(item) {
                    $('#historyItemName').text(item.name + ' - Transaction History');
                });

                // Get transaction history
                $('#historyContent').html('<div class="text-center py-4"><div class="spinner-border"></div><p class="mt-2">Loading history...</p></div>');
                $('#historyModal').modal('show');

                $.get(historyUrl, function(res) {
                    if (res.data.length === 0) {
                        $('#historyContent').html('<div class="alert alert-info">No transactions found</div>');
                        return;
                    }

                    let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>Type</th><th>Qty</th><th>Balance</th><th>User</th><th>Remarks</th></tr></thead><tbody>';
                    
                    res.data.forEach(function(tx) {
                        let typeColors = {
                            'stock_in': 'success',
                            'stock_out': 'danger',
                            'adjustment': 'warning',
                            'transfer': 'info',
                            'damage': 'dark',
                            'expired': 'secondary',
                            'return': 'primary'
                        };
                        let color = typeColors[tx.transaction_type] || 'secondary';
                        let qtyDisplay = tx.quantity > 0 ? '+' + tx.quantity : tx.quantity;
                        
                        html += '<tr>';
                        html += '<td>' + new Date(tx.created_at).toLocaleString() + '</td>';
                        html += '<td><span class="badge bg-' + color + '">' + tx.transaction_type.replace('_', ' ') + '</span></td>';
                        html += '<td class="' + (tx.quantity > 0 ? 'text-success' : 'text-danger') + '">' + qtyDisplay + '</td>';
                        html += '<td>' + tx.balance_after + '</td>';
                        html += '<td>' + (tx.user ? tx.user.name : 'System') + '</td>';
                        html += '<td>' + (tx.remarks || '-') + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                    $('#historyContent').html(html);
                }).fail(function() {
                    $('#historyContent').html('<div class="alert alert-danger">Failed to load history</div>');
                });
            });
        </script>

        <!-- Delete Item -->
        <script>
            $(document).on('click', '.delete-item', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This inventory item will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "<?php echo e(route('inventory.destroy', ':id')); ?>".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "<?php echo e(csrf_token()); ?>",
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
                            $('#inventoryTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: xhr.responseJSON?.message ?? 'Failed to delete item'
                            });
                        }
                    });
                });
            });
        </script>

        <!-- Reset Modal -->
        <script>
            function resetItemModal() {
                $('#itemProperty').val('');
                $('#itemCategory').val('');
                $('#itemCode').val('');
                $('#itemName').val('');
                $('#itemDescription').val('');
                $('#itemUnit').val('pcs');
                $('#currentStock').val('').prop('disabled', false);
                $('#minStock').val('');
                $('#maxStock').val('');
                $('#reorderPoint').val('');
                $('#unitPrice').val('');
                $('#storageLocation').val('');
                $('#binLocation').val('');
                $('#supplierName').val('');
                $('#supplierCode').val('');
                $('#batchNumber').val('');
                $('#expiryDate').val('');
                $('#isPerishable').prop('checked', false);
                $('#requiresApproval').prop('checked', false);
                $('#itemStatus').val('active');
                $('#isActive').prop('checked', true);
                $('#itemNotes').val('');
                
                $('.text-danger').addClass('d-none');
                $('#itemModal').removeData('id');
                $('#itemModalLabel').text('Add Inventory Item');
            }

            $('#itemModal').on('hidden.bs.modal', resetItemModal);
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
<?php endif; ?><?php /**PATH /var/www/html/wavestube/resources/views/inventory/index.blade.php ENDPATH**/ ?>