<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Helpers\Qs;

class Decaissement extends Model
{
    protected $table = 'decaissements';

    protected $fillable = [
        'date_paiement', 'montant', 'motif', 'description', 'beneficiaire',
        'methode_paiement', 'reference', 'piece', 'details_bancaires',
        'status', 'created_by', 'year'
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute()
    {
        $status = $this->status;
        $badges = [
            'en_attente' => '<span class="badge badge-warning">En attente</span>',
            'approuve' => '<span class="badge badge-success">Approuvé</span>',
            'rejete' => '<span class="badge badge-danger">Rejeté</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">Inconnu</span>';
    }

    public function getFormattedMontantAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' Ar';
    }

    public function getFormattedDateAttribute()
    {
        return $this->date_paiement->format('d/m/Y');
    }

    public static function getCategories()
    {
        return self::select('motif')
            ->distinct()
            ->orderBy('motif')
            ->pluck('motif')
            ->toArray();
    }

    public static function getMethodesPaiement()
    {
        return [
            'espèces' => 'Espèces',
            'chèque' => 'Chèque',
            'virement' => 'Virement bancaire',
            'mobile_money' => 'Mobile Money',
            'carte' => 'Carte bancaire',
            'autre' => 'Autre'
        ];
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function($model) {
            if (!$model->year) {
                $model->year = Qs::getCurrentSession();
            }

            if (!$model->created_by) {
                $model->created_by = auth()->id();
            }
        });
    }
}
