<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'module',
        'description'
    ];

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user')
            ->withPivot('is_granted')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('module')->orderBy('name');
    }

    // Helper Methods

    /**
     * Get permissions grouped by module
     */
    public static function groupedByModule()
    {
        return self::orderBy('module')->orderBy('name')->get()->groupBy('module');
    }

    /**
     * Get all module names
     */
    public static function getModules(): array
    {
        return self::distinct()->pluck('module')->filter()->sort()->values()->toArray();
    }

    /**
     * Create permissions from config
     */
    public static function seedFromConfig()
    {
        $permissions = [];
        
        foreach (config('hms_permissions') as $module => $slugs) {
            foreach ($slugs as $slug) {
                $name = ucfirst(str_replace(['.', '_'], ' ', $slug));
                $permissions[] = [
                    'name' => $name,
                    'slug' => $slug,
                    'module' => $module,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        self::insertOrIgnore($permissions);
        
        return count($permissions);
    }
}
