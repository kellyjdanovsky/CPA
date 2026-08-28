<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class DisciplineRecord extends Model
{
    protected $fillable = [
        'student_id', 'type', 'category', 'description', 'date_incident', 
        'severity', 'action_taken', 'parent_notified', 'recorded_by', 'year'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeStudent($query, $student_id)
    {
        return $query->where('student_id', $student_id);
    }

    public function scopeClass($query, $class_id)
    {
        return $query->whereHas('student.studentRecord', function ($q) use ($class_id) {
            $q->where('my_class_id', $class_id);
        });
    }

    public static function getCategories()
    {
        return [
            'incident' => ['Retard répété', 'Absence injustifiée', 'Bagarre', 'Insolence', 'Dégradation de matériel', 'Triche', 'Vol', 'Autre'],
            'sanction' => ['Avertissement oral', 'Avertissement écrit', 'Convocation parents', 'Exclusion temporaire', 'Conseil de discipline'],
            'recompense' => ['Tableau d\'honneur', 'Félicitations du conseil', 'Encouragements', 'Prix d\'excellence', 'Autre']
        ];
    }

    public function getSeverityBadgeAttribute()
    {
        $badges = [
            'mineur' => '<span class="badge badge-info">Mineur</span>',
            'moyen' => '<span class="badge badge-warning">Moyen</span>',
            'grave' => '<span class="badge badge-danger">Grave</span>',
            'tres_grave' => '<span class="badge badge-dark">Très grave</span>',
        ];

        return $badges[$this->severity] ?? '';
    }
}
