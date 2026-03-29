<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                max-height: 75vh;
                overflow-y: auto;
            }
            .line-item-row {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 10px;
                border-left: 3px solid #0d6efd;
            }
            .invoice-summary {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 4px;
            }
            .stat-card {
                transition: transform 0.2s;
            }
            .stat-card:hover {
                transform: translateY(-2px);
            }
        </style>
    @endpush

    <div class="card p-4">
        <!-- Quick Stats -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white stat-card">
                    <div class="card-body">
                        <h5 id="totalInvoiced" class="text-white">₹0</h5>
                        <small>Total Invoiced</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white stat-card">
                    <div class="card-body">
                        <h5 id="totalPaid" class="text-white">₹0</h5>
                        <small>Total Paid</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white stat-card">
                    <div class="card-body">
                        <h5 id="totalOutstanding" class="text-white">₹0</h5>
                        <small>Outstanding</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white stat-card">
                    <div class="card-body">
                        <h5 id="totalOverdue" class="text-white">₹0</h5>
                        <small>Overdue</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="propertyFilter" class="form-control">
                    <option value="">All Properties</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-control">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="partially_paid">Partially Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" id="dateFromFilter" class="form-control" placeholder="From Date">
            </div>
            <div class="col-md-3">
                <input type="date" id="dateToFilter" class="form-control" placeholder="To Date">
            </div>
        </div>

        <!-- Invoices Table -->
        <table id="invoicesTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Invoice #</th>
                    <th>Guest</th>
                    <th>Issue / Due Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Invoice Modal -->
    <div class="modal fade custom-modal" id="invoiceModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="invoiceModalLabel">Create Invoice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Property -->
                        <div class="col-md-4">
                            <label>Property <span class="text-danger">*</span></label>
                            <select id="invoiceProperty" class="form-control">
                                <option value="">Select Property</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guest -->
                        <div class="col-md-4">
                            <label>Guest <span class="text-danger">*</span></label>
                            <select id="invoiceGuest" class="form-control">
                                <option value="">Select Guest</option>
                                @foreach($guests as $g)
                                    <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type -->
                        <div class="col-md-4">
                            <label>Invoice Type</label>
                            <select id="invoiceType" class="form-control">
                                <option value="booking">Booking Invoice</option>
                                <option value="folio">Guest Folio</option>
                                <option value="proforma">Proforma Invoice</option>
                                <option value="credit_note">Credit Note</option>
                                <option value="debit_note">Debit Note</option>
                            </select>
                        </div>

                        <!-- Issue Date -->
                        <div class="col-md-4">
                            <label>Issue Date <span class="text-danger">*</span></label>
                            <input type="date" id="issueDate" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-4">
                            <label>Due Date</label>
                            <input type="date" id="dueDate" class="form-control">
                        </div>

                        <!-- Tax Rate -->
                        <div class="col-md-4">
                            <label>Tax Rate (%)</label>
                            <input type="number" id="taxRate" class="form-control" value="18" step="0.01" min="0" max="100">
                        </div>

                        <!-- Line Items Section -->
                        <div class="col-md-12">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6>Line Items</h6>
                                <button type="button" class="btn btn-sm btn-success" id="addLineItemBtn">
                                    <i class="bx bx-plus"></i> Add Item
                                </button>
                            </div>
                            <div id="lineItemsContainer">
                                <!-- Line items will be added here -->
                            </div>
                        </div>

                        <!-- Invoice Summary -->
                        <div class="col-md-12">
                            <div class="invoice-summary">
                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Notes -->
                                        <label>Notes</label>
                                        <textarea id="invoiceNotes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td class="text-end fw-bold" id="invoiceSubtotal">₹0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Tax:</td>
                                                <td class="text-end fw-bold" id="invoiceTax">₹0.00</td>
                                            </tr>
                                            <tr class="table-primary">
                                                <td><strong>Total:</strong></td>
                                                <td class="text-end fw-bold" id="invoiceTotal">₹0.00</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitInvoiceBtn" class="btn btn-primary">
                        <span id="submitInvoiceBtnText">Create Invoice</span>
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Invoice Modal -->
    <div class="modal fade" id="viewInvoiceModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Invoice Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewInvoiceContent">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment to Invoice Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">Add Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Invoice Balance:</strong> <span id="invoiceBalance">₹0.00</span>
                    </div>
                    <div class="mb-3">
                        <label>Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" id="paymentAmount" class="form-control" step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select id="paymentMethod" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="net_banking">Net Banking</option>
                            <option value="cheque">Cheque</option>
                            <option value="wallet">Wallet</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3 cheque-fields d-none">
                        <label>Cheque Number</label>
                        <input type="text" id="chequeNumber" class="form-control">
                    </div>
                    <div class="mb-3 cheque-fields d-none">
                        <label>Cheque Date</label>
                        <input type="date" id="chequeDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Transaction ID</label>
                        <input type="text" id="transactionId" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea id="paymentRemarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitPaymentBtn" class="btn btn-success">Add Payment</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            let lineItemCounter = 0;
            let currentInvoiceId = null;

            $(document).ready(function () {
                // Initialize DataTable
                let table = initDataTable({
                    selector: "#invoicesTable",
                    ajaxUrl: "{{ route('financials.invoices.ajax') }}",
                    moduleName: "Create Invoice",
                    modalSelector: "#invoiceModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'invoice_number_display' },
                        { data: 'guest_display' },
                        { data: 'dates_display' },
                        { data: 'amount_display' },
                        { data: 'status_badge' },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });

                // Load financial stats
                loadFinancialStats();

                // Add first line item by default
                addLineItem();

                // Apply filters
                $('#propertyFilter, #statusFilter').on('change', function() {
                    table.ajax.reload();
                });

                // Show cheque fields when cheque is selected
                $('#paymentMethod').on('change', function() {
                    if ($(this).val() === 'cheque') {
                        $('.cheque-fields').removeClass('d-none');
                    } else {
                        $('.cheque-fields').addClass('d-none');
                    }
                });
            });

            // Load Financial Stats
            function loadFinancialStats() {
                $.ajax({
                    url: "{{ route('financials.reports.stats') }}",
                    type: 'GET',
                    data: {
                        property_id: $('#propertyFilter').val()
                    },
                    success: function(response) {
                        if (response.status && response.stats) {
                            $('#totalInvoiced').text('₹' + formatNumber(response.stats.total_invoiced));
                            $('#totalPaid').text('₹' + formatNumber(response.stats.total_paid));
                            $('#totalOutstanding').text('₹' + formatNumber(response.stats.outstanding));
                            $('#totalOverdue').text('₹' + formatNumber(response.stats.overdue));
                        }
                    }
                });
            }

            // Format number
            function formatNumber(num) {
                return parseFloat(num).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Add Line Item
            $('#addLineItemBtn').on('click', function() {
                addLineItem();
            });

            function addLineItem() {
                lineItemCounter++;
                let html = `
                    <div class="line-item-row" data-item-index="${lineItemCounter}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label>Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control item-description" data-index="${lineItemCounter}" placeholder="Item description">
                            </div>
                            <div class="col-md-2">
                                <label>Quantity</label>
                                <input type="number" class="form-control item-quantity" data-index="${lineItemCounter}" value="1" min="1">
                            </div>
                            <div class="col-md-2">
                                <label>Unit Price (₹)</label>
                                <input type="number" class="form-control item-unit-price" data-index="${lineItemCounter}" step="0.01" min="0">
                            </div>
                            <div class="col-md-2">
                                <label>Subtotal (₹)</label>
                                <input type="text" class="form-control item-subtotal" data-index="${lineItemCounter}" readonly>
                            </div>
                            <div class="col-md-1">
                                <label>Type</label>
                                <select class="form-control item-type" data-index="${lineItemCounter}">
                                    <option value="room">Room</option>
                                    <option value="food">Food</option>
                                    <option value="service">Service</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-item" data-index="${lineItemCounter}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#lineItemsContainer').append(html);
                calculateInvoiceTotals();
            }

            // Remove Line Item
            $(document).on('click', '.remove-item', function() {
                let index = $(this).data('index');
                $(`.line-item-row[data-item-index="${index}"]`).remove();
                calculateInvoiceTotals();
            });

            // Calculate on change
            $(document).on('input', '.item-quantity, .item-unit-price', function() {
                let index = $(this).data('index');
                let quantity = $(`.item-quantity[data-index="${index}"]`).val() || 0;
                let unitPrice = $(`.item-unit-price[data-index="${index}"]`).val() || 0;
                let subtotal = quantity * unitPrice;
                $(`.item-subtotal[data-index="${index}"]`).val(subtotal.toFixed(2));
                calculateInvoiceTotals();
            });

            $(document).on('change', '#taxRate', function() {
                calculateInvoiceTotals();
            });

            // Calculate Invoice Totals
            function calculateInvoiceTotals() {
                let subtotal = 0;
                $('.item-subtotal').each(function() {
                    let value = parseFloat($(this).val()) || 0;
                    subtotal += value;
                });

                let taxRate = parseFloat($('#taxRate').val()) || 0;
                let tax = (subtotal * taxRate) / 100;
                let total = subtotal + tax;

                $('#invoiceSubtotal').text('₹' + subtotal.toFixed(2));
                $('#invoiceTax').text('₹' + tax.toFixed(2));
                $('#invoiceTotal').text('₹' + total.toFixed(2));
            }

            // Submit Invoice
            $('#submitInvoiceBtn').on('click', function() {
                let id = $('#invoiceModal').data('id');
                
                // Collect line items
                let lineItems = [];
                $('.line-item-row').each(function() {
                    let index = $(this).data('item-index');
                    let description = $(`.item-description[data-index="${index}"]`).val();
                    let quantity = $(`.item-quantity[data-index="${index}"]`).val();
                    let unitPrice = $(`.item-unit-price[data-index="${index}"]`).val();
                    let itemType = $(`.item-type[data-index="${index}"]`).val();
                    
                    if (description && quantity && unitPrice) {
                        lineItems.push({
                            item_type: itemType,
                            description: description,
                            quantity: parseInt(quantity),
                            unit_price: parseFloat(unitPrice),
                            tax_rate: parseFloat($('#taxRate').val()) || 0
                        });
                    }
                });

                if (lineItems.length === 0) {
                    error_noti('Please add at least one line item');
                    return;
                }

                let payload = {
                    _token: "{{ csrf_token() }}",
                    property_id: $('#invoiceProperty').val(),
                    guest_id: $('#invoiceGuest').val(),
                    type: $('#invoiceType').val(),
                    issue_date: $('#issueDate').val(),
                    due_date: $('#dueDate').val(),
                    notes: $('#invoiceNotes').val(),
                    line_items: lineItems
                };

                let url = id 
                    ? "{{ route('financials.invoices.update', ':id') }}".replace(':id', id)
                    : "{{ route('financials.invoices.store') }}";

                if (id) {
                    payload._method = 'PUT';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: payload,
                    success: function(res) {
                        success_noti(res.message);
                        $('#invoiceModal').modal('hide');
                        $('#invoicesTable').DataTable().ajax.reload();
                        loadFinancialStats();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save invoice';
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });

            // View Invoice
            $(document).on('click', '.view-invoice', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ route('financials.invoices.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(data) {
                        let html = buildInvoiceViewHTML(data);
                        $('#viewInvoiceContent').html(html);
                        $('#viewInvoiceModal').modal('show');
                    }
                });
            });

            function buildInvoiceViewHTML(invoice) {
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Invoice Information</h6>
                            <p>
                                <strong>Invoice #:</strong> ${invoice.invoice_number}<br>
                                <strong>Type:</strong> ${invoice.type}<br>
                                <strong>Issue Date:</strong> ${invoice.issue_date}<br>
                                <strong>Due Date:</strong> ${invoice.due_date || 'N/A'}<br>
                                <strong>Status:</strong> <span class="badge bg-info">${invoice.status}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Guest Information</h6>
                            <p>
                                <strong>Name:</strong> ${invoice.guest ? invoice.guest.full_name : 'N/A'}<br>
                                <strong>Phone:</strong> ${invoice.guest ? invoice.guest.phone : 'N/A'}<br>
                                <strong>Email:</strong> ${invoice.guest ? invoice.guest.email : 'N/A'}
                            </p>
                        </div>
                        <div class="col-md-12 mt-3">
                            <h6>Line Items</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;
                
                invoice.line_items.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.description}</td>
                            <td>${item.quantity}</td>
                            <td>₹${(item.unit_price_cents / 100).toFixed(2)}</td>
                            <td>₹${(item.subtotal_cents / 100).toFixed(2)}</td>
                            <td>₹${(item.tax_cents / 100).toFixed(2)}</td>
                            <td>₹${(item.total_cents / 100).toFixed(2)}</td>
                        </tr>
                    `;
                });
                
                html += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><strong>₹${(invoice.subtotal_cents / 100).toFixed(2)}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Tax:</strong></td>
                                        <td><strong>₹${(invoice.tax_cents / 100).toFixed(2)}</strong></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>₹${(invoice.total_cents / 100).toFixed(2)}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td colspan="5" class="text-end"><strong>Paid:</strong></td>
                                        <td><strong>₹${(invoice.paid_cents / 100).toFixed(2)}</strong></td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td colspan="5" class="text-end"><strong>Balance:</strong></td>
                                        <td><strong>₹${(invoice.balance_cents / 100).toFixed(2)}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                `;
                
                return html;
            }

            // Add Payment to Invoice
            $(document).on('click', '.add-payment-to-invoice', function() {
                currentInvoiceId = $(this).data('id');
                
                // Get invoice balance
                $.ajax({
                    url: "{{ route('financials.invoices.show', ':id') }}".replace(':id', currentInvoiceId),
                    type: 'GET',
                    success: function(data) {
                        let balance = (data.balance_cents / 100).toFixed(2);
                        $('#invoiceBalance').text('₹' + balance);
                        $('#paymentAmount').val(balance);
                        $('#addPaymentModal').modal('show');
                    }
                });
            });

            // Submit Payment
            $('#submitPaymentBtn').on('click', function() {
                let amount = $('#paymentAmount').val();
                let method = $('#paymentMethod').val();

                if (!amount || amount <= 0) {
                    error_noti('Please enter valid amount');
                    return;
                }

                $.ajax({
                    url: "{{ route('financials.payments.store') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        property_id: $('#propertyFilter').val() || {{ $properties->first()->id ?? 'null' }},
                        invoice_id: currentInvoiceId,
                        amount: amount,
                        method: method,
                        transaction_id: $('#transactionId').val(),
                        cheque_number: $('#chequeNumber').val(),
                        cheque_date: $('#chequeDate').val(),
                        remarks: $('#paymentRemarks').val()
                    },
                    success: function(res) {
                        success_noti(res.message);
                        $('#addPaymentModal').modal('hide');
                        $('#invoicesTable').DataTable().ajax.reload();
                        loadFinancialStats();
                        resetPaymentForm();
                    },
                    error: function(xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to add payment');
                    }
                });
            });

            function resetPaymentForm() {
                $('#paymentAmount').val('');
                $('#transactionId').val('');
                $('#chequeNumber').val('');
                $('#chequeDate').val('');
                $('#paymentRemarks').val('');
            }

            // Cancel Invoice
            $(document).on('click', '.cancel-invoice', function() {
                let id = $(this).data('id');
                
                Swal.fire({
                    title: 'Cancel Invoice',
                    input: 'textarea',
                    inputLabel: 'Cancellation Reason',
                    inputPlaceholder: 'Enter reason...',
                    showCancelButton: true,
                    confirmButtonText: 'Cancel Invoice',
                    confirmButtonColor: '#d33',
                    inputValidator: (value) => {
                        if (!value) return 'Please enter a reason';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('financials.invoices.cancel', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}", reason: result.value },
                            success: function(res) {
                                success_noti(res.message);
                                $('#invoicesTable').DataTable().ajax.reload();
                                loadFinancialStats();
                            },
                            error: function(xhr) {
                                error_noti(xhr.responseJSON?.message ?? 'Failed to cancel invoice');
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>