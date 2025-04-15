<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class decaissement extends Model
{
    use HasFactory;
    protected $table = 'decaissements';
    protected $fillable = ['motif', 'montant', 'annee','ref','piece'];
}
