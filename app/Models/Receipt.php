<?php

namespace App\Models;

use App\User;
use Eloquent;

class Receipt extends Eloquent
{
    protected $fillable = ['pr_id', 'year', 'balance', 'amt_paid', 'methode', 'created_by', 'payment_method', 'reference_number', 'observations', 'amount', 'description'];

    public function pr()
    {
        return $this->belongsTo(PaymentRecord::class, 'pr_id');
    }

}
