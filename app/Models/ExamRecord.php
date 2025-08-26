<?php

namespace App\Models;

use App\User;
use App\Models\Exam;
use App\Models\MyClass;
use App\Models\Section;
use Eloquent;

class ExamRecord extends Eloquent
{
    protected $fillable = ['exam_id', 'my_class_id', 'student_id', 'section_id', 'af', 'af_id', 'ps', 'ps_id','t_comment', 'p_comment', 'year', 'total', 'ave', 'class_ave', 'pos', 'decision', 'next_class_id', 'observations'];
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    
    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }
    
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}