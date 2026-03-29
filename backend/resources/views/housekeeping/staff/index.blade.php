<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                height: 73vh;
                overflow-y: auto;
            }
            .specialization-badge {
                margin: 2px;
                font-size: 0.75rem;
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

        <table id="staffTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Property</th>
                    <th>Staff Details</th>
                    <th>Contact</th>
                    <th>Employment</th>
                    <th>Shift</th>
                    <th>Workload</th>
                    <th>Tenure</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Staff Modal -->
    <div class="modal fade custom-modal" id="staffModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="staffModalLabel">Add Staff Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Information Section -->
                        <div class="col-12"><h6 class="border-bottom pb-2">Basic Information</h6></div>
                        
                        <div class="col-md-4">
                            <label>Property <span class="text-danger">*</span></label>
                            <select id="staffProperty" class="form-control">
                                <option value="">Select Property</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger d-none" id="staffPropertyError">Property is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Staff Code <span class="text-danger">*</span></label>
                            <input type="text" id="staffCode" class="form-control" placeholder="HK-001">
                            <small class="text-danger d-none" id="staffCodeError">Staff code is required</small>
                        </div>

                        <div class="col-md-4">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="fullName" class="form-control" placeholder="John Doe">
                            <small class="text-danger d-none" id="fullNameError">Full name is required</small>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Contact Information</h6></div>

                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" id="staffEmail" class="form-control" placeholder="email@example.com">
                        </div>

                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" id="staffPhone" class="form-control" placeholder="+1-555-0123">
                        </div>

                        <!-- Employment Details -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Employment Details</h6></div>

                        <div class="col-md-4">
                            <label>Employment Type <span class="text-danger">*</span></label>
                            <select id="employmentType" class="form-control">
                                @foreach($employmentTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Shift <span class="text-danger">*</span></label>
                            <select id="staffShift" class="form-control">
                                @foreach($shifts as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Max Rooms per Day</label>
                            <input type="number" id="maxRooms" class="form-control" placeholder="12" min="1" max="30" value="12">
                            <small class="text-muted">Default: 12 rooms</small>
                        </div>

                        <div class="col-md-6">
                            <label>Joining Date</label>
                            <input type="date" id="joiningDate" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Leaving Date</label>
                            <input type="date" id="leavingDate" class="form-control">
                            <small class="text-muted">Leave empty if currently employed</small>
                        </div>

                        <!-- Specializations -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Specializations</h6></div>

                        <div class="col-12">
                            <label class="d-block mb-2">Select Specializations</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" class="specialization-check" value="checkout-cleaning"> Checkout Cleaning
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" class="specialization-check" value="daily-cleaning"> Daily Cleaning
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" class="specialization-check" value="deep-cleaning"> Deep Cleaning
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" class="specialization-check" value="turndown-service"> Turndown Service
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" class="specialization-check" value="laundry"> Laundry
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="d-block">
                                        <input type="checkbox" class="specialization-check" value="inspection"> Quality Inspection
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Privileges -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Privileges & Status</h6></div>

                        <div class="col-md-4">
                            <label class="d-block">
                                <input type="checkbox" id="isSupervisor"> Supervisor
                            </label>
                            <small class="text-muted">Can inspect and manage tasks</small>
                        </div>

                        <div class="col-md-4">
                            <label class="d-block">
                                <input type="checkbox" id="isActive" checked> Active
                            </label>
                        </div>

                        <!-- User Account Section (Optional) -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">User Account (Optional)</h6></div>

                        <div class="col-md-12">
                            <label class="d-block">
                                <input type="checkbox" id="createUserAccount"> Create Login Account
                            </label>
                            <small class="text-muted">Create a user account for staff to access the system</small>
                        </div>

                        <div id="userAccountFields" class="col-12 d-none">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label>Username/Email <span class="text-danger">*</span></label>
                                    <input type="email" id="username" class="form-control" placeholder="user@example.com">
                                </div>
                                <div class="col-md-4">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" id="password" class="form-control" placeholder="Min 8 characters">
                                </div>
                                <div class="col-md-4">
                                    <label>Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" id="passwordConfirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label>Notes</label>
                            <textarea id="staffNotes" class="form-control" rows="2" placeholder="Additional notes or remarks..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitStaffBtn" class="btn btn-primary">Save Staff</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Workload Modal -->
    <div class="modal fade" id="workloadModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Staff Workload</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="workloadContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Loading workload...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">Mark Attendance</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="attendanceStaffId">
                    
                    <div class="mb-3">
                        <label>Staff: <strong id="attendanceStaffName"></strong></label>
                    </div>

                    <div class="mb-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" id="attendanceDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label>Status <span class="text-danger">*</span></label>
                        <select id="attendanceStatus" class="form-control">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="half-day">Half Day</option>
                            <option value="leave">Leave</option>
                            <option value="sick">Sick Leave</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Check In Time</label>
                            <input type="time" id="checkIn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Check Out Time</label>
                            <input type="time" id="checkOut" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label>Notes</label>
                        <textarea id="attendanceNotes" class="form-control" rows="2" placeholder="Any remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitAttendanceBtn" class="btn btn-success">Mark Attendance</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @if(session('notify'))
            <script>
                alert_box('success', "{{ session('notify') }}");
            </script>
        @endif

        <script type="text/javascript">
            $(document).ready(function () {
                let table = initDataTable({
                    selector: "#staffTable",
                    ajaxUrl: "{{ route('housekeeping-staff.ajax') }}",
                    moduleName: "Add Staff",
                    modalSelector: "#staffModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', filter: 'none', orderable: false, searchable: false },
                        { data: 'property.name', filter: 'text' },
                        { data: 'staff_display', filter: 'none' },
                        { data: 'contact_display', filter: 'none' },
                        { data: 'employment_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "full-time", label: "Full Time" },
                                { value: "part-time", label: "Part Time" },
                                { value: "contract", label: "Contract" },
                                { value: "temporary", label: "Temporary" }
                            ]
                        },
                        { data: 'shift_badge', filter: 'select', options: [
                                { value: "", label: "All" },
                                { value: "morning", label: "Morning" },
                                { value: "afternoon", label: "Afternoon" },
                                { value: "evening", label: "Evening" },
                                { value: "night", label: "Night" },
                                { value: "rotating", label: "Rotating" }
                            ]
                        },
                        { data: 'workload_display', filter: 'none' },
                        { data: 'tenure_display', filter: 'none' },
                        { data: 'status_badge', filter: 'none' },
                        { data: 'action', filter: 'none' }
                    ],
                    columnDefs: [
                        { targets: 0, width: "70px", className: "text-center" },
                        { targets: [4, 5, 6, 8], className: "text-center" },
                        { targets: -1, width: "80px", className: "text-center" }
                    ]
                });

                // Show/hide user account fields
                $('#createUserAccount').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#userAccountFields').removeClass('d-none');
                    } else {
                        $('#userAccountFields').addClass('d-none');
                    }
                });
            });
        </script>

        <!-- Submit Staff -->
        <script>
            $('#submitStaffBtn').on('click', function () {
                let id = $('#staffModal').data('id');
                
                $('.text-danger').addClass('d-none');

                let propertyId = $('#staffProperty').val();
                let staffCode = $('#staffCode').val().trim();
                let fullName = $('#fullName').val().trim();
                let email = $('#staffEmail').val().trim();
                let phone = $('#staffPhone').val().trim();
                let employmentType = $('#employmentType').val();
                let shift = $('#staffShift').val();
                let maxRooms = $('#maxRooms').val();
                let joiningDate = $('#joiningDate').val();
                let leavingDate = $('#leavingDate').val();
                let isSupervisor = $('#isSupervisor').is(':checked') ? 1 : 0;
                let isActive = $('#isActive').is(':checked') ? 1 : 0;
                let notes = $('#staffNotes').val().trim();

                // Get specializations
                let specializations = [];
                $('.specialization-check:checked').each(function() {
                    specializations.push($(this).val());
                });

                // User account fields
                let createUserAccount = $('#createUserAccount').is(':checked') ? 1 : 0;
                let username = $('#username').val().trim();
                let password = $('#password').val();
                let passwordConfirmation = $('#passwordConfirmation').val();

                // Validation
                let isValid = true;
                if (!propertyId) {
                    $('#staffPropertyError').removeClass('d-none');
                    isValid = false;
                }
                if (!staffCode) {
                    $('#staffCodeError').removeClass('d-none');
                    isValid = false;
                }
                if (!fullName) {
                    $('#fullNameError').removeClass('d-none');
                    isValid = false;
                }

                if (!isValid) {
                    error_noti('Please fill all required fields');
                    return;
                }

                let payload = {
                    _token: "{{ csrf_token() }}",
                    _method: id ? 'PUT' : 'POST',
                    property_id: propertyId,
                    staff_code: staffCode,
                    full_name: fullName,
                    email: email || null,
                    phone: phone || null,
                    employment_type: employmentType,
                    shift: shift,
                    max_rooms_per_day: maxRooms || 12,
                    joining_date: joiningDate || null,
                    leaving_date: leavingDate || null,
                    specializations: specializations,
                    is_supervisor: isSupervisor,
                    is_active: isActive,
                    notes: notes || null,
                    create_user_account: createUserAccount,
                    username: username || null,
                    password: password || null,
                    password_confirmation: passwordConfirmation || null
                };

                let url = id
                    ? "{{ route('housekeeping-staff.update', ':id') }}".replace(':id', id)
                    : "{{ route('housekeeping-staff.store') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function (res) {
                        success_noti(res.message);
                        $('#staffModal').modal('hide');
                        resetStaffModal();
                        $('#staffTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save staff';
                        if (xhr.responseJSON?.errors) {
                            let errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });
        </script>

        <!-- Edit Staff -->
        <script>
            $(document).on('click', '.edit-staff', function () {
                let id = $(this).data('id');
                let url = "{{ route('housekeeping-staff.show', ':id') }}".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#staffProperty').val(data.property_id);
                        $('#staffCode').val(data.staff_code);
                        $('#fullName').val(data.full_name);
                        $('#staffEmail').val(data.email || '');
                        $('#staffPhone').val(data.phone || '');
                        $('#employmentType').val(data.employment_type);
                        $('#staffShift').val(data.shift);
                        $('#maxRooms').val(data.max_rooms_per_day);
                        $('#joiningDate').val(data.joining_date_display || '');
                        $('#leavingDate').val(data.leaving_date_display || '');
                        $('#isSupervisor').prop('checked', data.is_supervisor);
                        $('#isActive').prop('checked', data.is_active);
                        $('#staffNotes').val(data.notes || '');

                        // Set specializations
                        $('.specialization-check').prop('checked', false);
                        if (data.specializations && data.specializations.length > 0) {
                            data.specializations.forEach(function(spec) {
                                $('.specialization-check[value="' + spec + '"]').prop('checked', true);
                            });
                        }

                        $('#staffModal')
                            .data('id', data.id)
                            .modal('show');

                        $('#staffModalLabel').text('Edit Staff Member');
                    },
                    error: function () {
                        error_noti('Unable to load staff details');
                    }
                });
            });
        </script>

        <!-- View Workload -->
        <script>
            $(document).on('click', '.view-workload', function () {
                let id = $(this).data('id');
                let url = "{{ route('housekeeping-staff.workload', ':id') }}".replace(':id', id);
                
                $('#workloadContent').html('<div class="text-center py-4"><div class="spinner-border"></div><p class="mt-2">Loading...</p></div>');
                $('#workloadModal').modal('show');

                $.get(url, function(res) {
                    let data = res.data;
                    
                    let html = '<div class="row mb-4">';
                    html += '<div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>' + data.today_total + '</h3><small>Total Tasks</small></div></div></div>';
                    html += '<div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>' + data.today_in_progress + '</h3><small>In Progress</small></div></div></div>';
                    html += '<div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>' + data.today_completed + '</h3><small>Completed</small></div></div></div>';
                    html += '<div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>' + data.max_rooms + '</h3><small>Max Capacity</small></div></div></div>';
                    html += '</div>';

                    if (data.tasks.length > 0) {
                        html += '<h6 class="mb-3">Today\'s Tasks:</h6>';
                        html += '<div class="table-responsive"><table class="table table-sm table-hover">';
                        html += '<thead><tr><th>Room</th><th>Type</th><th>Status</th><th>Priority</th><th>Started</th></tr></thead><tbody>';
                        
                        data.tasks.forEach(function(task) {
                            html += '<tr>';
                            html += '<td>' + task.room_number + '</td>';
                            html += '<td>' + task.task_type.replace('-', ' ') + '</td>';
                            html += '<td><span class="badge bg-info">' + task.status + '</span></td>';
                            html += '<td><span class="badge bg-warning">' + task.priority + '</span></td>';
                            html += '<td>' + (task.started_at || '-') + '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                    } else {
                        html += '<div class="alert alert-info">No tasks assigned for today</div>';
                    }

                    $('#workloadContent').html(html);
                }).fail(function() {
                    $('#workloadContent').html('<div class="alert alert-danger">Failed to load workload</div>');
                });
            });
        </script>

        <!-- Mark Attendance -->
        <script>
            $(document).on('click', '.mark-attendance', function () {
                let id = $(this).data('id');
                let url = "{{ route('housekeeping-staff.show', ':id') }}".replace(':id', id);
                
                $.get(url, function(data) {
                    $('#attendanceStaffId').val(data.id);
                    $('#attendanceStaffName').text(data.full_name);
                    $('#attendanceDate').val('{{ date("Y-m-d") }}');
                    $('#attendanceStatus').val('present');
                    $('#checkIn').val('');
                    $('#checkOut').val('');
                    $('#attendanceNotes').val('');
                    
                    $('#attendanceModal').modal('show');
                });
            });

            $('#submitAttendanceBtn').on('click', function() {
                let staffId = $('#attendanceStaffId').val();
                let date = $('#attendanceDate').val();
                let status = $('#attendanceStatus').val();
                let checkIn = $('#checkIn').val();
                let checkOut = $('#checkOut').val();
                let notes = $('#attendanceNotes').val().trim();

                $.ajax({
                    url: "{{ route('housekeeping-staff.mark-attendance') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        staff_id: staffId,
                        attendance_date: date,
                        status: status,
                        check_in: checkIn || null,
                        check_out: checkOut || null,
                        notes: notes || null
                    },
                    success: function (res) {
                        success_noti(res.message);
                        $('#attendanceModal').modal('hide');
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to mark attendance');
                    }
                });
            });
        </script>

        <!-- Toggle Status -->
        <script>
            $(document).on('click', '.toggle-status', function () {
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ route('housekeeping-staff.toggle-status') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        staff_id: id
                    },
                    success: function (res) {
                        success_noti(res.message);
                        $('#staffTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to update status');
                    }
                });
            });
        </script>

        <!-- Delete Staff -->
        <script>
            $(document).on('click', '.delete-staff', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This staff member will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = "{{ route('housekeeping-staff.destroy', ':id') }}".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
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
                            $('#staffTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: xhr.responseJSON?.message ?? 'Failed to delete staff'
                            });
                        }
                    });
                });
            });
        </script>

        <!-- Reset Modal -->
        <script>
            function resetStaffModal() {
                $('#staffProperty').val('');
                $('#staffCode').val('');
                $('#fullName').val('');
                $('#staffEmail').val('');
                $('#staffPhone').val('');
                $('#employmentType').val('full-time');
                $('#staffShift').val('morning');
                $('#maxRooms').val('12');
                $('#joiningDate').val('');
                $('#leavingDate').val('');
                $('.specialization-check').prop('checked', false);
                $('#isSupervisor').prop('checked', false);
                $('#isActive').prop('checked', true);
                $('#createUserAccount').prop('checked', false);
                $('#userAccountFields').addClass('d-none');
                $('#username').val('');
                $('#password').val('');
                $('#passwordConfirmation').val('');
                $('#staffNotes').val('');
                
                $('.text-danger').addClass('d-none');
                $('#staffModal').removeData('id');
                $('#staffModalLabel').text('Add Staff Member');
            }

            $('#staffModal').on('hidden.bs.modal', resetStaffModal);
        </script>
    @endpush
</x-app-layout>