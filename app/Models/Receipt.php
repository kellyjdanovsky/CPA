<?php

namespace App\Models;

use App\User;
use App\Traits\DuplicateDetection;
use Eloquent;
use Illuminate\Support\Str;

class Receipt extends Eloquent
{
    use DuplicateDetection;
    protected $fillable = ['pr_id', 'year', 'balance', 'amt_paid', 'methode', 'created_by', 'payment_method', 'reference_number', 'observations', 'amount', 'description', 'operation_uuid'];

    public function pr()
    {
        return $this->belongsTo(PaymentRecord::class, 'pr_id');
    }

    /**
     * Get fields to check for duplicates
     * 
     * @return array
     */
    protected function getDuplicateCheckFields()
    {
        return ['pr_id', 'reference_number'];
    }

    /**
     * Generate unique reference number
     * 
     * @return string
     */
    public static function generateReferenceNumber()
    {
        $prefix = 'REC';
        $timestamp = now()->format('YmdHis');
        $random = Str::upper(Str::random(4));
        
        $referenceNumber = "{$prefix}-{$timestamp}-{$random}";
        
        // Ensure uniqueness
        while (static::where('reference_number', $referenceNumber)->exists()) {
            $random = Str::upper(Str::random(4));
            $referenceNumber = "{$prefix}-{$timestamp}-{$random}";
        }
        
        return $referenceNumber;
    }

    /**
     * Safely create receipt with duplicate prevention
     * 
     * @param array $data
     * @return static
     */
    public static function createSafeReceipt($data)
    {
        // Generate reference number if not provided
        if (empty($data['reference_number'])) {
            $data['reference_number'] = static::generateReferenceNumber();
        }

        // Check for duplicate by reference number
        if (!empty($data['reference_number'])) {
            $existing = static::where('reference_number', $data['reference_number'])->first();
            if ($existing) {
                throw new \Exception("Receipt with reference number {$data['reference_number']} already exists.");
            }
        }

        return static::safeCreate($data);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatically generate reference number if not provided
        static::creating(function ($receipt) {
            if (empty($receipt->reference_number)) {
                $receipt->reference_number = static::generateReferenceNumber();
            }
        });
        
        // Update payment record balance after creating receipt
        static::created(function ($receipt) {
            if ($receipt->pr) {
                $receipt->pr->recalculateBalance();
            }
        });
        
        // Update payment record balance after updating receipt
        static::updated(function ($receipt) {
            if ($receipt->pr) {
                $receipt->pr->recalculateBalance();
            }
        });
        
        // Update payment record balance after deleting receipt
        static::deleted(function ($receipt) {
            if ($receipt->pr) {
                $receipt->pr->recalculateBalance();
            }
        });
    }
}
