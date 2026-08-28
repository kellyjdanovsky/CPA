<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SchoolEvent extends Model
{
    protected $fillable = [
        'title', 'description', 'event_type', 'start_date', 'end_date', 
        'all_day', 'class_id', 'created_by', 'year'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'all_day' => 'boolean',
    ];

    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeMonth($query, $month, $year)
    {
        return $query->whereYear('start_date', $year)
                     ->whereMonth('start_date', $month);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', Carbon::today())->orderBy('start_date');
    }

    public function getEventColorAttribute()
    {
        $colors = [
            'cours' => 'primary',
            'examen' => 'danger',
            'vacances' => 'success',
            'fete' => 'warning',
            'reunion' => 'info',
            'conseil' => 'purple',
            'pedagogique' => 'teal',
            'autre' => 'secondary'
        ];

        return $colors[$this->event_type] ?? 'secondary';
    }

    public function getDurationDays()
    {
        if (!$this->end_date) return 1;
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
