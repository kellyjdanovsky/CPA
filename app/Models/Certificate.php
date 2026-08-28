<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Helpers\Qs;

class Certificate extends Model
{
    protected $fillable = [
        'student_id', 'type', 'reference_no', 'date_issued', 'academic_year', 'details', 'generated_by'
    ];

    protected $casts = [
        'details' => 'array',
        'date_issued' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public static function generateReferenceNo($type, $year)
    {
        $prefix = 'CERT-';
        switch ($type) {
            case 'scolarite':
                $prefix .= 'SCOL-';
                break;
            case 'frequentation':
                $prefix .= 'FREQ-';
                break;
            case 'reussite':
                $prefix .= 'REUS-';
                break;
            case 'fin_etudes':
                $prefix .= 'FIN-';
                break;
            case 'paiement':
                $prefix .= 'PAIE-';
                break;
            case 'transfert':
                $prefix .= 'TRAN-';
                break;
            default:
                $prefix .= 'GEN-';
                break;
        }

        $prefix .= $year . '-';

        $lastCert = self::where('reference_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        if (!$lastCert) {
            $number = 1;
        } else {
            $lastNumber = intval(substr($lastCert->reference_no, strrpos($lastCert->reference_no, '-') + 1));
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
