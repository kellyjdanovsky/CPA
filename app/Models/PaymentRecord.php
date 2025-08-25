<?php

namespace App\Models;

use App\User;
use App\Traits\DuplicateDetection;
use Eloquent;

class PaymentRecord extends Eloquent
{
    use DuplicateDetection;
    protected $fillable =['student_id', 'methode','payment_id', 'amt_paid', 'year', 'paid', 'balance', 'ref_no', 'operation_uuid'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'pr_id');
    }

    /**
     * Get fields to check for duplicates
     * 
     * @return array
     */
    protected function getDuplicateCheckFields()
    {
        return ['student_id', 'payment_id', 'year'];
    }

    /**
     * Safely create payment record with duplicate prevention
     * 
     * @param array $data
     * @return static
     */
    public static function createSafePaymentRecord($data)
    {
        // Check if payment record already exists
        $existing = static::where('student_id', $data['student_id'])
            ->where('payment_id', $data['payment_id'])
            ->where('year', $data['year'])
            ->first();

        if ($existing) {
            // Update existing record instead of creating duplicate
            $existing->update([
                'amt_paid' => ($existing->amt_paid ?? 0) + ($data['amt_paid'] ?? 0),
                'balance' => $existing->balance - ($data['amt_paid'] ?? 0),
                'methode' => $data['methode'] ?? $existing->methode
            ]);
            return $existing;
        }

        return static::safeCreate($data);
    }

    /**
     * Get total amount paid through receipts
     * 
     * @return float
     */
    public function getTotalPaidFromReceipts()
    {
        return $this->receipt()->sum('amt_paid') ?? 0;
    }

    /**
     * Recalculate and update balance
     * 
     * @return void
     */
    public function recalculateBalance()
    {
        $totalPaid = $this->getTotalPaidFromReceipts();
        $originalAmount = $this->payment ? $this->payment->amount : 0;
        
        $this->update([
            'amt_paid' => $totalPaid,
            'balance' => max(0, $originalAmount - $totalPaid),
            'paid' => $totalPaid >= $originalAmount ? 1 : 0
        ]);
    }
}
