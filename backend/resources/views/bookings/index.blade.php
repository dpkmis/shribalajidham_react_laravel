<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                overflow-y: auto;
            }
            .room-assignment-row {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                margin-bottom: 10px;
            }
            /* Select2 Fixes */
            .select2-container {
                width: 100% !important;
            }
            .select2-container--default .select2-selection--single {
                height: 38px;
                border: 1px solid #ced4da;
                border-radius: 0.25rem;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px;
                padding-left: 12px;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }
            .select2-dropdown {
                border: 1px solid #ced4da;
            }
            .select2-results__option {
                padding: 6px 12px;
            }
            /* Make sure Select2 appears above modal */
            .select2-container--open {
                z-index: 9999;
            }
            .select2-dropdown {
                z-index: 9999;
            }
        </style>
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    @endpush

    <div class="card p-4">
        <!-- Quick Stats -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 id="totalBookings" class="text-white">0</h5>
                        <small>Total Bookings</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 id="checkedInCount" class="text-white">0</h5>
                        <small>Checked In</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 id="upcomingCount" class="text-white">0</h5>
                        <small>Upcoming</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 id="pendingPayment" class="text-white">₹0</h5>
                        <small>Pending Payment</small>
                    </div>
                </div>
            </div>
        </div>

        <table id="bookingsTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Booking Ref</th>
                    <th>Guest</th>
                    <th>Dates</th>
                    <th>Rooms</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Booking Modal -->
    <div class="modal fade custom-modal" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="bookingModalLabel">Add Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Property -->
                        <div class="col-md-6">
                            <label>Property <span class="text-danger">*</span></label>
                            <select id="bookingProperty" class="form-control">
                                <option value="">Select Property</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger d-none" id="bookingPropertyError">Property is required</small>
                        </div>

                        <!-- Source -->
                        <div class="col-md-6">
                            <label>Booking Source</label>
                            <select id="bookingSource" class="form-control">
                                <option value="walk-in">Walk-in</option>
                                <option value="phone">Phone</option>
                                <option value="email">Email</option>
                                <option value="website">Website</option>
                                <option value="booking.com">Booking.com</option>
                                <option value="airbnb">Airbnb</option>
                                <option value="agoda">Agoda</option>
                                <option value="makemytrip">MakeMyTrip</option>
                                <option value="goibibo">Goibibo</option>
                                <option value="corporate">Corporate</option>
                                <option value="travel-agent">Travel Agent</option>
                            </select>
                        </div>

                        <!-- Guest Selection -->
                        <div class="col-md-12">
                            <label>Guest <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select id="guestSelect" class="form-control" style="width: 100%;">
                                    <option value=""></option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="newGuestBtn">
                                    <i class="bx bx-plus"></i> New Guest
                                </button>
                            </div>
                            <small class="text-muted">Type at least 2 characters to search</small>
                        </div>

                        <!-- New Guest Fields (Hidden by default) -->
                        <div class="col-md-12 d-none" id="newGuestFields">
                            <div class="card">
                                <div class="card-header bg-light">New Guest Details</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" id="guestFirstName" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" id="guestLastName" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Phone <span class="text-danger">*</span></label>
                                            <input type="text" id="guestPhone" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Email</label>
                                            <input type="email" id="guestEmail" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Check-in Date -->
                        <div class="col-md-4">
                            <label>Check-in Date <span class="text-danger">*</span></label>
                            <input type="date" id="checkinDate" class="form-control" min="{{ date('Y-m-d') }}">
                            <small class="text-danger d-none" id="checkinDateError">Check-in date is required</small>
                        </div>

                        <!-- Check-out Date -->
                        <div class="col-md-4">
                            <label>Check-out Date <span class="text-danger">*</span></label>
                            <input type="date" id="checkoutDate" class="form-control">
                            <small class="text-danger d-none" id="checkoutDateError">Check-out date is required</small>
                        </div>

                        <!-- Arrival Time -->
                        <div class="col-md-4">
                            <label>Expected Arrival Time</label>
                            <input type="time" id="arrivalTime" class="form-control" value="14:00">
                        </div>

                        <!-- Number of Adults -->
                        <div class="col-md-4">
                            <label>Adults <span class="text-danger">*</span></label>
                            <input type="number" id="numAdults" class="form-control" min="1" value="2">
                        </div>

                        <!-- Number of Children -->
                        <div class="col-md-4">
                            <label>Children</label>
                            <input type="number" id="numChildren" class="form-control" min="0" value="0">
                        </div>

                        <!-- Number of Infants -->
                        <div class="col-md-4">
                            <label>Infants</label>
                            <input type="number" id="numInfants" class="form-control" min="0" value="0">
                        </div>

                        <!-- Room Assignments Section -->
                        <div class="col-md-12">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6>Room Assignments</h6>
                                <button type="button" class="btn btn-sm btn-success" id="addRoomBtn">
                                    <i class="bx bx-plus"></i> Add Room
                                </button>
                            </div>
                            <div id="roomAssignments">
                                <!-- Room assignment rows will be added here -->
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="col-md-12">
                            <label>Special Requests</label>
                            <textarea id="specialRequests" class="form-control" rows="2" placeholder="High floor, quiet room, extra pillows..."></textarea>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <label>Internal Notes</label>
                            <textarea id="bookingNotes" class="form-control" rows="2" placeholder="Internal notes for staff..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitBookingBtn" class="btn btn-primary">
                        <span id="submitBtnText">Create Booking</span>
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Booking Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Booking Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewBookingContent">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Charge Modal -->
    <div class="modal fade" id="addChargeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Add Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Charge Type <span class="text-danger">*</span></label>
                        <select id="chargeType" class="form-control">
                            <option value="service-charge">Service Charge</option>
                            <option value="food-beverage">Food & Beverage</option>
                            <option value="laundry">Laundry</option>
                            <option value="minibar">Minibar</option>
                            <option value="spa">Spa</option>
                            <option value="transportation">Transportation</option>
                            <option value="extra-bed">Extra Bed</option>
                            <option value="early-checkin">Early Check-in</option>
                            <option value="late-checkout">Late Check-out</option>
                            <option value="pet-charge">Pet Charge</option>
                            <option value="parking">Parking</option>
                            <option value="damage">Damage</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Description <span class="text-danger">*</span></label>
                        <input type="text" id="chargeDescription" class="form-control" placeholder="Breakfast for 2 persons">
                    </div>
                    <div class="mb-3">
                        <label>Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" id="chargeAmount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" id="chargeQuantity" class="form-control" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitChargeBtn" class="btn btn-warning">Add Charge</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">Add Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" id="balanceAlert">
                        <strong>Balance Amount:</strong> <span id="balanceAmount">₹0.00</span>
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
                            <option value="net-banking">Net Banking</option>
                            <option value="cheque">Cheque</option>
                            <option value="wallet">Wallet</option>
                            <option value="bank-transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Transaction ID</label>
                        <input type="text" id="transactionId" class="form-control" placeholder="Optional">
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
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script type="text/javascript">
            let roomAssignmentCounter = 0;
            let currentBookingId = null;

            $(document).ready(function () {
                // Initialize DataTable
                let table = initDataTable({
                    selector: "#bookingsTable",
                    ajaxUrl: "{{ route('bookings.ajax') }}",
                    moduleName: "Add Booking",
                    modalSelector: "#bookingModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'booking_ref_display', filter: 'text' },
                        { data: 'guest_display', filter: 'text' },
                        { data: 'dates_display', filter: 'date' },
                        { data: 'rooms_display', filter: 'none' },
                        { data: 'guests_display', filter: 'none' },
                        { data: 'amount_display', filter: 'none' },
                        {
                            data: 'status_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "pending", label: "Pending" },
                                { value: "confirmed", label: "Confirmed" },
                                { value: "checked-in", label: "Checked In" },
                                { value: "checked-out", label: "Checked Out" },
                                { value: "cancelled", label: "Cancelled" },
                                { value: "no-show", label: "No Show" }                                
                            ]
                        },
                        {
                            data: 'payment_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "unpaid", label: "Unpaid" },
                                { value: "partially-paid", label: "Partially Paid" },
                                { value: "paid", label: "Paid" },                                
                            ]
                        },                                               
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "60px", className: "text-center" },
                        { targets: [5, 6, 7, 8, 9], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });

                // Initialize Select2 for guest search
                initGuestSelect2();

                // Add first room by default
                addRoomAssignment();

                // Load booking statistics
                loadBookingStats();
            });

            // Function to load booking statistics
            function loadBookingStats() {
                $.ajax({
                    url: "{{ route('bookings.stats') }}",
                    type: 'GET',
                    data: {
                        // You can add property filter if needed
                        // property_id: $('#propertyFilter').val()
                    },
                    success: function(response) {
                        if (response.status && response.stats) {
                            // Update stat cards with animation
                            animateValue('totalBookings', 0, response.stats.total_bookings, 1000);
                            animateValue('checkedInCount', 0, response.stats.checked_in, 1000);
                            animateValue('upcomingCount', 0, response.stats.upcoming, 1000);
                            
                            // Format pending payment with currency
                            $('#pendingPayment').text('₹' + formatNumber(response.stats.pending_payment));
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load booking statistics:', xhr);
                        // Set default values on error
                        $('#totalBookings').text('--');
                        $('#checkedInCount').text('--');
                        $('#upcomingCount').text('--');
                        $('#pendingPayment').text('₹--');
                    }
                });
            }

            // Animate number counting
            function animateValue(elementId, start, end, duration = 1000) {

                const element = document.getElementById(elementId);
                if (!element) return;

                // 🔒 HARD GUARDS
                start = Number(start);
                end   = Number(end);

                // If end is invalid → just set start value and exit
                if (isNaN(end)) {
                    element.textContent = start;
                    return;
                }

                // If start is invalid → normalize
                if (isNaN(start)) start = 0;

                // No animation needed
                if (start === end) {
                    element.textContent = end;
                    return;
                }

                const range = end - start;
                const increment = range > 0 ? 1 : -1;
                const stepTime = Math.max(20, Math.abs(Math.floor(duration / range)));

                let current = start;

                const timer = setInterval(() => {
                    current += increment;
                    element.textContent = current;

                    if ((increment > 0 && current >= end) ||
                        (increment < 0 && current <= end)) {

                        element.textContent = end; // exact final value
                        clearInterval(timer);
                    }
                }, stepTime);
            }


            // Format number with commas
            function formatNumber(num) {
                return parseFloat(num).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Refresh stats after booking operations
            function refreshStats() {
                loadBookingStats();
            }

            // Initialize Select2 function
            function initGuestSelect2() {
                if ($('#guestSelect').hasClass('select2-hidden-accessible')) {
                    $('#guestSelect').select2('destroy');
                }

                $('#guestSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Search guest by name, email or phone...',
                    allowClear: true,
                    minimumInputLength: 2,
                    ajax: {
                        url: "{{ route('guests.search') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                _token: "{{ csrf_token() }}"
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function(guest) {
                                    return {
                                        id: guest.id,
                                        text: guest.first_name + ' ' + guest.last_name + ' - ' + guest.phone + (guest.email ? ' (' + guest.email + ')' : '')
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    dropdownParent: $('#bookingModal'),
                    language: {
                        inputTooShort: function() {
                            return 'Please enter 2 or more characters';
                        },
                        noResults: function() {
                            return 'No guest found. Click "New Guest" to add.';
                        },
                        searching: function() {
                            return 'Searching...';
                        }
                    }
                });

                // Handle clear button
                $('#guestSelect').on('select2:clear', function() {
                    console.log('Guest selection cleared');
                });
            }
        </script>

        <!-- Room Assignment Logic -->
        <script>
            $('#addRoomBtn').on('click', function() {
                addRoomAssignment();
            });

            function addRoomAssignment() {
                roomAssignmentCounter++;
                let html = `
                    <div class="room-assignment-row" data-room-index="${roomAssignmentCounter}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label>Room Type <span class="text-danger">*</span></label>
                                <select class="form-control room-type-select" data-index="${roomAssignmentCounter}">
                                    <option value="">Select Room Type</option>
                                    @foreach($roomTypes as $rt)
                                        <option value="{{ $rt->id }}" data-rate="{{ $rt->default_rate }}">
                                            {{ $rt->name }} (₹{{ number_format($rt->default_rate, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Room Number</label>
                                <select class="form-control room-number-select" data-index="${roomAssignmentCounter}">
                                    <option value="">Auto-assign</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Rate/Night (₹)</label>
                                <input type="number" class="form-control room-rate" data-index="${roomAssignmentCounter}" step="0.01" min="0">
                            </div>
                            <div class="col-md-1">
                                <label>Adults</label>
                                <input type="number" class="form-control room-adults" data-index="${roomAssignmentCounter}" value="2" min="1">
                            </div>
                            <div class="col-md-1">
                                <label>Child</label>
                                <input type="number" class="form-control room-children" data-index="${roomAssignmentCounter}" value="0" min="0">
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm w-100 remove-room" data-index="${roomAssignmentCounter}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#roomAssignments').append(html);
            }

            // Remove room assignment
            $(document).on('click', '.remove-room', function() {
                let index = $(this).data('index');
                $(`.room-assignment-row[data-room-index="${index}"]`).remove();
            });

            // When room type is selected, populate rate and check availability
            $(document).on('change', '.room-type-select', function() {
                let index = $(this).data('index');
                let rate = $(this).find(':selected').data('rate');
                $(`.room-rate[data-index="${index}"]`).val(rate);

                // Check availability and populate room numbers
                checkRoomAvailability(index);
            });

            function checkRoomAvailability(index) {
                let propertyId = $('#bookingProperty').val();
                let roomTypeId = $(`.room-type-select[data-index="${index}"]`).val();
                let checkinDate = $('#checkinDate').val();
                let checkoutDate = $('#checkoutDate').val();

                if (!propertyId || !roomTypeId || !checkinDate || !checkoutDate) return;

                $.ajax({
                    url: "{{ route('bookings.check-availability') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        property_id: propertyId,
                        room_type_id: roomTypeId,
                        checkin_date: checkinDate,
                        checkout_date: checkoutDate
                    },
                    success: function(res) {
                        let select = $(`.room-number-select[data-index="${index}"]`);
                        select.empty();
                        select.append('<option value="">Auto-assign</option>');
                        
                        if (res.rooms) {
                            res.rooms.forEach(room => {
                                select.append(`<option value="${room.id}">${room.room_number}</option>`);
                            });
                        }
                    }
                });
            }

            // Toggle new guest fields
            $('#newGuestBtn').on('click', function() {
                $('#newGuestFields').toggleClass('d-none');
                if (!$('#newGuestFields').hasClass('d-none')) {
                    // Clear Select2 selection when showing new guest fields
                    $('#guestSelect').val(null).trigger('change');
                    $('#guestSelect').prop('disabled', true);
                } else {
                    // Re-enable Select2 when hiding new guest fields
                    $('#guestSelect').prop('disabled', false);
                }
            });
        </script>

        <!-- Submit Booking -->
        <script>
            $('#submitBookingBtn').on('click', function () {
                let id = $('#bookingModal').data('id');
                $('.text-danger').addClass('d-none');

                // Validation
                let isValid = true;
                if (!$('#bookingProperty').val()) {
                    $('#bookingPropertyError').removeClass('d-none');
                    isValid = false;
                }
                if (!$('#checkinDate').val()) {
                    $('#checkinDateError').removeClass('d-none');
                    isValid = false;
                }
                if (!$('#checkoutDate').val()) {
                    $('#checkoutDateError').removeClass('d-none');
                    isValid = false;
                }

                // Validate guest (only for new bookings)
                let guestId = $('#guestSelect').val();
                if (!id && !guestId && $('#newGuestFields').hasClass('d-none')) {
                    error_noti('Please select a guest or add new guest');
                    isValid = false;
                }

                if (!isValid) {
                    error_noti('Please fill all required fields');
                    return;
                }

                // For edit, we don't need room assignments (simplified update)
                // You can extend this to update rooms if needed
                let payload = {
                    _token: "{{ csrf_token() }}",
                    _method: id ? 'PUT' : 'POST',
                    guest_id: guestId || null,
                    checkin_date: $('#checkinDate').val(),
                    checkout_date: $('#checkoutDate').val(),
                    number_of_adults: $('#numAdults').val(),
                    number_of_children: $('#numChildren').val(),
                    number_of_infants: $('#numInfants').val(),
                    special_requests: $('#specialRequests').val(),
                    arrival_time: $('#arrivalTime').val(),
                    notes: $('#bookingNotes').val()
                };

                // Add additional fields for new booking
                if (!id) {
                    payload.property_id = $('#bookingProperty').val();
                    payload.source = $('#bookingSource').val();

                    // Collect room assignments
                    let rooms = [];
                    $('.room-assignment-row').each(function() {
                        let index = $(this).data('room-index');
                        let roomTypeId = $(`.room-type-select[data-index="${index}"]`).val();
                        let roomId = $(`.room-number-select[data-index="${index}"]`).val();
                        let rate = $(`.room-rate[data-index="${index}"]`).val();
                        let adults = $(`.room-adults[data-index="${index}"]`).val();
                        let children = $(`.room-children[data-index="${index}"]`).val();

                        if (roomTypeId && rate) {
                            rooms.push({
                                room_type_id: roomTypeId,
                                room_id: roomId || null,
                                rate_per_night: rate,
                                adults: adults,
                                children: children
                            });
                        }
                    });

                    if (rooms.length === 0) {
                        error_noti('Please add at least one room');
                        return;
                    }
                    payload.rooms = rooms;

                    // Add new guest data if creating new
                    if (!guestId && !$('#newGuestFields').hasClass('d-none')) {
                        payload.guest_first_name = $('#guestFirstName').val();
                        payload.guest_last_name = $('#guestLastName').val();
                        payload.guest_phone = $('#guestPhone').val();
                        payload.guest_email = $('#guestEmail').val();
                    }
                }

                let url = id 
                    ? "{{ route('bookings.update', ':id') }}".replace(':id', id)
                    : "{{ route('bookings.store') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#bookingModal').modal('hide');
                        resetBookingModal();
                        $('#bookingsTable').DataTable().ajax.reload(null, false);
                        refreshStats(); // Refresh stats after booking created/updated
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save booking';
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- View Booking -->
        <script>
            $(document).on('click', '.view-booking', function () {
                let id = $(this).data('id');
                let url = "{{ route('bookings.show', ':id') }}".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        let html = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Booking Information</h6>
                                    <p>
                                        <strong>Reference:</strong> ${data.booking_reference}<br>
                                        <strong>Status:</strong> <span class="badge bg-info">${data.status}</span><br>
                                        <strong>Source:</strong> ${data.source}<br>
                                        <strong>Check-in:</strong> ${data.checkin_date}<br>
                                        <strong>Check-out:</strong> ${data.checkout_date}<br>
                                        <strong>Nights:</strong> ${data.nights}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Guest Information</h6>
                                    <p>
                                        <strong>Name:</strong> ${data.guest ? data.guest.full_name : 'N/A'}<br>
                                        <strong>Phone:</strong> ${data.guest ? data.guest.phone : 'N/A'}<br>
                                        <strong>Email:</strong> ${data.guest ? data.guest.email : 'N/A'}
                                    </p>
                                </div>
                                <div class="col-md-12">
                                    <h6>Financial Summary</h6>
                                    <p>
                                        <strong>Total Amount:</strong> ₹${(data.total_amount_cents / 100).toFixed(2)}<br>
                                        <strong>Paid:</strong> ₹${(data.paid_amount_cents / 100).toFixed(2)}<br>
                                        <strong>Balance:</strong> ₹${(data.balance_amount_cents / 100).toFixed(2)}
                                    </p>
                                </div>
                            </div>
                        `;
                        $('#viewBookingContent').html(html);
                        $('#viewBookingModal').modal('show');
                    }
                });
            });
        </script>

        <!-- Edit Booking -->
        <script>
            $(document).on('click', '.edit-booking', function () {
                let id = $(this).data('id');
                let url = "{{ route('bookings.show', ':id') }}".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        // Set booking ID for update
                        $('#bookingModal').data('id', data.id);
                        $('#bookingModalLabel').text('Edit Booking');
                        $('#submitBtnText').text('Update Booking');
                        
                        // Populate basic fields
                        $('#bookingProperty').val(data.property_id).prop('disabled', true); // Disable property change
                        $('#bookingSource').val(data.source);
                        
                        // Populate guest with Select2
                        if (data.guest) {
                            // Destroy and recreate Select2 with the selected option
                            $('#guestSelect').select2('destroy');
                            
                            // Add the option
                            let guestText = data.guest.first_name + ' ' + data.guest.last_name + ' - ' + data.guest.phone;
                            if (data.guest.email) {
                                guestText += ' (' + data.guest.email + ')';
                            }
                            
                            let newOption = new Option(guestText, data.guest.id, true, true);
                            $('#guestSelect').append(newOption);
                            
                            // Reinitialize Select2
                            initGuestSelect2();
                            
                            // Trigger change to update UI
                            $('#guestSelect').trigger('change');
                        }
                        
                        // Populate dates
                        $('#checkinDate').val(data.checkin_date);
                        $('#checkoutDate').val(data.checkout_date);
                        $('#arrivalTime').val(data.arrival_time || '14:00');
                        
                        // Populate guest counts
                        $('#numAdults').val(data.number_of_adults);
                        $('#numChildren').val(data.number_of_children);
                        $('#numInfants').val(data.number_of_infants);
                        
                        // Populate special requests and notes
                        $('#specialRequests').val(data.special_requests || '');
                        $('#bookingNotes').val(data.notes || '');
                        
                        // Clear existing room assignments
                        $('#roomAssignments').empty();
                        roomAssignmentCounter = 0;
                        
                        // Populate room assignments
                        if (data.booking_rooms && data.booking_rooms.length > 0) {
                            data.booking_rooms.forEach(function(bookingRoom) {
                                roomAssignmentCounter++;
                                let roomTypeOptions = '';
                                @foreach($roomTypes as $rt)
                                    roomTypeOptions += `<option value="{{ $rt->id }}" data-rate="{{ $rt->default_rate }}" ${bookingRoom.room_type_id == {{ $rt->id }} ? 'selected' : ''}>{{ $rt->name }} (₹{{ number_format($rt->default_rate, 2) }})</option>`;
                                @endforeach
                                
                                let html = `
                                    <div class="room-assignment-row" data-room-index="${roomAssignmentCounter}" data-booking-room-id="${bookingRoom.id}">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label>Room Type <span class="text-danger">*</span></label>
                                                <select class="form-control room-type-select" data-index="${roomAssignmentCounter}" disabled>
                                                    <option value="">Select Room Type</option>
                                                    ${roomTypeOptions}
                                                </select>
                                                <small class="text-muted">Room type cannot be changed</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Room Number</label>
                                                <select class="form-control room-number-select" data-index="${roomAssignmentCounter}" disabled>
                                                    <option value="">Auto-assign</option>
                                                    ${bookingRoom.room ? `<option value="${bookingRoom.room.id}" selected>${bookingRoom.room.room_number}</option>` : ''}
                                                </select>
                                                <small class="text-muted">Room cannot be changed</small>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Rate/Night (₹)</label>
                                                <input type="number" class="form-control room-rate" data-index="${roomAssignmentCounter}" step="0.01" min="0" value="${(bookingRoom.rate_per_night_cents / 100).toFixed(2)}" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <label>Adults</label>
                                                <input type="number" class="form-control room-adults" data-index="${roomAssignmentCounter}" value="${bookingRoom.adults}" min="1" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <label>Child</label>
                                                <input type="number" class="form-control room-children" data-index="${roomAssignmentCounter}" value="${bookingRoom.children}" min="0" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="bx bx-lock"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#roomAssignments').append(html);
                            });
                        } else {
                            // Add one empty room if no rooms exist
                            addRoomAssignment();
                        }
                        
                        // Hide Add Room button in edit mode
                        $('#addRoomBtn').hide();
                        
                        // Show modal
                        $('#bookingModal').modal('show');
                    },
                    error: function(xhr) {
                        error_noti('Unable to load booking details');
                    }
                });
            });
        </script>

        <!-- Check-in/Check-out/Cancel -->
        <script>
            $(document).on('click', '.checkin-booking', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Check In Guest?',
                    text: 'Mark this booking as checked in',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Check In'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('bookings.checkin', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                success_noti(res.message);
                                $('#bookingsTable').DataTable().ajax.reload(null, false);
                                refreshStats(); // Refresh stats after check-in
                            },
                            error: function(xhr) {
                                error_noti(xhr.responseJSON?.message ?? 'Failed to check in');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.checkout-booking', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Check Out Guest?',
                    text: 'Ensure all payments are settled',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Check Out'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('bookings.checkout', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                success_noti(res.message);
                                $('#bookingsTable').DataTable().ajax.reload(null, false);
                                refreshStats(); // Refresh stats after check-out
                            },
                            error: function(xhr) {
                                error_noti(xhr.responseJSON?.message ?? 'Failed to check out');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.cancel-booking', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Cancel Booking',
                    input: 'textarea',
                    inputLabel: 'Cancellation Reason',
                    inputPlaceholder: 'Enter reason here...',
                    showCancelButton: true,
                    confirmButtonText: 'Cancel Booking',
                    confirmButtonColor: '#d33',
                    inputValidator: (value) => {
                        if (!value) return 'Please enter a reason';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('bookings.cancel', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}", reason: result.value },
                            success: function(res) {
                                success_noti(res.message);
                                $('#bookingsTable').DataTable().ajax.reload(null, false);
                                refreshStats(); // Refresh stats after cancellation
                            },
                            error: function(xhr) {
                                error_noti(xhr.responseJSON?.message ?? 'Failed to cancel');
                            }
                        });
                    }
                });
            });
        </script>

        <!-- Add Charge -->
        <script>
            $(document).on('click', '.manage-charges', function () {
                currentBookingId = $(this).data('id');
                $('#addChargeModal').modal('show');
            });

            $('#submitChargeBtn').on('click', function() {
                let type = $('#chargeType').val();
                let description = $('#chargeDescription').val().trim();
                let amount = $('#chargeAmount').val();
                let quantity = $('#chargeQuantity').val();

                if (!description || !amount) {
                    error_noti('Please fill all required fields');
                    return;
                }

                $.ajax({
                    url: "{{ route('bookings.add-charge', ':id') }}".replace(':id', currentBookingId),
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: type,
                        description: description,
                        amount: amount,
                        quantity: quantity
                    },
                    success: function(res) {
                        success_noti(res.message);
                        $('#addChargeModal').modal('hide');
                        $('#chargeDescription').val('');
                        $('#chargeAmount').val('');
                        $('#chargeQuantity').val('1');
                        $('#bookingsTable').DataTable().ajax.reload(null, false);
                        refreshStats(); // Refresh stats after charge added
                    },
                    error: function(xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to add charge');
                    }
                });
            });
        </script>

        <!-- Add Payment -->
        <script>
            $(document).on('click', '.add-payment', function () {
                currentBookingId = $(this).data('id');
                
                // Get booking details to show balance
                $.ajax({
                    url: "{{ route('bookings.show', ':id') }}".replace(':id', currentBookingId),
                    type: 'GET',
                    success: function(data) {
                        let balance = (data.balance_amount_cents / 100).toFixed(2);
                        $('#balanceAmount').text('₹' + balance);
                        $('#paymentAmount').val(balance);
                        $('#addPaymentModal').modal('show');
                    }
                });
            });

            $('#submitPaymentBtn').on('click', function() {
                let amount = $('#paymentAmount').val();
                let method = $('#paymentMethod').val();
                let transactionId = $('#transactionId').val();
                let remarks = $('#paymentRemarks').val();

                if (!amount || amount <= 0) {
                    error_noti('Please enter a valid amount');
                    return;
                }

                $.ajax({
                    url: "{{ route('bookings.add-payment', ':id') }}".replace(':id', currentBookingId),
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        amount: amount,
                        method: method,
                        transaction_id: transactionId,
                        remarks: remarks
                    },
                    success: function(res) {
                        success_noti(res.message);
                        $('#addPaymentModal').modal('hide');
                        $('#paymentAmount').val('');
                        $('#transactionId').val('');
                        $('#paymentRemarks').val('');
                        $('#bookingsTable').DataTable().ajax.reload(null, false);
                        refreshStats(); // Refresh stats after payment
                    },
                    error: function(xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to add payment');
                    }
                });
            });
        </script>

        <!-- Reset Modal -->
        <script>
            function resetBookingModal() {
                $('#bookingModal').removeData('id');
                $('#bookingModalLabel').text('Add Booking');
                $('#submitBtnText').text('Create Booking');
                
                // Reset regular inputs
                $('#bookingModal').find('input, textarea').val('');
                $('#bookingModal').find('input, select, textarea').prop('disabled', false);
                $('#bookingModal').find('input, select').prop('readonly', false);
                
                // Reset regular selects (not Select2)
                $('#bookingProperty').val('');
                $('#bookingSource').val('walk-in');
                
                // Reset Select2 properly
                $('#guestSelect').val(null).trigger('change');
                
                // Hide new guest fields
                $('#newGuestFields').addClass('d-none');
                
                // Reset room assignments
                $('#roomAssignments').empty();
                
                // Reset default values
                $('#numAdults').val('2');
                $('#numChildren').val('0');
                $('#numInfants').val('0');
                $('#arrivalTime').val('14:00');
                
                // Show add room button
                $('#addRoomBtn').show();
                
                // Hide errors
                $('.text-danger').addClass('d-none');
                
                // Reset counter and add first room
                roomAssignmentCounter = 0;
                addRoomAssignment();
                
                // Reinitialize Select2 to ensure it's working
                initGuestSelect2();
            }

            $('#bookingModal').on('hidden.bs.modal', resetBookingModal);
            
            // Reinitialize Select2 when modal opens
            $('#bookingModal').on('shown.bs.modal', function() {
                // Make sure Select2 dropdown appears above modal
                if (!$('#bookingModal').data('id')) {
                    initGuestSelect2();
                }
            });
        </script>
    @endpush
</x-app-layout>