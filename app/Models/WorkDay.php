<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDay extends Model
{
    protected $fillable = ['user_id', 'date', 'start_time', 'end_time', 'break_duration'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function getTotalHoursAttribute()
    {
        if ($this->timeEntries->count() > 0) {
            return $this->timeEntries->sum('hours');
        }

        $startTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);
        $durationInMinutes = $endTime->diffInMinutes($startTime) - $this->break_duration;

        return max(0, $durationInMinutes / 60);
    }
}
