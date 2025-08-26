<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Recette extends Model
{
    use SoftDeletes;

    protected $table = 'recettes';
    
    protected $fillable = [
        'date_recette',
        'student_id',
        'beneficiaire_nom', // Pour les recettes non liées à un étudiant
        'class_id',
        'payment_id',
        'payment_record_id',
        'encaissement_id', // Référence vers encaissement si applicable
        'montant_encaisse',
        'mode_paiement',
        'reference_recette',
        'type_recette', // 'NORMAL', 'ADRA', 'TEAM3', 'DIVERS'
        'description',
        'created_by',
        'year',
        'observations'
    ];

    protected $dates = ['date_recette', 'deleted_at'];

    protected $casts = [
        'montant_encaisse' => 'decimal:2'
    ];

    /**
     * Relation avec l'étudiant
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relation avec le paiement
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Relation avec l'enregistrement de paiement
     */
    public function paymentRecord()
    {
        return $this->belongsTo(PaymentRecord::class, 'payment_record_id');
    }

    /**
     * Relation avec l'encaissement
     */
    public function encaissement()
    {
        return $this->belongsTo(Encaissement::class, 'encaissement_id');
    }

    /**
     * Relation avec la classe
     */
    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'class_id');
    }

    /**
     * Relation avec le créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopePeriod($query, $date_debut, $date_fin)
    {
        return $query->whereBetween('date_recette', [$date_debut, $date_fin]);
    }

    /**
     * Scope pour filtrer par classe
     */
    public function scopeClass($query, $class_id)
    {
        return $query->where('class_id', $class_id);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type_recette', $type);
    }

    /**
     * Scope pour filtrer par année
     */
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Générer une référence de recette unique
     */
    public static function generateReference($type, $year)
    {
        $prefix = 'REC-' . strtoupper(substr($type, 0, 3));
        $count = self::where('type_recette', $type)
                    ->where('year', $year)
                    ->count() + 1;
        
        return $prefix . '-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir le nom complet du bénéficiaire
     */
    public function getBeneficiaireAttribute()
    {
        if ($this->student) {
            return $this->student->name;
        }
        return $this->beneficiaire_nom;
    }

    /**
     * Calcul du total des recettes par période
     */
    public static function getTotalByPeriod($date_debut, $date_fin, $type = null)
    {
        $query = self::whereBetween('date_recette', [$date_debut, $date_fin]);
        
        if ($type) {
            $query->where('type_recette', $type);
        }
        
        return $query->sum('montant_encaisse');
    }
}