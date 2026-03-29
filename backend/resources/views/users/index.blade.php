<x-app-layout>
    @push('style')
        <style>
            .custom-modal .modal-body {
                max-height: 75vh;
                overflow-y: auto;
            }
            .stat-card {
                transition: transform 0.2s;
            }
            .stat-card:hover {
                transform: translateY(-2px);
            }
            .role-checkbox-group {
                max-height: 300px;
                overflow-y: auto;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                padding: 15px;
            }
        </style>
    @endpush

    <div class="card p-4">
        <!-- Quick Stats -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white stat-card">
                    <div class="card-body">
                        <h5 id="totalUsers" class="text-white">0</h5>
                        <small>Total Users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white stat-card">
                    <div class="card-body">
                        <h5 id="activeUsers" class="text-white">0</h5>
                        <small>Active Users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white stat-card">
                    <div class="card-body">
                        <h5 id="inactiveUsers" class="text-white">0</h5>
                        <small>Inactive Users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white stat-card">
                    <div class="card-body">
                        <h5 id="onlineUsers" class="text-white">0</h5>
                        <small>Online Now</small>
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
                <select id="roleFilter" class="form-control">
                    <option value="">All Roles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-control">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <table id="usersTable" class="table table-striped table-bordered table-hover mt-3">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Property</th>
                    <th>Roles</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal fade custom-modal" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="userModalLabel">Add User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Property -->
                        <div class="col-md-6">
                            <label>Property</label>
                            <select id="userProperty" class="form-control">
                                <option value="">Global User</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="userName" class="form-control" placeholder="John Doe">
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" id="userEmail" class="form-control" placeholder="john@example.com">
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" id="userPhone" class="form-control" placeholder="+91 9876543210">
                        </div>

                        <!-- Password (only for new users) -->
                        <div class="col-md-6" id="passwordField">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" id="userPassword" class="form-control">
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6" id="confirmPasswordField">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="userPasswordConfirmation" class="form-control">
                        </div>

                        <!-- Designation -->
                        <div class="col-md-6">
                            <label>Designation</label>
                            <input type="text" id="userDesignation" class="form-control" placeholder="Manager, Receptionist, etc.">
                        </div>

                        <!-- Department -->
                        <div class="col-md-6">
                            <label>Department</label>
                            <input type="text" id="userDepartment" class="form-control" placeholder="Front Desk, Housekeeping, etc.">
                        </div>

                        <!-- Date of Joining -->
                        <div class="col-md-6">
                            <label>Date of Joining</label>
                            <input type="date" id="userDateOfJoining" class="form-control">
                        </div>

                        <!-- Roles -->
                        <div class="col-md-12">
                            <label>Assign Roles <span class="text-danger">*</span></label>
                            <div class="role-checkbox-group">
                                @foreach($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input user-role" type="checkbox" value="{{ $role->id }}" id="role{{ $role->id }}">
                                        <label class="form-check-label" for="role{{ $role->id }}">
                                            <strong>{{ $role->name }}</strong>
                                            @if($role->property)
                                                <span class="badge bg-secondary ms-2">{{ $role->property->name }}</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="submitUserBtn" class="btn btn-primary">
                        <span id="submitUserBtnText">Create User</span>
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewUserContent">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>New Password <span class="text-danger">*</span></label>
                        <input type="password" id="resetPassword" class="form-control">
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" id="resetPasswordConfirmation" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitResetPasswordBtn" class="btn btn-warning">Reset Password</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Activity Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title text-white">User Activity</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="activityContent">
                        <!-- Activity logs loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            let currentUserId = null;

            $(document).ready(function () {
                // Initialize DataTable
                let table = initDataTable({
                    selector: "#usersTable",
                    ajaxUrl: "{{ route('users.ajax') }}",
                    moduleName: "Add User",
                    modalSelector: "#userModal",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name_display' },
                        { data: 'property_display' },
                        { data: 'roles_display' },
                        { data: 'contact_display' },
                        { data: 'status_badge' },
                        { data: 'last_login_display' },
                        { data: 'action', orderable: false, searchable: false }
                    ],
                    columnDefs: [
                        { targets: 0, width: "60px", className: "text-center" },
                        { targets: [5, 6, 7], className: "text-center" },
                        { targets: -1, width: "80px" }
                    ]
                });

                // Load user stats
                loadUserStats();

                // Apply filters
                $('#propertyFilter, #roleFilter, #statusFilter').on('change', function() {
                    table.ajax.reload();
                });
            });

            // Load User Stats
            function loadUserStats() {
                $.ajax({
                    url: "{{ route('users.stats') }}",
                    type: 'GET',
                    data: {
                        property_id: $('#propertyFilter').val()
                    },
                    success: function(response) {
                        if (response.status && response.stats) {
                            $('#totalUsers').text(response.stats.total_users);
                            $('#activeUsers').text(response.stats.active_users);
                            $('#inactiveUsers').text(response.stats.inactive_users);
                            $('#onlineUsers').text(response.stats.online_users);
                        }
                    }
                });
            }

            // Submit User
            $('#submitUserBtn').on('click', function() {
                let id = $('#userModal').data('id');
                
                // Collect roles
                let roles = [];
                $('.user-role:checked').each(function() {
                    roles.push($(this).val());
                });

                // Basic validation
                if (!$('#userName').val().trim()) {
                    error_noti('Name is required');
                    return;
                }
                if (!$('#userEmail').val().trim()) {
                    error_noti('Email is required');
                    return;
                }
                if (!id && !$('#userPassword').val()) {
                    error_noti('Password is required');
                    return;
                }
                if (!id && $('#userPassword').val() !== $('#userPasswordConfirmation').val()) {
                    error_noti('Passwords do not match');
                    return;
                }
                if (roles.length === 0) {
                    error_noti('Please assign at least one role');
                    return;
                }

                let payload = {
                    _token: "{{ csrf_token() }}",
                    _method: id ? 'PUT' : 'POST',
                    property_id: $('#userProperty').val() || null,
                    name: $('#userName').val(),
                    email: $('#userEmail').val(),
                    phone: $('#userPhone').val(),
                    designation: $('#userDesignation').val(),
                    department: $('#userDepartment').val(),
                    date_of_joining: $('#userDateOfJoining').val(),
                    roles: roles
                };

                // Add password for new users
                if (!id) {
                    payload.password = $('#userPassword').val();
                    payload.password_confirmation = $('#userPasswordConfirmation').val();
                }

                let url = id 
                    ? "{{ route('users.update', ':id') }}".replace(':id', id)
                    : "{{ route('users.store') }}";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: payload,
                    success: function(res) {
                        success_noti(res.message);
                        $('#userModal').modal('hide');
                        resetUserModal();
                        $('#usersTable').DataTable().ajax.reload(null, false);
                        loadUserStats();
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message ?? 'Failed to save user';
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        error_noti(message);
                    }
                });
            });

            // View User
            $(document).on('click', '.view-user', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ route('users.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(data) {
                        let html = buildUserViewHTML(data);
                        $('#viewUserContent').html(html);
                        $('#viewUserModal').modal('show');
                    }
                });
            });

            function buildUserViewHTML(data) {
                let user = data.user;
                let stats = data.stats;
                
                let html = `
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        ${user.avatar 
                                            ? '<img src="' + user.avatar + '" class="rounded-circle" width="100" height="100">'
                                            : '<div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:100px;height:100px;font-size:2rem;">' + user.name.charAt(0).toUpperCase() + '</div>'
                                        }
                                    </div>
                                    <h5>${user.name}</h5>
                                    <p class="text-muted">${user.email}</p>
                                    ${user.designation ? '<p class="mb-0"><strong>' + user.designation + '</strong></p>' : ''}
                                    ${user.department ? '<p class="text-muted">' + user.department + '</p>' : ''}
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">Statistics</div>
                                <div class="card-body">
                                    <p><strong>Total Logins:</strong> ${stats.total_logins}</p>
                                    <p><strong>Last Login:</strong> ${stats.last_login || 'Never'}</p>
                                    <p><strong>Active Sessions:</strong> ${stats.active_sessions}</p>
                                    <p><strong>Roles:</strong> ${stats.role_count}</p>
                                    <p class="mb-0"><strong>Permissions:</strong> ${stats.permission_count}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header">User Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Property:</strong> ${user.property ? user.property.name : 'Global'}</p>
                                            <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
                                            <p><strong>Status:</strong> <span class="badge bg-${user.is_active ? 'success' : 'secondary'}">${user.status_label}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Date of Joining:</strong> ${user.date_of_joining || 'N/A'}</p>
                                            <p><strong>Email Verified:</strong> ${user.email_verified ? 'Yes' : 'No'}</p>
                                            <p><strong>Created:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">Assigned Roles</div>
                                <div class="card-body">
                `;
                
                user.roles.forEach(role => {
                    html += `
                        <div class="mb-3">
                            <h6>${role.name}</h6>
                            <div class="d-flex flex-wrap gap-1">
                    `;
                    
                    role.permissions.forEach(perm => {
                        html += `<span class="badge bg-secondary">${perm.name}</span>`;
                    });
                    
                    html += `
                            </div>
                        </div>
                    `;
                });
                
                html += `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                return html;
            }

            // Edit User
            $(document).on('click', '.edit-user', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ route('users.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(data) {
                        let user = data.user;
                        
                        $('#userModal').data('id', user.id);
                        $('#userModalLabel').text('Edit User');
                        $('#submitUserBtnText').text('Update User');
                        
                        $('#userProperty').val(user.property_id);
                        $('#userName').val(user.name);
                        $('#userEmail').val(user.email);
                        $('#userPhone').val(user.phone);
                        $('#userDesignation').val(user.designation);
                        $('#userDepartment').val(user.department);
                        $('#userDateOfJoining').val(user.date_of_joining);
                        
                        // Hide password fields for edit
                        $('#passwordField, #confirmPasswordField').hide();
                        
                        // Check roles
                        $('.user-role').prop('checked', false);
                        user.roles.forEach(role => {
                            $('.user-role[value="' + role.id + '"]').prop('checked', true);
                        });
                        
                        $('#userModal').modal('show');
                    }
                });
            });

            // Activate/Deactivate
            $(document).on('click', '.activate-user', function() {
                let id = $(this).data('id');
                updateUserStatus(id, 'activate');
            });

            $(document).on('click', '.deactivate-user', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Deactivate User?',
                    text: 'This will end all active sessions',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Deactivate'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateUserStatus(id, 'deactivate');
                    }
                });
            });

            function updateUserStatus(id, action) {
                $.ajax({
                    url: "{{ route('users.status', ':id') }}".replace(':id', id).replace('status', action),
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        success_noti(res.message);
                        $('#usersTable').DataTable().ajax.reload(null, false);
                        loadUserStats();
                    },
                    error: function(xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Operation failed');
                    }
                });
            }

            // Unlock User
            $(document).on('click', '.unlock-user', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ route('users.unlock', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        success_noti(res.message);
                        $('#usersTable').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to unlock user');
                    }
                });
            });

            // Reset Password
            $(document).on('click', '.reset-password', function() {
                currentUserId = $(this).data('id');
                $('#resetPassword, #resetPasswordConfirmation').val('');
                $('#resetPasswordModal').modal('show');
            });

            $('#submitResetPasswordBtn').on('click', function() {
                let password = $('#resetPassword').val();
                let confirmation = $('#resetPasswordConfirmation').val();
                
                if (!password || password.length < 8) {
                    error_noti('Password must be at least 8 characters');
                    return;
                }
                
                if (password !== confirmation) {
                    error_noti('Passwords do not match');
                    return;
                }
                
                $.ajax({
                    url: "{{ route('users.reset-password', ':id') }}".replace(':id', currentUserId),
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        password: password,
                        password_confirmation: confirmation
                    },
                    success: function(res) {
                        success_noti(res.message);
                        $('#resetPasswordModal').modal('hide');
                    },
                    error: function(xhr) {
                        error_noti(xhr.responseJSON?.message ?? 'Failed to reset password');
                    }
                });
            });

            // View Activity
            $(document).on('click', '.view-activity', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ route('users.activity', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(data) {
                        let html = '<div class="timeline">';
                        
                        data.activities.forEach(activity => {
                            html += `
                                <div class="border-start border-2 border-primary ps-3 mb-3">
                                    <div class="fw-bold">${activity.action}</div>
                                    ${activity.description ? '<div class="text-muted">' + activity.description + '</div>' : ''}
                                    <small class="text-muted">${new Date(activity.created_at).toLocaleString()}</small>
                                    ${activity.ip_address ? '<br><small class="text-muted">IP: ' + activity.ip_address + '</small>' : ''}
                                </div>
                            `;
                        });
                        
                        html += '</div>';
                        $('#activityContent').html(html);
                        $('#activityModal').modal('show');
                    }
                });
            });

            // Delete User
            $(document).on('click', '.delete-user', function() {
                let id = $(this).data('id');
                
                Swal.fire({
                    title: 'Delete User?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('users.destroy', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(res) {
                                success_noti(res.message);
                                $('#usersTable').DataTable().ajax.reload(null, false);
                                loadUserStats();
                            },
                            error: function(xhr) {
                                error_noti(xhr.responseJSON?.message ?? 'Failed to delete user');
                            }
                        });
                    }
                });
            });

            // Reset Modal
            function resetUserModal() {
                $('#userModal').removeData('id');
                $('#userModalLabel').text('Add User');
                $('#submitUserBtnText').text('Create User');
                
                $('#userProperty, #userName, #userEmail, #userPhone').val('');
                $('#userDesignation, #userDepartment, #userDateOfJoining').val('');
                $('#userPassword, #userPasswordConfirmation').val('');
                $('.user-role').prop('checked', false);
                
                // Show password fields for new user
                $('#passwordField, #confirmPasswordField').show();
            }

            $('#userModal').on('hidden.bs.modal', resetUserModal);
        </script>
    @endpush
</x-app-layout>