<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Models\Mark;
use App\Models\Payment;
use App\Models\PaymentRecord;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    protected $loc, $my_class, $student;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class, StudentRepo $student)
    {
        $this->loc = $loc;
        $this->my_class = $my_class;
        $this->student = $student;
    }

    public function get_lga($state_id)
    {
//        $state_id = Qs::decodeHash($state_id);
//        return ['id' => Qs::hash($q->id), 'name' => $q->name];

        $lgas = $this->loc->getLGAs($state_id);
        return $data = $lgas->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function get_class_sections($class_id)
    {
        $sections = $this->my_class->getClassSections($class_id);
        return $sections = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function get_class_subjects($class_id)
    {
        $sections = $this->my_class->getClassSections($class_id);
        $subjects = $this->my_class->findSubjectByClass($class_id);

        if(Qs::userIsTeacher()){
            $subjects = $this->my_class->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }

        $d['sections'] = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
        $d['subjects'] = $subjects->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();

        return $d;
    }

    public function get_available_years(Request $request)
    {
        // Si student_id est fourni, récupérer les examens disponibles pour cet étudiant
        if ($request->has('student_id') && $request->has('year')) {
            $student_id = Qs::decodeHash($request->student_id);
            $year = $request->year;
            
            // Récupérer les examens disponibles pour cet étudiant et cette année
            $exams = \App\Models\Exam::where('year', $year)
                ->whereExists(function ($query) use ($student_id, $year) {
                    $query->select(\DB::raw(1))
                        ->from('exam_records')
                        ->whereRaw('exam_records.exam_id = exams.id')
                        ->where('exam_records.student_id', $student_id)
                        ->where('exam_records.year', $year);
                })
                ->get();
            
            // Si aucun examen n'est disponible, récupérer tous les examens de l'année
            if ($exams->count() < 1) {
                $exams = \App\Models\Exam::where('year', $year)->get();
            }
            
            return response()->json(['exams' => $exams]);
        }
        
        // Sinon, traiter comme une demande d'années disponibles
        $class_id = $request->class_id;
        $section_id = $request->section_id;

        // Récupérer les élèves de cette classe/section
        $students = $this->student->getRecord(['my_class_id' => $class_id, 'section_id' => $section_id])->get();

        if ($students->count() < 1) {
            return response()->json(['years' => [Qs::getSetting('current_session')]]);
        }

        // Récupérer les IDs des élèves
        $student_ids = $students->pluck('user_id')->toArray();

        // Récupérer toutes les années scolaires disponibles pour ces élèves
        $years = Mark::whereIn('student_id', $student_ids)
                    ->select('year')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year')
                    ->toArray();

        // Si aucune année n'est disponible, utiliser l'année actuelle
        if (empty($years)) {
            $years = [Qs::getSetting('current_session')];
        }

        return response()->json(['years' => $years]);
    }

    public function update_student_field(Request $request)
    {
        // Vérifier si l'utilisateur a les droits d'accès (TeamSA ou TeamSAT)
        if (!Qs::userIsTeamSA() && !Qs::userIsTeamSAT()) {
            return response()->json(['ok' => false, 'msg' => 'Accès non autorisé'], 403);
        }

        // Valider les données reçues
        $request->validate([
            'student_id' => 'required',
            'field_name' => 'required|string',
            'field_value' => 'required',
        ]);

        $student_id = Qs::decodeHash($request->student_id);
        $field_name = $request->field_name;
        $field_value = $request->field_value;

        // Vérifier si l'étudiant existe
        $student = $this->student->getRecord(['id' => $student_id])->first();
        if (!$student) {
            return response()->json(['ok' => false, 'msg' => 'Étudiant non trouvé'], 404);
        }

        try {
            // Déterminer si le champ appartient à la table users ou student_records
            $user_fields = ['name', 'dob', 'status', 'nom_p', 'prof_p', 'nom_m', 'prof_m', 'phone', 'address'];
            $student_fields = ['age'];

            if (in_array($field_name, $user_fields)) {
                // Mettre à jour le champ dans la table users
                $data = [$field_name => $field_value];

                // Si c'est la date de naissance, calculer l'âge automatiquement
                if ($field_name == 'dob') {
                    $birthDate = new \DateTime($field_value);
                    $today = new \DateTime('today');
                    $age = $birthDate->diff($today)->y;

                    // Mettre à jour l'âge dans la table student_records
                    $this->student->updateRecord($student_id, ['age' => $age]);
                }

                // Mettre à jour le champ utilisateur
                $student->user()->update($data);

                // Si le champ est le statut, vérifier qu'il est valide
                if ($field_name == 'status' && !in_array($field_value, ['Normal', 'ADRA', 'TEAM3'])) {
                    return response()->json(['ok' => false, 'msg' => 'Statut invalide'], 400);
                }

                // Si le statut a été modifié, mettre à jour les enregistrements de paiement en conséquence
                if ($field_name == 'status') {
                    try {
                        \Log::info("Début de la mise à jour des paiements pour l'étudiant ID: {$student->user_id} avec le statut: {$field_value}");
                        $this->updatePaymentRecordsBasedOnStatus($student->user_id, $field_value);
                        \Log::info("Fin de la mise à jour des paiements pour l'étudiant ID: {$student->user_id}");
                    } catch (\Exception $e) {
                        \Log::error("Erreur lors de la mise à jour des paiements pour l'étudiant ID: {$student->user_id}: " . $e->getMessage());
                        \Log::error("Trace: " . $e->getTraceAsString());
                    }
                }

                return response()->json([
                    'ok' => true,
                    'msg' => 'Mise à jour réussie',
                    'age' => $field_name == 'dob' ? $age : null
                ]);
            }
            elseif (in_array($field_name, $student_fields)) {
                // Mettre à jour le champ dans la table student_records
                $this->student->updateRecord($student_id, [$field_name => $field_value]);
                return response()->json(['ok' => true, 'msg' => 'Mise à jour réussie']);
            }
            else {
                return response()->json(['ok' => false, 'msg' => 'Champ non autorisé'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'msg' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour les enregistrements de paiement en fonction du statut de l'étudiant
     *
     * @param int $student_id ID de l'étudiant
     * @param string $status Nouveau statut de l'étudiant (Normal, ADRA, TEAM3)
     * @return void
     */
    private function updatePaymentRecordsBasedOnStatus($student_id, $status)
    {
        try {
            // Récupérer tous les enregistrements de paiement de l'étudiant
            $paymentRecords = PaymentRecord::where('student_id', $student_id)->get();

            \Log::info("Mise à jour des paiements pour l'étudiant ID: {$student_id} avec le statut: {$status}");
            \Log::info("Nombre d'enregistrements de paiement trouvés: " . $paymentRecords->count());

            foreach ($paymentRecords as $record) {
                // Récupérer le paiement associé pour connaître le montant total
                $payment = Payment::find($record->payment_id);
                if (!$payment) {
                    \Log::warning("Paiement non trouvé pour l'enregistrement ID: {$record->id}");
                    continue;
                }

                // Récupérer le montant total des reçus pour cet enregistrement
                $totalReceiptAmount = 0;
                $receipts = $record->receipt;
                if ($receipts && $receipts->count() > 0) {
                    $totalReceiptAmount = $receipts->sum('amt_paid');
                }

                $totalAmount = $payment->amount;
                // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
                $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($record->amt_paid ?: 0);

                \Log::info("Enregistrement ID: {$record->id}, Paiement ID: {$record->payment_id}, Montant total: {$totalAmount}, Montant payé (reçus): {$totalReceiptAmount}, Montant payé (record): {$record->amt_paid}, Montant utilisé: {$paidAmount}");

                // S'assurer que le montant payé dans l'enregistrement est à jour
                if ($totalReceiptAmount > 0 && $record->amt_paid != $totalReceiptAmount) {
                    $record->amt_paid = $totalReceiptAmount;
                    \Log::info("Mise à jour du montant payé dans l'enregistrement: {$totalReceiptAmount}");
                }

                // Mettre à jour le champ balance dans la base de données
                if ($status === 'ADRA') {
                    // Pour les élèves ADRA, le montant dû est la différence pour atteindre 25% du montant total
                    $minimumRequired = $totalAmount * 0.25;

                    if ($paidAmount >= $minimumRequired) {
                        // Si l'élève a déjà payé au moins 25%, le montant dû est 0
                        $record->balance = 0;
                    } else {
                        // Sinon, le montant dû est ce qui reste à payer pour atteindre 25%
                        $record->balance = $minimumRequired - $paidAmount;
                    }
                    \Log::info("Mise à jour du solde pour l'élève ADRA: {$record->balance}");
                }

                // Appliquer les règles selon le statut
                switch ($status) {
                    case 'TEAM3':
                        // Les élèves TEAM3 sont considérés comme ayant payé
                        $record->paid = 1;
                        $record->balance = 0;
                        \Log::info("Statut TEAM3: Marqué comme payé");
                        break;

                    case 'ADRA':
                        // Les élèves ADRA doivent payer au moins 25% du montant
                        if ($totalAmount > 0) {
                            // Calculer le montant minimum requis (25% du montant total)
                            $minimumRequired = $totalAmount * 0.25;
                            $paidPercentage = ($paidAmount / $totalAmount) * 100;
                            \Log::info("Statut ADRA: Pourcentage payé: {$paidPercentage}%, Montant minimum requis: {$minimumRequired}");

                            if ($paidAmount >= $minimumRequired) {
                                // Si l'élève a payé au moins 25%, il est considéré comme ayant payé
                                $record->paid = 1;
                                $record->balance = 0; // Le solde est 0 car l'élève est considéré comme ayant payé
                                \Log::info("Statut ADRA: Montant payé >= 25%, marqué comme payé");
                            } else {
                                // Sinon, il doit encore payer la différence pour atteindre 25%
                                $record->paid = 0;
                                $record->balance = $minimumRequired - $paidAmount; // Le solde est ce qui reste à payer pour atteindre 25%
                                \Log::info("Statut ADRA: Montant payé < 25%, marqué comme non payé, balance (reste à payer pour atteindre 25%): {$record->balance}");
                            }
                        } else {
                            // Si le montant total est 0, considérer comme payé
                            $record->paid = 1;
                            $record->balance = 0;
                            \Log::info("Statut ADRA: Montant total est 0, marqué comme payé");
                        }
                        break;

                    case 'Normal':
                    default:
                        // Les élèves normaux doivent payer le montant total
                        if ($paidAmount >= $totalAmount) {
                            $record->paid = 1;
                            $record->balance = 0;
                            \Log::info("Statut Normal: Montant payé >= Montant total, marqué comme payé");
                        } else {
                            $record->paid = 0;
                            $record->balance = $totalAmount - $paidAmount;
                            \Log::info("Statut Normal: Montant payé < Montant total, marqué comme non payé, balance: {$record->balance}");
                        }
                        break;
                }

                // Sauvegarder les modifications
                $record->save();
                \Log::info("Enregistrement sauvegardé avec paid={$record->paid}, balance={$record->balance}");
            }
        } catch (\Exception $e) {
            // Enregistrer l'erreur dans les logs
            \Log::error('Erreur lors de la mise à jour des paiements: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
        }
    }
}
