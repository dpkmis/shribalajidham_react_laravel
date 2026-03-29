<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousekeepingAttendance extends Model
{
    protected $table = 'housekeeping_attendance';

    protected $fillable = [
        'staff_id', 'property_id', 'attendance_date', 'status',
        'check_in', 'check_out', 'rooms_cleaned', 'tasks_completed', 'notes'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'rooms_cleaned' => 'integer',
        'tasks_completed' => 'integer',
    ];

    public function staff()
    {
        return $this->belongsTo(HousekeepingStaff::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public static function getStatusOptions()
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'half-day' => 'Half Day',
            'leave' => 'Leave',
            'sick' => 'Sick Leave',
        ];
    }
}