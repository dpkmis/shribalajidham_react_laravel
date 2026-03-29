<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'property_id',
        'name',
        'slug',
        'description'
    ];

    protected $casts = [
        'property_id' => 'integer'
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }

    // Scopes
    public function scopeGlobal($query)
    {
        return $query->whereNull('property_id');
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where(function($q) use ($propertyId) {
            $q->whereNull('property_id')
              ->orWhere('property_id', $propertyId);
        });
    }

    // Helper Methods
    
    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Grant permission to role
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
        
        return $this;
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
        
        return $this;
    }

    /**
     * Sync permissions (replace all)
     */
    public function syncPermissions(array $permissions)
    {
        $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id')->toArray();
        
        if (empty($permissionIds)) {
            $permissionIds = Permission::whereIn('id', $permissions)->pluck('id')->toArray();
        }
        
        $this->permissions()->sync($permissionIds);
        
        return $this;
    }

    /**
     * Check if role is global
     */
    public function isGlobal(): bool
    {
        return $this->property_id === null;
    }

    /**
     * Get user count
     */
    public function getUserCount(): int
    {
        return $this->users()->count();
    }
}
