<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                max-height: 73vh;
                overflow-y: auto;
            }
            .stats-card {
                transition: transform 0.2s;
            }
            .stats-card:hover {
                transform: translateY(-2px);
            }
            .transaction-filters {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
        </style>
    @endpush

    <div class="card p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Quick Filters -->
        <div class="transaction-filters">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Property</label>
                    <select id="filterProperty" class="form-select">
                        <option value="">All Properties</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select id="filterCategory" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Transaction Type</label>
                    <select id="filterType" class="form-select">
                        <option value="">All Types</option>
                        @foreach($transactionTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="applyFilters" class="btn btn-primary w-100">
                        <i class="bx bx-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Dashboard -->
        <div class="row mb-3">
            <div class="col-md-2">
                <div class="card bg-success text-white stats-card">
                    <div class="card-body py-2 text-center">
                        <small>Stock In</small>
                        <h5 class="mb-0" id="stockInCount">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white stats-card">
                    <div class="card-body py-2 text-center">
                        <small>Stock Out</small>
                        <h5 class="mb-0" id="stockOutCount">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white stats-card">
                    <div class="card-body py-2 text-center">
                        <small>Adjustments</small>
                        <h5 class="mb-0" id="adjustmentCount">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white stats-card">
                    <div class="card-body py-2 text-center">
                        <small>Transfers</small>
                        <h5 class="mb-0" id="transferCount">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-dark text-white stats-card">
                    <div class="card-body py-2 text-center">
                        <small>Damage</small>
                        <h5 class="mb-0" id="damageCount">0</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white stats-card">
                    <div class="card-body py-2 text-center">
                        <small>Expired</small>
                        <h5 class="mb-0" id="expiredCount">0</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-3">
            <button class="btn btn-primary" id="generateReportBtn">
                <i class="bx bx-bar-chart"></i> Generate Report
            </button>
            <button class="btn btn-success" id="exportBtn">
                <i class="bx bx-download"></i> Export
            </button>
            <button class="btn btn-info" id="dailySummaryBtn">
                <i class="bx bx-calendar"></i> Daily Summary
            </button>
        </div>

        <table id="transactionsTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Date & Time</th>
                    <th>Item</th>
                    <th>Transaction Type</th>
                    <th>Quantity</th>
                    <th>Balance After</th>
                    <th>Value</th>
                    <th>User</th>
                    <th>Reference</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- View Transaction Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Loading details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Generate Transaction Report</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Start Date <span class="text-danger">*</span></label>
                        <input type="date" id="reportStartDate" class="form-control" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="mb-3">
                        <label>End Date <span class="text-danger">*</span></label>
                        <input type="date" id="reportEndDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label>Property</label>
                        <select id="reportProperty" class="form-select">
                            <option value="">All Properties</option>
                            @foreach($properties as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Category</label>
                        <select id="reportCategory" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Transaction Type</label>
                        <select id="reportType" class="form-select">
                            <option value="">All Types</option>
                            @foreach($transactionTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="generateReportConfirmBtn">Generate Report</button>
                </div>
            </div>
        </div>
    </div>
    @push('script')
        <script src="{{ asset('assets/js/inventory/transactions/index.js') }}"></script>
    @endpush
</x-app-layout>