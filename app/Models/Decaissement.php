<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Decaissement extends Model
{
    use SoftDeletes;

    protected $table = 'decaissements';
    
    protected $fillable = [
        'date_decaissement',
        'reference_op',
        'beneficiaire',
        'montant',
        'montant_lettres',
        'motif',
        'mode_paiement',
        'projet_rubrique',
        'piece_justificative_path',
        'piece_justificative_nom',
        'piece_justificative_valide',
        'statut', // 'EN_ATTENTE', 'APPROUVE', 'PAYE', 'ANNULE'
        'created_by',
        'approved_by',
        'paid_by',
        'year',
        'observations'
    ];

    protected $dates = ['date_decaissement', 'deleted_at'];

    protected $casts = [
        'montant' => 'decimal:2',
        'piece_justificative_valide' => 'boolean'
    ];

    /**
     * Relation avec le créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'approbateur
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relation avec le payeur
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopePeriod($query, $date_debut, $date_fin)
    {
        return $query->whereBetween('date_decaissement', [$date_debut, $date_fin]);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour filtrer par année
     */
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope pour filtrer par projet/rubrique
     */
    public function scopeProjet($query, $projet)
    {
        return $query->where('projet_rubrique', $projet);
    }

    /**
     * Générer une référence OP unique
     */
    public static function generateReferenceOP($year)
    {
        $count = self::where('year', $year)->count() + 1;
        return 'OP-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Convertir un nombre en lettres (français)
     */
    public static function nombreEnLettres($nombre)
    {
        // Utilisation de l'helper existant NumberToWords si disponible
        if (class_exists('\App\Helpers\NumberToWords')) {
            return \App\Helpers\NumberToWords::convert($nombre);
        }
        
        // Sinon, implémentation basique
        $unite = [
            '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
            'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'
        ];
        
        $dizaine = [
            '', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'
        ];
        
        if ($nombre == 0) return 'zéro';
        if ($nombre < 20) return $unite[$nombre];
        if ($nombre < 100) {
            $d = intval($nombre / 10);
            $u = $nombre % 10;
            if ($u == 0) return $dizaine[$d];
            return $dizaine[$d] . '-' . $unite[$u];
        }
        
        // Pour les nombres plus grands, une implémentation plus complexe serait nécessaire
        return 'nombre trop grand';
    }

    /**
     * Vérifier si la pièce justificative est présente
     */
    public function hasPieceJustificative()
    {
        return !empty($this->piece_justificative_path) && file_exists(storage_path('app/' . $this->piece_justificative_path));
    }

    /**
     * Obtenir l'URL de la pièce justificative
     */
    public function getPieceJustificativeUrlAttribute()
    {
        if ($this->hasPieceJustificative()) {
            return asset('storage/' . str_replace('public/', '', $this->piece_justificative_path));
        }
        return null;
    }

    /**
     * Calcul du total des décaissements par période
     */
    public static function getTotalByPeriod($date_debut, $date_fin, $statut = null)
    {
        $query = self::whereBetween('date_decaissement', [$date_debut, $date_fin]);
        
        if ($statut) {
            $query->where('statut', $statut);
        }
        
        return $query->sum('montant');
    }

    /**
     * Obtenir les statuts disponibles
     */
    public static function getStatuts()
    {
        return [
            'EN_ATTENTE' => 'En attente',
            'APPROUVE' => 'Approuvé',
            'PAYE' => 'Payé',
            'ANNULE' => 'Annulé'
        ];
    }
}