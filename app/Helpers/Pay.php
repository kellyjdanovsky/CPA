<?php

namespace App\Helpers;

use App\Models\PaymentRecord;

class Pay
{
    public static function getYears($st_id)
    {
        return PaymentRecord::where(['student_id' => $st_id])->pluck('year')->unique();
    }

    public static function genRefCode()
    {
        // Utiliser microtime pour une meilleure unicité
        $timestamp = str_replace('.', '', microtime(true));
        $random = mt_rand(1000, 9999);
        return date('Y').'/'.substr($timestamp, -6).$random;
    }
}