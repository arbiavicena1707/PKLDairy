<?php

// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'present'
    ];

    protected $casts = [
        'date' => 'date',
        'present' => 'boolean'
    ];

    /**
     * Get the activities for the attendance.
     */
    public function activities()
    {
        return $this->hasMany(Activities::class);
    }

    /**
     * Scope for present attendance
     */
    public function scopePresent($query)
    {
        return $query->where('present', true);
    }

    /**
     * Scope for absent attendance
     */
    public function scopeAbsent($query)
    {
        return $query->where('present', false);
    }

    /**
     * Scope for specific date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d M Y');
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return $this->present ? 'Hadir' : 'Tidak Hadir';
    }
}
