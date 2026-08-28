<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'my_class_id', 'section_id', 'date', 'status', 
        'period', 'subject_id', 'marked_by', 'observations', 'year'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'my_class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Scopes
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeClass($query, $class_id)
    {
        return $query->where('my_class_id', $class_id);
    }

    public function scopeDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopePeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }
}
