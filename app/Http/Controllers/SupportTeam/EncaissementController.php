<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Repositories\EncaissementRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\PaymentRepo;
use App\Repositories\StudentRepo;
use App\Models\Encaissement;
use App\Models\Payment;
use App\Models\User;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class EncaissementController extends Controller
{
    protected $encaissement, $my_class, $payment, $student, $year;

    public function __construct(EncaissementRepo $encaissement, MyClassRepo $my_class, PaymentRepo $payment, StudentRepo $student)
    {
        $this->encaissement = $encaissement;
        $this->my_class = $my_class;
        $this->payment = $payment;
        $this->student = $student;
        $this->year = Qs::getCurrentSession();

        $this->middleware('teamAccount');
    }

    /**
     * Afficher la page principale des encaissements
     */
    public function index()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['payments'] = $this->payment->getPayment(['year' => $this->year])->get();
        $d['encaissements'] = $this->encaissement->getByYear($this->year);
        $d['statistics'] = $this->encaissement->getStatistics($this->year);
        $d['year'] = $this->year;
        $d['selected_year'] = $this->year;

        return view('pages.support_team.payments.encaissements.index', $d);
    }

    /**
     * Afficher le formulaire de sélection pour les encaissements
     */
    public function create()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['payments'] = $this->payment->getPayment(['year' => $this->year])->get();
        $d['year'] = $this->year;
        $d['selected_year'] = $this->year;

        return view('pages.support_team.payments.encaissements.create', $d);
    }

    /**
     * Obtenir les paiements d'une classe via Ajax
     */
    public function getClassPayments(Request $request)
    {
        try {
            $class_id = $request->class_id;
            
            // Récupérer les paiements spécifiques à la classe
            $payments = $this->payment->getPaymentWithYear([
                'my_class_id' => $class_id
            ], $this->year)->get();

            // Inclure aussi les paiements généraux (non spécifiques à une classe)
            $general_payments = $this->payment->getPaymentWithYear([
                'my_class_id' => null
            ], $this->year)->get();

            $all_payments = $payments->merge($general_payments);

            if ($all_payments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun paiement trouvé pour cette classe.',
                    'payments' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'payments' => $all_payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'title' => $payment->title,
                        'amount' => $payment->amount,
                        'description' => $payment->description
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des paiements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des paiements. Veuillez réessayer.',
                'payments' => []
            ], 500);
        }
    }

    /**
     * Obtenir les étudiants éligibles pour l'encaissement
     */
    public function getEligibleStudents(Request $request)
    {
        try {
            $class_id = $request->class_id;
            $payment_id = $request->payment_id;
            $type = $request->type; // 'ADRA' ou 'TEAM3'

            if (!$class_id || !$payment_id || !$type) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paramètres manquants',
                    'students' => []
                ], 400);
            }

            $students = $this->encaissement->getEligibleStudents($class_id, $payment_id, $type);

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun étudiant éligible trouvé',
                    'students' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'students' => $students->map(function ($student) use ($payment_id) {
                    $payment_record = $student->payment_records->where('payment_id', $payment_id)->first();
                    $payment = $payment_record->payment ?? null;
                     
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'adm_no' => $student->student_records->first()->adm_no ?? '',
                        'payment_record_id' => $payment_record->id,
                        'montant_original' => $payment ? $payment->amount : 0,
                        'balance' => $payment_record->balance ?? 0,
                        'amt_paid' => $payment_record->amt_paid ?? 0,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des étudiants éligibles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des étudiants éligibles',
                'students' => []
            ], 500);
        }
    }

    /**
     * Traiter l'encaissement
     */
    public function processEncaissement(Request $request)
    {
        $this->validate($request, [
            'class_id' => 'required|exists:my_classes,id',
            'payment_id' => 'required|exists:payments,id',
            'type_encaissement' => 'required|in:ADRA,TEAM3',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:users,id',
            'students.*.payment_record_id' => 'required|exists:payment_records,id',
            'students.*.selected' => 'required|boolean'
        ], [], [
            'class_id' => 'Classe',
            'payment_id' => 'Paiement',
            'type_encaissement' => 'Type d\'encaissement',
            'students' => 'Étudiants'
        ]);

        try {
            DB::beginTransaction();

            // Filtrer seulement les étudiants sélectionnés
            $selected_students = collect($request->students)->filter(function ($student) {
                return $student['selected'] == true;
            })->values()->toArray();

            if (empty($selected_students)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun étudiant sélectionné pour l\'encaissement.'
                ], 422);
            }

            // Traiter l'encaissement en lot
            $encaissements = $this->encaissement->processBatchEncaissement(
                $request->class_id,
                $request->payment_id,
                $request->type_encaissement,
                $selected_students
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Encaissement traité avec succès pour ' . count($encaissements) . ' étudiant(s).',
                'encaissements' => $encaissements->map(function ($enc) {
                    return [
                        'id' => $enc->id,
                        'reference' => $enc->reference_encaissement,
                        'student_name' => $enc->student->name,
                        'montant' => $enc->montant_encaisse
                    ];
                })
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement de l\'encaissement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les détails d'un encaissement
     */
    public function show($id)
    {
        $encaissement = $this->encaissement->find($id);
        
        if (!$encaissement) {
            return back()->with('flash_danger', 'Encaissement introuvable.');
        }

        $d['encaissement'] = $encaissement;
        
        return view('pages.support_team.payments.encaissements.show', $d);
    }

    /**
     * Modifier un encaissement
     */
    public function edit($id)
    {
        $encaissement = $this->encaissement->find($id);
        
        if (!$encaissement) {
            return back()->with('flash_danger', 'Encaissement introuvable.');
        }

        $d['encaissement'] = $encaissement;
        $d['my_classes'] = $this->my_class->all();
        $d['payments'] = $this->payment->getPayment(['year' => $this->year])->get();
        
        return view('pages.support_team.payments.encaissements.edit', $d);
    }

    /**
     * Mettre à jour un encaissement
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date_encaissement' => 'required|date',
            'observations' => 'nullable|string|max:1000'
        ], [], [
            'date_encaissement' => 'Date d\'encaissement',
            'observations' => 'Observations'
        ]);

        try {
            $encaissement = $this->encaissement->update($id, $request->only([
                'date_encaissement',
                'observations'
            ]));

            if ($encaissement) {
                return back()->with('flash_success', 'Encaissement mis à jour avec succès.');
            } else {
                return back()->with('flash_danger', 'Erreur lors de la mise à jour de l\'encaissement.');
            }

        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un encaissement
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->encaissement->delete($id);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Encaissement supprimé avec succès.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de l\'encaissement.'
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporter les encaissements en Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = [
                'type_encaissement' => $request->type_encaissement,
                'class_id' => $request->class_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'year' => $request->year ?: $this->year
            ];

            // Obtenir les données filtrées
            if ($filters['date_debut'] && $filters['date_fin']) {
                $encaissements = $this->encaissement->getByPeriod($filters['date_debut'], $filters['date_fin'], $filters);
            } else {
                $encaissements = $this->encaissement->getByYear($filters['year']);
            }

            // Créer l'export Excel
            $filename = 'encaissements_' . ($filters['type_encaissement'] ?: 'tous') . '_' . date('Y-m-d') . '.xlsx';
            
            return response()->streamDownload(function () use ($encaissements) {
                $handle = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($handle, [
                    'Date',
                    'Référence',
                    'Étudiant',
                    'Classe',
                    'Paiement',
                    'Type',
                    'Montant Original',
                    'Pourcentage',
                    'Montant Encaissé',
                    'Créé par',
                    'Observations'
                ]);

                // Données
                foreach ($encaissements as $enc) {
                    fputcsv($handle, [
                        $enc->date_encaissement,
                        $enc->reference_encaissement,
                        $enc->student->name ?? '',
                        $enc->myClass->name ?? '',
                        $enc->payment->title ?? '',
                        $enc->type_encaissement,
                        number_format($enc->montant_original, 2),
                        $enc->pourcentage_pris_en_charge . '%',
                        number_format($enc->montant_encaisse, 2),
                        $enc->creator->name ?? '',
                        $enc->observations
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Imprimer les reçus d'encaissement
     */
    public function printReceipts(Request $request)
    {
        try {
            $encaissement_ids = $request->encaissement_ids;
            
            if (empty($encaissement_ids)) {
                return back()->with('flash_danger', 'Aucun encaissement sélectionné pour l\'impression.');
            }

            $encaissements = Encaissement::with(['student', 'payment', 'myClass'])
                                       ->whereIn('id', $encaissement_ids)
                                       ->get();

            $d['encaissements'] = $encaissements;
            $d['date_impression'] = now()->format('d/m/Y H:i');
            
            // Utiliser le template de reçu avec en-tête de l'école
            $pdf = PDF::loadView('pages.support_team.payments.encaissements.receipts_pdf', $d);
            
            return $pdf->download('recus_encaissements_' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur lors de l\'impression: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques d'encaissement
     */
    public function getStatistics(Request $request)
    {
        $year = $request->year ?: $this->year;
        $statistics = $this->encaissement->getStatistics($year);
        
        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    /**
     * Journal des encaissements
     */
    public function journal(Request $request)
    {
        $filters = [
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type_encaissement' => $request->type_encaissement,
            'class_id' => $request->class_id,
            'year' => $request->year ?: $this->year
        ];

        if ($filters['date_debut'] && $filters['date_fin']) {
            $encaissements = $this->encaissement->getByPeriod($filters['date_debut'], $filters['date_fin'], $filters);
        } else {
            $encaissements = $this->encaissement->getByYear($filters['year']);
        }

        $d['encaissements'] = $encaissements;
        $d['filters'] = $filters;
        $d['my_classes'] = $this->my_class->all();
        $d['statistics'] = $this->encaissement->getStatistics($filters['year']);

        return view('pages.support_team.payments.encaissements.journal', $d);
    }
}