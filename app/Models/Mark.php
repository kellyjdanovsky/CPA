<?php

namespace App\Models;

use App\User;
use App\Traits\DuplicateDetection;
use Eloquent;

class Mark extends Eloquent
{
    use DuplicateDetection;
    protected $fillable = ['t1', 't2', 't3', 't4', 'tca', 'exm', 'tex1', 'tex2', 'tex3', 'sub_pos', 'cum', 'cum_ave', 'grade_id', 'comment', 'year', 'exam_id', 'subject_id', 'my_class_id', 'student_id', 'section_id', 'operation_uuid'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get fields to check for duplicates
     * 
     * @return array
     */
    protected function getDuplicateCheckFields()
    {
        return ['student_id', 'subject_id', 'exam_id', 'year'];
    }

    /**
     * Safely create or update mark record
     * 
     * @param array $data
     * @return static
     */
    public static function createOrUpdateSafely($data)
    {
        // Check if mark record already exists
        $existing = static::where('student_id', $data['student_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('exam_id', $data['exam_id'])
            ->where('year', $data['year'])
            ->first();

        if ($existing) {
            $existing->update($data);
            return $existing;
        }

        return static::safeCreate($data);
    }

    /**
     * Calculate total marks
     * 
     * @return int
     */
    public function getTotalMarks()
    {
        return ($this->t1 ?? 0) + ($this->t2 ?? 0) + ($this->t3 ?? 0) + 
               ($this->t4 ?? 0) + ($this->tca ?? 0) + ($this->exm ?? 0);
    }

    /**
     * Validate mark values
     * 
     * @param array $marks
     * @return bool
     */
    public static function validateMarks($marks)
    {
        $markFields = ['t1', 't2', 't3', 't4', 'tca', 'exm', 'tex1', 'tex2', 'tex3'];
        
        foreach ($markFields as $field) {
            if (isset($marks[$field])) {
                $value = $marks[$field];
                if (!is_numeric($value) || $value < 0 || $value > 100) {
                    return false;
                }
            }
        }
        
        return true;
    }
}
