<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'activity'
    ];

    /**
     * Get the attendance that owns the activity.
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Scope for non-empty activities
     */
    public function scopeNotEmpty($query)
    {
        return $query->whereNotNull('activity')->where('activity', '!=', '');
    }
}
