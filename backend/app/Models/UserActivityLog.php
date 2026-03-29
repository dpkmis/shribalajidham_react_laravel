<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Helper Methods
    
    /**
     * Get human-readable action name
     */
    public function getActionNameAttribute(): string
    {
        $actions = [
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'viewed' => 'Viewed',
            'activated' => 'Activated',
            'deactivated' => 'Deactivated',
            'password_reset' => 'Password Reset',
            'account_locked' => 'Account Locked',
            'account_unlocked' => 'Account Unlocked',
            'unauthorized_access_attempt' => 'Unauthorized Access Attempt'
        ];

        return $actions[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Get activity icon
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'login' => 'bx-log-in',
            'logout' => 'bx-log-out',
            'created' => 'bx-plus-circle',
            'updated' => 'bx-edit',
            'deleted' => 'bx-trash',
            'viewed' => 'bx-show',
            'activated' => 'bx-check-circle',
            'deactivated' => 'bx-x-circle',
            'password_reset' => 'bx-key',
            'account_locked' => 'bx-lock',
            'account_unlocked' => 'bx-lock-open',
            'unauthorized_access_attempt' => 'bx-error'
        ];

        return $icons[$this->action] ?? 'bx-info-circle';
    }

    /**
     * Get activity color
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'created' => 'primary',
            'updated' => 'info',
            'deleted' => 'danger',
            'activated' => 'success',
            'deactivated' => 'warning',
            'password_reset' => 'warning',
            'account_locked' => 'danger',
            'account_unlocked' => 'success',
            'unauthorized_access_attempt' => 'danger'
        ];

        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Static method to log activity (alternative to User model method)
     */
    public static function log(
        int $userId,
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?array $changes = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $changes
        ]);
    }

    /**
     * Get activity summary for a user
     */
    public static function getSummary(int $userId, int $days = 30): array
    {
        $activities = self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_activities' => $activities->count(),
            'by_action' => $activities->groupBy('action')->map->count(),
            'by_module' => $activities->groupBy('module')->map->count(),
            'recent' => $activities->take(10)
        ];
    }
}

