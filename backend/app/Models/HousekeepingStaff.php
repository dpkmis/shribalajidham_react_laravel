<?php
// ============================================================================
// FILE 1: app/Models/HousekeepingStaff.php
// ============================================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousekeepingStaff extends Model
{
    use SoftDeletes;

    protected $table = 'housekeeping_staff';

    protected $fillable = [
        'user_id', 'property_id', 'staff_code', 'full_name', 'email', 'phone',
        'employment_type', 'shift', 'joining_date', 'leaving_date',
        'max_rooms_per_day', 'specializations', 'is_supervisor', 'is_active', 'notes'
    ];

    protected $casts = [
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'max_rooms_per_day' => 'integer',
        'specializations' => 'array',
        'is_supervisor' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'assigned_to');
    }

    public function todayTasks()
    {
        return $this->tasks()
            ->whereDate('scheduled_date', today())
            ->whereIn('status', ['pending', 'assigned', 'in-progress']);
    }

    public function attendance()
    {
        return $this->hasMany(HousekeepingAttendance::class, 'staff_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSupervisors($query)
    {
        return $query->where('is_supervisor', true);
    }

    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('full_name');
    }

    // Methods
    public function getTodayWorkload()
    {
        return $this->todayTasks()->count();
    }

    public function canTakeMoreRooms()
    {
        return $this->getTodayWorkload() < $this->max_rooms_per_day;
    }

    public static function getEmploymentTypes()
    {
        return [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
        ];
    }

    public static function getShifts()
    {
        return [
            'morning' => 'Morning (6 AM - 2 PM)',
            'afternoon' => 'Afternoon (2 PM - 10 PM)',
            'evening' => 'Evening (10 PM - 6 AM)',
            'night' => 'Night (10 PM - 6 AM)',
            'rotating' => 'Rotating',
        ];
    }
}
