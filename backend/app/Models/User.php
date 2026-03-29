<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'is_active',
        'email_verified',
        'email_verified_at',
        'designation',
        'department',
        'date_of_joining',
        'address',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'date_of_joining' => 'date',
        'is_active' => 'boolean',
        'email_verified' => 'boolean'
    ];

    protected $appends = [
        'full_name',
        'is_locked',
        'status_label'
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('is_granted')
            ->withTimestamps();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getIsLockedAttribute(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        if ($this->is_locked) {
            return 'Locked';
        }
        return 'Active';
    }

    // ============================================
    // MUTATORS
    // ============================================

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeWithRole($query, $roleSlug)
    {
        return $query->whereHas('roles', function($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });
    }

    public function scopeWithPermission($query, $permissionSlug)
    {
        return $query->whereHas('roles.permissions', function($q) use ($permissionSlug) {
            $q->where('slug', $permissionSlug);
        });
    }

    // ============================================
    // ROLE METHODS
    // ============================================

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleSlug): bool
    {        
        if (is_array($roleSlug)) {
            return $this->roles()->whereIn('slug', $roleSlug)->exists();
        }
        
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    /**
     * Assign role(s) to user
     */
    public function assignRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        $roleIds = Role::whereIn('slug', $roles)->pluck('id')->toArray();
        
        if (empty($roleIds)) {
            $roleIds = Role::whereIn('id', $roles)->pluck('id')->toArray();
        }
        
        $this->roles()->syncWithoutDetaching($roleIds);
        
        return $this;
    }

    /**
     * Remove role(s) from user
     */
    public function removeRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        $roleIds = Role::whereIn('slug', $roles)->pluck('id')->toArray();
        
        if (empty($roleIds)) {
            $roleIds = Role::whereIn('id', $roles)->pluck('id')->toArray();
        }
        
        $this->roles()->detach($roleIds);
        
        return $this;
    }

    /**
     * Sync user roles (replace all existing)
     */
    public function syncRoles($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        $roleIds = Role::whereIn('slug', $roles)->pluck('id')->toArray();
        
        if (empty($roleIds)) {
            $roleIds = Role::whereIn('id', $roles)->pluck('id')->toArray();
        }
        
        $this->roles()->sync($roleIds);
        
        return $this;
    }

    // ============================================
    // PERMISSION METHODS
    // ============================================

    /**
     * Check if user has a specific permission
     * Checks both role permissions and direct user permissions
     */
    public function hasPermission($permissionSlug): bool
    {
        // dd($permissionSlug);

        if (is_array($permissionSlug)) {
            
            return $this->hasAnyPermission($permissionSlug);
        }
        
        // Check direct user permissions first (they override role permissions)
        $directPermission = $this->permissions()
        ->where('slug', $permissionSlug)
        ->first();
        
        // dd($directPermission, 'here');
        if ($directPermission) {
            return $directPermission->pivot->is_granted;
        }

        // Check role permissions
        return $this->roles()
            ->whereHas('permissions', function($q) use ($permissionSlug) {
                $q->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        // dd($permissions);
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Grant direct permission to user
     */
    public function grantPermission($permissionSlug)
    {
        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if (!$permission) {
            throw new \Exception("Permission '{$permissionSlug}' not found");
        }

        $this->permissions()->syncWithoutDetaching([
            $permission->id => ['is_granted' => true]
        ]);

        return $this;
    }

    /**
     * Revoke direct permission from user
     */
    public function revokePermission($permissionSlug)
    {
        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if (!$permission) {
            return $this;
        }

        $this->permissions()->syncWithoutDetaching([
            $permission->id => ['is_granted' => false]
        ]);

        return $this;
    }

    /**
     * Get all permissions (from roles + direct)
     */
    public function getAllPermissions(): array
    {
        // Get permissions from roles
        $rolePermissions = Permission::whereHas('roles', function($q) {
            $q->whereIn('roles.id', $this->roles->pluck('id'));
        })->pluck('slug')->toArray();

        // Get direct permissions
        $directPermissions = $this->permissions()
            ->wherePivot('is_granted', true)
            ->pluck('slug')
            ->toArray();

        // Get revoked permissions
        $revokedPermissions = $this->permissions()
            ->wherePivot('is_granted', false)
            ->pluck('slug')
            ->toArray();

        // Merge and remove revoked
        $allPermissions = array_unique(array_merge($rolePermissions, $directPermissions));
        $allPermissions = array_diff($allPermissions, $revokedPermissions);

        return array_values($allPermissions);
    }

    // ============================================
    // SPECIAL CHECKS
    // ============================================

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {        
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is property admin
     */
    public function isPropertyAdmin(): bool
    {
        return $this->hasRole('property-admin');
    }

    /**
     * Check if user can access module
     */
    public function canAccessModule($module): bool
    {        
        
        if ($this->isSuperAdmin()) {
            return true;
        }

        $modulePermissions = config(key: "hms_permissions.{$module}", default: []);
        
        if (empty($modulePermissions)) {
            return false;
        }

        return $this->hasAnyPermission($modulePermissions);
    }

    // ============================================
    // ACCOUNT SECURITY
    // ============================================

    /**
     * Lock user account
     */
    public function lockAccount(int $minutes = 30)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes)
        ]);

        $this->logActivity('account_locked', null, "Account locked for {$minutes} minutes");
    }

    /**
     * Unlock user account
     */
    public function unlockAccount()
    {
        $this->update([
            'locked_until' => null,
            'login_attempts' => 0
        ]);

        $this->logActivity('account_unlocked');
    }

    /**
     * Increment login attempts
     */
    public function incrementLoginAttempts()
    {
        $attempts = $this->login_attempts + 1;
        $this->update(['login_attempts' => $attempts]);

        // Lock after 5 failed attempts
        if ($attempts >= 5) {
            $this->lockAccount(30);
        }

        return $attempts;
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts()
    {
        $this->update(['login_attempts' => 0]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(?string $ipAddress = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
            'login_attempts' => 0
        ]);

        $this->logActivity('login', null, 'User logged in');
    }

    // ============================================
    // ACTIVITY LOGGING
    // ============================================

    /**
     * Log user activity
     */
    public function logActivity(
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?array $changes = null
    ): UserActivityLog {
        return $this->activityLogs()->create([
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $changes
        ]);
    }

    // ============================================
    // STATUS MANAGEMENT
    // ============================================

    /**
     * Activate user
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
        $this->logActivity('activated', 'users', 'User account activated');
        return $this;
    }

    /**
     * Deactivate user
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
        $this->logActivity('deactivated', 'users', 'User account deactivated');
        
        // End all active sessions
        $this->sessions()->where('is_active', true)->update(['is_active' => false]);
        
        return $this;
    }

    /**
     * Check if user can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Prevent deletion if user created bookings, invoices, etc.
        // Add your business logic here
        
        return true;
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get user's active sessions
     */
    public function getActiveSessions()
    {
        return $this->sessions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->get();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(int $limit = 10)
    {
        return $this->activityLogs()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user stats
     */
    public function getStats(): array
    {
        return [
            'total_logins' => $this->activityLogs()->where('action', 'login')->count(),
            'last_login' => $this->last_login_at?->diffForHumans(),
            'active_sessions' => $this->getActiveSessions()->count(),
            'role_count' => $this->roles()->count(),
            'permission_count' => count($this->getAllPermissions())
        ];
    }
}