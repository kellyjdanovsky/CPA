<?php

namespace App\Models;

use App\User;
use Eloquent;

class StaffRecord extends Eloquent
{
    protected $fillable = ['code', 'emp_date', 'user_id', 'poste', 'departement', 'qualification', 'diplome', 'specialite', 'type_contrat', 'salaire', 'date_fin_contrat', 'heures_semaine', 'observations'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
