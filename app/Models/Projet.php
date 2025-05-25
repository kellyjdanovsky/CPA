<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $table = 'projets';

    protected $fillable = [
        'nom', 'description', 'date_debut', 'date_fin', 'budget', 'statut'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'budget' => 'decimal:2',
    ];

    public function decaissements()
    {
        return $this->hasMany(Decaissement::class);
    }
}
