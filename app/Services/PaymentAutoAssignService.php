<?php
namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentRecord;
use App\Helpers\Qs;

class PaymentAutoAssignService
{
    public function assignClassPaymentsToStudent($student_id, $class_id, $session, $student_type = 'Nouveau', $status = 'Normal')
    {
        // Récupère les frais associés à la classe et à l'année
        $payments = Payment::where('my_class_id', $class_id)->where('year', $session)->get();
        
        foreach ($payments as $p) {
            // Logique de distinction Nouveau/Ancien
            if ($student_type == 'Ancien' && stripos($p->title, 'Inscription') !== false && stripos($p->title, 'Réinscription') === false) {
                continue; // Ignore Inscription pour les anciens
            }
            
            $pr = PaymentRecord::firstOrCreate([
                'student_id' => $student_id,
                'payment_id' => $p->id,
                'year' => $session
            ], [
                'amt_paid' => 0,
                'balance' => $p->amount,
                'paid' => 0,
                'ref_no' => Qs::getSystemRef()
            ]);
            
            // Adaptation selon le régime (Normal, ADRA, TEAM3)
            if ($status == 'ADRA') {
                $pr->balance = $p->amount * 0.25; // 25% parent
                $pr->save();
            } elseif ($status == 'TEAM3') {
                $pr->balance = 0; // 0% parent
                $pr->paid = 1; // Considéré payé
                $pr->amt_paid = $p->amount;
                $pr->save();
            }
        }
    }
}