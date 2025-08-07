<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Session extends Eloquent
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'is_active', 'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir la session active
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Définir une session comme active (désactive les autres)
     */
    public function setAsActive()
    {
        // Désactiver toutes les autres sessions
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activer cette session
        $this->update(['is_active' => true]);
        
        return $this;
    }

    /**
     * Obtenir la session précédente
     */
    public function getPreviousSession()
    {
        $currentYear = (int) explode('-', $this->name)[0];
        $previousSessionName = ($currentYear - 1) . '-' . $currentYear;
        
        return static::where('name', $previousSessionName)->first();
    }

    /**
     * Obtenir la session suivante
     */
    public function getNextSession()
    {
        $currentYear = (int) explode('-', $this->name)[1];
        $nextSessionName = $currentYear . '-' . ($currentYear + 1);
        
        return static::where('name', $nextSessionName)->first();
    }

    /**
     * Vérifier si la session est en cours
     */
    public function isCurrent()
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Obtenir toutes les sessions triées par nom (année)
     */
    public static function getAllSorted()
    {
        return static::orderBy('name', 'desc')->get();
    }

    /**
     * Relations avec les autres modèles
     */
    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class, 'session', 'name');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'year', 'name');
    }

    public function paymentRecords()
    {
        return $this->hasMany(PaymentRecord::class, 'year', 'name');
    }

    public function examRecords()
    {
        return $this->hasMany(ExamRecord::class, 'year', 'name');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'year', 'name');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'year', 'name');
    }
}
