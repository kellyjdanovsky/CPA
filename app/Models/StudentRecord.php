<?php

namespace App\Models;

use App\User;
use App\Traits\DuplicateDetection;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRecord extends Eloquent
{
    use HasFactory, DuplicateDetection;

    protected $fillable = [
        'session', 'user_id', 'my_class_id', 'section_id', 'my_parent_id', 'dorm_id', 'dorm_room_no', 'adm_no', 'year_admitted', 'wd', 'wd_date', 'grad', 'grad_date', 'house', 'age', 'operation_uuid'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function my_parent()
    {
        return $this->belongsTo(User::class);
    }

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function dorm()
    {
        return $this->belongsTo(Dorm::class);
    }

    /**
     * Get fields to check for duplicates
     * 
     * @return array
     */
    protected function getDuplicateCheckFields()
    {
        return ['user_id', 'session', 'my_class_id'];
    }

    /**
     * Safely create or update student record
     * 
     * @param array $data
     * @return static
     */
    public static function createOrUpdateSafely($data)
    {
        // Check if student record already exists
        $existing = static::where('user_id', $data['user_id'])
            ->where('session', $data['session'])
            ->first();

        if ($existing) {
            $existing->update($data);
            return $existing;
        }

        return static::safeCreate($data);
    }
}
