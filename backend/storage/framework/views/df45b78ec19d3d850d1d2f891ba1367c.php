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
            .avatar-circle {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #0d6efd;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 12px;
            }
            .nav-tabs .nav-link {
                color: #6c757d;
            }
            .nav-tabs .nav-link.active {
                color: #0d6efd;
                font-weight: 600;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <div class="card p-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <table id="guestsTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Guest Name</th>
                    <th>Contact</th>
                    <th>Type</th>
                    <th>Bookings</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Guest Modal -->
    <div class="modal fade custom-modal" id="guestModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="guestModalLabel">Add Guest</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" id="guestTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button">
                                Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button">
                                Contact Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button">
                                Business Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button">
                                Preferences
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                                Documents
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="guestTabContent">
                        
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Property</label>
                                    <select id="guestProperty" class="form-control">
                                        <option value="">Select Property</option>
                                        <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Guest Type <span class="text-danger">*</span></label>
                                    <select id="guestType" class="form-control">
                                        <option value="individual">Individual</option>
                                        <option value="corporate">Corporate</option>
                                        <option value="group">Group</option>
                                        <option value="vip">VIP</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <select id="title" class="form-control">
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Ms">Ms</option>
                                        <option value="Dr">Dr</option>
                                        <option value="Prof">Prof</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" id="firstName" class="form-control">
                                    <small class="text-danger d-none" id="firstNameError">First name is required</small>
                                </div>

                                <div class="col-md-3">
                                    <label>Middle Name</label>
                                    <input type="text" id="middleName" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" id="lastName" class="form-control">
                                    <small class="text-danger d-none" id="lastNameError">Last name is required</small>
                                </div>

                                <div class="col-md-4">
                                    <label>Gender</label>
                                    <select id="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Date of Birth</label>
                                    <input type="date" id="dob" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Nationality</label>
                                    <input type="text" id="nationality" class="form-control" placeholder="Indian, American...">
                                </div>

                                <div class="col-md-6">
                                    <label class="d-block mt-2">
                                        <input type="checkbox" id="isVip"> Mark as VIP Guest
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Tab -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" id="email" class="form-control" placeholder="guest@example.com">
                                </div>

                                <div class="col-md-6">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" id="phone" class="form-control" placeholder="+91 9876543210">
                                    <small class="text-danger d-none" id="phoneError">Phone is required</small>
                                </div>

                                <div class="col-md-6">
                                    <label>Alternate Phone</label>
                                    <input type="text" id="alternatePhone" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>WhatsApp Number</label>
                                    <input type="text" id="whatsapp" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label>Address Line 1</label>
                                    <input type="text" id="addressLine1" class="form-control" placeholder="Street Address">
                                </div>

                                <div class="col-md-12">
                                    <label>Address Line 2</label>
                                    <input type="text" id="addressLine2" class="form-control" placeholder="Apartment, Suite, etc.">
                                </div>

                                <div class="col-md-3">
                                    <label>City</label>
                                    <input type="text" id="city" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>State</label>
                                    <input type="text" id="state" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Country</label>
                                    <input type="text" id="country" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label>Postal Code</label>
                                    <input type="text" id="postalCode" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Business Info Tab -->
                        <div class="tab-pane fade" id="business" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Company Name</label>
                                    <input type="text" id="companyName" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Designation</label>
                                    <input type="text" id="companyDesignation" class="form-control" placeholder="Manager, Director...">
                                </div>

                                <div class="col-md-6">
                                    <label>GSTIN (GST Number)</label>
                                    <input type="text" id="gstin" class="form-control" placeholder="22AAAAA0000A1Z5">
                                </div>
                            </div>
                        </div>

                        <!-- Preferences Tab -->
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Preferred Language</label>
                                    <select id="preferredLanguage" class="form-control">
                                        <option value="en">English</option>
                                        <option value="hi">Hindi</option>
                                        <option value="es">Spanish</option>
                                        <option value="fr">French</option>
                                        <option value="de">German</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Meal Preference</label>
                                    <select id="mealPreference" class="form-control">
                                        <option value="">Select Preference</option>
                                        <option value="veg">Vegetarian</option>
                                        <option value="non-veg">Non-Vegetarian</option>
                                        <option value="vegan">Vegan</option>
                                        <option value="jain">Jain</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label>Special Requests</label>
                                    <textarea id="specialRequests" class="form-control" rows="3" placeholder="High floor, quiet room, extra pillows..."></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label>Allergies</label>
                                    <textarea id="allergies" class="form-control" rows="2" placeholder="Food allergies, medication allergies..."></textarea>
                                </div>

                                <div class="col-md-12">
                                    <h6 class="mt-3">Consent Preferences</h6>
                                </div>

                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="marketingConsent"> Marketing Communications
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="smsConsent" checked> SMS Notifications
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" id="emailConsent" checked> Email Notifications
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label>Profile Photo</label>
                                    <input type="file" id="photoUpload" class="form-control" accept="image/*">
                                    <small class="text-muted">Max 2MB, JPEG/PNG only</small>
                                </div>

                                <div class="col-md-4">
                                    <label>ID Type</label>
                                    <select id="idType" class="form-control">
                                        <option value="">Select ID Type</option>
                                        <option value="passport">Passport</option>
                                        <option value="aadhar">Aadhar Card</option>
                                        <option value="driving-license">Driving License</option>
                                        <option value="voter-id">Voter ID</option>
                                        <option value="pan">PAN Card</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>ID Number</label>
                                    <input type="text" id="idNumber" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>ID Expiry Date</label>
                                    <input type="date" id="idExpiryDate" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label>Upload ID Document</label>
                                    <input type="file" id="idDocumentUpload" class="form-control" accept="image/*,application/pdf">
                                    <small class="text-muted">Max 5MB, PDF/JPEG/PNG only</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitGuestBtn" class="btn btn-primary">Save Guest</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Guest Modal (Read Only) -->
    <div class="modal fade" id="viewGuestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Guest Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewGuestContent">
                    <!-- Content loaded dynamically -->
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
                    selector: "#guestsTable",
                    ajaxUrl: "<?php echo e(route('guests.ajax')); ?>",
                    moduleName: "Add Guest",
                    modalSelector: "#guestModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'full_name_display', filter: 'text' },
                        { data: 'contact_display', filter: 'none' },
                        {
                            data: 'guest_type_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "individual", label: "Individual" },
                                { value: "corporate", label: "Corporate" },
                                { value: "group", label: "Group" },
                                { value: "vip", label: "VIP" }                                
                            ]
                        },                                                
                        { data: 'bookings_count', filter: 'none' },                        
                        {
                            data: 'status_display', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "active", label: "Active" },
                                { value: "blacklisted", label: "Blacklisted" }
                            ]
                        },
                        { data: 'created_at', filter: 'date' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "60px", className: "text-center" },
                        { targets: [4, 5, 6], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });
            });
        </script>

        <!-- Submit Guest -->
        <script>
            $('#submitGuestBtn').on('click', function () {
                let id = $('#guestModal').data('id');
                $('.text-danger').addClass('d-none');

                // Validation
                let isValid = true;
                if (!$('#firstName').val().trim()) {
                    $('#firstNameError').removeClass('d-none');
                    isValid = false;
                }
                if (!$('#lastName').val().trim()) {
                    $('#lastNameError').removeClass('d-none');
                    isValid = false;
                }
                if (!$('#phone').val().trim()) {
                    $('#phoneError').removeClass('d-none');
                    isValid = false;
                }

                if (!isValid) {
                    error_noti('Please fill all required fields');
                    return;
                }

                let formData = new FormData();
                formData.append('_token', "<?php echo e(csrf_token()); ?>");
                formData.append('_method', id ? 'PUT' : 'POST');
                formData.append('property_id', $('#guestProperty').val() || '');
                formData.append('title', $('#title').val());
                formData.append('first_name', $('#firstName').val());
                formData.append('middle_name', $('#middleName').val());
                formData.append('last_name', $('#lastName').val());
                formData.append('gender', $('#gender').val() || '');
                formData.append('dob', $('#dob').val() || '');
                formData.append('nationality', $('#nationality').val() || '');
                formData.append('email', $('#email').val() || '');
                formData.append('phone', $('#phone').val());
                formData.append('alternate_phone', $('#alternatePhone').val() || '');
                formData.append('whatsapp', $('#whatsapp').val() || '');
                formData.append('address_line1', $('#addressLine1').val() || '');
                formData.append('address_line2', $('#addressLine2').val() || '');
                formData.append('city', $('#city').val() || '');
                formData.append('state', $('#state').val() || '');
                formData.append('country', $('#country').val() || '');
                formData.append('postal_code', $('#postalCode').val() || '');
                formData.append('company_name', $('#companyName').val() || '');
                formData.append('company_designation', $('#companyDesignation').val() || '');
                formData.append('gstin', $('#gstin').val() || '');
                formData.append('id_type', $('#idType').val() || '');
                formData.append('id_number', $('#idNumber').val() || '');
                formData.append('id_expiry_date', $('#idExpiryDate').val() || '');
                formData.append('preferred_language', $('#preferredLanguage').val());
                formData.append('meal_preference', $('#mealPreference').val() || '');
                formData.append('special_requests', $('#specialRequests').val() || '');
                formData.append('allergies', $('#allergies').val() || '');
                formData.append('guest_type', $('#guestType').val());
                formData.append('is_vip', $('#isVip').is(':checked') ? 1 : 0);
                formData.append('marketing_consent', $('#marketingConsent').is(':checked') ? 1 : 0);
                formData.append('sms_consent', $('#smsConsent').is(':checked') ? 1 : 0);
                formData.append('email_consent', $('#emailConsent').is(':checked') ? 1 : 0);

                // File uploads
                if ($('#photoUpload')[0].files[0]) {
                    formData.append('photo', $('#photoUpload')[0].files[0]);
                }
                if ($('#idDocumentUpload')[0].files[0]) {
                    formData.append('id_document', $('#idDocumentUpload')[0].files[0]);
                }

                let url = id
                    ? "<?php echo e(route('guests.update', ':id')); ?>".replace(':id', id)
                    : "<?php echo e(route('guests.store')); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        success_noti(res.message);
                        $('#guestModal').modal('hide');
                        resetGuestModal();
                        $('#guestsTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save guest';
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- View/Edit/Delete/Blacklist Scripts -->
        <script>
            // View Guest
            $(document).on('click', '.view-guest', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('guests.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        let html = `
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>${data.full_name}</h5>
                                    <p><strong>Email:</strong> ${data.email || 'N/A'}<br>
                                       <strong>Phone:</strong> ${data.phone}<br>
                                       <strong>Type:</strong> ${data.guest_type}<br>
                                       <strong>Total Bookings:</strong> ${data.total_bookings}<br>
                                       <strong>Total Spent:</strong> ₹${data.total_spent}</p>
                                </div>
                            </div>
                        `;
                        $('#viewGuestContent').html(html);
                        $('#viewGuestModal').modal('show');
                    }
                });
            });

            // Edit Guest
            $(document).on('click', '.edit-guest', function () {
                let id = $(this).data('id');
                let url = "<?php echo e(route('guests.show', ':id')); ?>".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#guestProperty').val(data.property_id || '');
                        $('#title').val(data.title);
                        $('#firstName').val(data.first_name);
                        $('#middleName').val(data.middle_name || '');
                        $('#lastName').val(data.last_name);
                        $('#gender').val(data.gender || '');
                        $('#dob').val(data.dob || '');
                        $('#nationality').val(data.nationality || '');
                        $('#email').val(data.email || '');
                        $('#phone').val(data.phone);
                        $('#alternatePhone').val(data.alternate_phone || '');
                        $('#whatsapp').val(data.whatsapp || '');
                        $('#addressLine1').val(data.address_line1 || '');
                        $('#addressLine2').val(data.address_line2 || '');
                        $('#city').val(data.city || '');
                        $('#state').val(data.state || '');
                        $('#country').val(data.country || '');
                        $('#postalCode').val(data.postal_code || '');
                        $('#companyName').val(data.company_name || '');
                        $('#companyDesignation').val(data.company_designation || '');
                        $('#gstin').val(data.gstin || '');
                        $('#idType').val(data.id_type || '');
                        $('#idNumber').val(data.id_number || '');
                        $('#idExpiryDate').val(data.id_expiry_date || '');
                        $('#preferredLanguage').val(data.preferred_language);
                        $('#mealPreference').val(data.meal_preference || '');
                        $('#specialRequests').val(data.special_requests || '');
                        $('#allergies').val(data.allergies || '');
                        $('#guestType').val(data.guest_type);
                        $('#isVip').prop('checked', data.is_vip);
                        $('#marketingConsent').prop('checked', data.marketing_consent);
                        $('#smsConsent').prop('checked', data.sms_consent);
                        $('#emailConsent').prop('checked', data.email_consent);

                        $('#guestModal').data('id', data.id).modal('show');
                        $('#guestModalLabel').text('Edit Guest');
                    }
                });
            });

            // Delete Guest
            $(document).on('click', '.delete-guest', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This guest will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "<?php echo e(route('guests.destroy', ':id')); ?>".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: { _token: "<?php echo e(csrf_token()); ?>", _method: "DELETE" },
                        success: function (res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#guestsTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message ?? 'Failed to delete', 'error');
                        }
                    });
                });
            });

            // Blacklist Guest
            $(document).on('click', '.blacklist-guest', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Blacklist Guest',
                    input: 'textarea',
                    inputLabel: 'Reason for blacklisting',
                    inputPlaceholder: 'Enter reason here...',
                    showCancelButton: true,
                    confirmButtonText: 'Blacklist',
                    confirmButtonColor: '#d33',
                    inputValidator: (value) => {
                        if (!value) return 'Please enter a reason';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(route('guests.blacklist', ':id')); ?>".replace(':id', id),
                            type: 'POST',
                            data: { _token: "<?php echo e(csrf_token()); ?>", reason: result.value },
                            success: function(res) {
                                success_noti(res.message);
                                $('#guestsTable').DataTable().ajax.reload(null, false);
                            }
                        });
                    }
                });
            });

            // Whitelist Guest
            $(document).on('click', '.whitelist-guest', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Remove from Blacklist?',
                    text: 'This guest will be able to book again',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, whitelist'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(route('guests.whitelist', ':id')); ?>".replace(':id', id),
                            type: 'POST',
                            data: { _token: "<?php echo e(csrf_token()); ?>" },
                            success: function(res) {
                                success_noti(res.message);
                                $('#guestsTable').DataTable().ajax.reload(null, false);
                            }
                        });
                    }
                });
            });

            function resetGuestModal() {
                $('#guestModal').removeData('id');
                $('#guestModalLabel').text('Add Guest');
                $('#guestModal').find('input, select, textarea').val('');
                $('#guestModal').find('input[type="checkbox"]').prop('checked', false);
                $('#smsConsent, #emailConsent').prop('checked', true);
                $('.text-danger').addClass('d-none');
                $('#guestTabs a:first').tab('show');
            }

            $('#guestModal').on('hidden.bs.modal', resetGuestModal);
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
<?php endif; ?><?php /**PATH /var/www/html/wavestube/resources/views/guests/index.blade.php ENDPATH**/ ?>