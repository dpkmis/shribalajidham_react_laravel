<?php
// ============================================================================
// FILE 1: app/Models/HousekeepingStaff.php
// ============================================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class HousekeepingTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id', 'room_id', 'assigned_to', 'assigned_by',
        'task_type', 'status', 'priority', 'scheduled_date', 'scheduled_time',
        'started_at', 'completed_at', 'inspected_at',
        'estimated_duration_minutes', 'actual_duration_minutes',
        'checklist_items', 'completed_items', 'special_instructions',
        'quality_rating', 'inspected_by', 'inspection_notes', 'rejection_reason',
        'is_occupied', 'guest_present', 'do_not_disturb', 'staff_notes'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'inspected_at' => 'datetime',
        'estimated_duration_minutes' => 'integer',
        'actual_duration_minutes' => 'integer',
        'quality_rating' => 'integer',
        'checklist_items' => 'array',
        'completed_items' => 'array',
        'is_occupied' => 'boolean',
        'guest_present' => 'boolean',
        'do_not_disturb' => 'boolean',
    ];

    protected $appends = ['status_label', 'priority_label'];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function staff()
    {
        return $this->belongsTo(HousekeepingStaff::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function suppliesUsed()
    {
        return $this->hasMany(HousekeepingSupplyUsage::class, 'task_id');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->status));
    }

    public function getPriorityLabelAttribute()
    {
        return ucfirst($this->priority);
    }

    public function getDurationDisplayAttribute()
    {
        if (!$this->actual_duration_minutes) {
            return $this->estimated_duration_minutes . ' min (est.)';
        }
        return $this->actual_duration_minutes . ' min';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in-progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', today())
            ->whereNotIn('status', ['completed', 'inspected', 'cancelled']);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time');
    }

    // Methods
    public function start()
    {
        $this->update([
            'status' => 'in-progress',
            'started_at' => now(),
        ]);

        // Update room housekeeping status
        $this->room->update(['housekeeping_status' => 'in-progress']);
    }

    public function complete()
    {
        $duration = null;
        if ($this->started_at) {
            $duration = $this->started_at->diffInMinutes(now());
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_duration_minutes' => $duration,
        ]);

        // Update room housekeeping status
        $this->room->update(['housekeeping_status' => 'clean']);
    }

    public function inspect($rating, $notes, $userId)
    {
        $this->update([
            'status' => 'inspected',
            'quality_rating' => $rating,
            'inspection_notes' => $notes,
            'inspected_by' => $userId,
            'inspected_at' => now(),
        ]);

        // Update room to inspected status
        $this->room->update(['housekeeping_status' => 'inspected']);
    }

    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Mark room as dirty again
        $this->room->update(['housekeeping_status' => 'dirty']);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public static function getTaskTypes()
    {
        return [
            'checkout-cleaning' => 'Checkout Cleaning',
            'daily-cleaning' => 'Daily Cleaning',
            'deep-cleaning' => 'Deep Cleaning',
            'turndown-service' => 'Turndown Service',
            'maintenance-cleaning' => 'Maintenance Cleaning',
            'inspection' => 'Inspection',
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'in-progress' => 'In Progress',
            'completed' => 'Completed',
            'inspected' => 'Inspected',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function getPriorityOptions()
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }
}
