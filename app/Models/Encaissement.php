<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Encaissement extends Model
{
    use SoftDeletes;

    protected $table = 'encaissements';
    
    protected $fillable = [
        'student_id',
        'payment_id',
        'payment_record_id',
        'class_id',
        'type_encaissement', // 'ADRA' ou 'TEAM3'
        'montant_original',
        'pourcentage_pris_en_charge',
        'montant_encaisse',
        'date_encaissement',
        'reference_encaissement',
        'created_by',
        'year',
        'observations'
    ];

    protected $dates = ['date_encaissement', 'deleted_at'];

    protected $casts = [
        'montant_original' => 'decimal:2',
        'pourcentage_pris_en_charge' => 'decimal:2',
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
     * Scope pour filtrer par année
     */
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope pour filtrer par type d'encaissement
     */
    public function scopeType($query, $type)
    {
        return $query->where('type_encaissement', $type);
    }

    /**
     * Scope pour filtrer par classe
     */
    public function scopeClass($query, $class_id)
    {
        return $query->where('class_id', $class_id);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopePeriod($query, $date_debut, $date_fin)
    {
        return $query->whereBetween('date_encaissement', [$date_debut, $date_fin]);
    }

    /**
     * Calculer le montant à encaisser en fonction du type
     */
    public static function calculateMontantEncaisse($montant_original, $type)
    {
        switch ($type) {
            case 'ADRA':
                return $montant_original * 0.75; // 75%
            case 'TEAM3':
                return $montant_original * 1.00; // 100%
            default:
                return 0;
        }
    }

    /**
     * Générer une référence d'encaissement unique
     */
    public static function generateReference($type, $year)
    {
        $prefix = $type === 'ADRA' ? 'ENC-ADR' : 'ENC-T3';
        $count = self::where('type_encaissement', $type)
                    ->where('year', $year)
                    ->count() + 1;
        
        return $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}