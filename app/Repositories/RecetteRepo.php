<?php

namespace App\Repositories;

use App\Models\Recette;
use App\Models\Encaissement;
use App\Models\Receipt;
use App\Helpers\Qs;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecetteRepo
{
    /**
     * Obtenir toutes les recettes
     */
    public function getAll()
    {
        return Recette::with(['student', 'payment', 'myClass', 'encaissement', 'creator'])->get();
    }

    /**
     * Obtenir les recettes par année
     */
    public function getByYear($year)
    {
        return Recette::with(['student', 'payment', 'myClass', 'encaissement', 'creator'])
                     ->year($year)
                     ->orderBy('date_recette', 'desc')
                     ->get();
    }

    /**
     * Obtenir les recettes par période
     */
    public function getByPeriod($date_debut, $date_fin, $filters = [])
    {
        $query = Recette::with(['student', 'payment', 'myClass', 'encaissement', 'creator'])
                        ->period($date_debut, $date_fin);

        // Appliquer les filtres
        if (isset($filters['type_recette']) && $filters['type_recette']) {
            $query->type($filters['type_recette']);
        }

        if (isset($filters['class_id']) && $filters['class_id']) {
            $query->class($filters['class_id']);
        }

        if (isset($filters['mode_paiement']) && $filters['mode_paiement']) {
            $query->where('mode_paiement', $filters['mode_paiement']);
        }

        if (isset($filters['year']) && $filters['year']) {
            $query->year($filters['year']);
        }

        return $query->orderBy('date_recette', 'desc')->get();
    }

    /**
     * Trouver une recette par ID
     */
    public function find($id)
    {
        return Recette::with(['student', 'payment', 'myClass', 'encaissement', 'creator'])->find($id);
    }

    /**
     * Créer une nouvelle recette
     */
    public function create($data)
    {
        return Recette::create($data);
    }

    /**
     * Mettre à jour une recette
     */
    public function update($id, $data)
    {
        $recette = $this->find($id);
        if ($recette) {
            return $recette->update($data);
        }
        return false;
    }

    /**
     * Supprimer une recette
     */
    public function delete($id)
    {
        $recette = $this->find($id);
        if ($recette) {
            return $recette->delete();
        }
        return false;
    }

    /**
     * Synchroniser les recettes avec les reçus existants
     */
    public function syncWithReceipts($year = null)
    {
        $year = $year ?: Qs::getCurrentSession();
        
        return DB::transaction(function () use ($year) {
            // Obtenir tous les reçus de l'année qui n'ont pas de recette associée
            $receipts = Receipt::whereHas('pr', function ($query) use ($year) {
                $query->where('year', $year);
            })->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('recettes')
                      ->whereRaw('recettes.payment_record_id = receipts.pr_id')
                      ->whereRaw('recettes.montant_encaisse = receipts.amt_paid');
            })->with(['pr.student', 'pr.payment', 'pr.student.student_records'])->get();

            $created_count = 0;

            foreach ($receipts as $receipt) {
                // Déterminer le type de recette basé sur l'étudiant
                $type_recette = 'NORMAL';
                if ($receipt->pr && $receipt->pr->student && $receipt->pr->student->student_records) {
                    $student_record = $receipt->pr->student->student_records->where('year', $year)->first();
                    if ($student_record) {
                        if (strpos($student_record->adm_no, 'ADRA') !== false) {
                            $type_recette = 'ADRA';
                        } elseif (strpos($student_record->adm_no, 'TEAM3') !== false) {
                            $type_recette = 'TEAM3';
                        }
                    }
                }

                // Créer la recette
                $this->create([
                    'date_recette' => $receipt->created_at->format('Y-m-d'),
                    'student_id' => $receipt->pr->student_id,
                    'class_id' => $receipt->pr->student->student_records->where('year', $year)->first()->my_class_id ?? null,
                    'payment_id' => $receipt->pr->payment_id,
                    'payment_record_id' => $receipt->pr_id,
                    'montant_encaisse' => $receipt->amt_paid,
                    'mode_paiement' => $receipt->methode ?? $receipt->payment_method ?? 'Non spécifié',
                    'reference_recette' => $this->generateReference($type_recette, $year),
                    'type_recette' => $type_recette,
                    'description' => 'Synchronisation automatique depuis reçu #' . $receipt->id,
                    'created_by' => 1, // Admin par défaut
                    'year' => $year,
                    'observations' => $receipt->observations
                ]);

                $created_count++;
            }

            return $created_count;
        });
    }

    /**
     * Créer une recette manuelle (non liée à un paiement étudiant)
     */
    public function createManualRecette($data)
    {
        $data['type_recette'] = 'DIVERS';
        $data['reference_recette'] = $this->generateReference('DIVERS', $data['year']);
        
        return $this->create($data);
    }

    /**
     * Obtenir les statistiques de recettes
     */
    public function getStatistics($year = null, $period = null)
    {
        $year = $year ?: Qs::getCurrentSession();
        
        $query = Recette::year($year);
        
        if ($period) {
            $query->period($period['debut'], $period['fin']);
        }

        $stats = [
            'total_recettes' => $query->count(),
            'total_montant' => $query->sum('montant_encaisse'),
            'normal_count' => $query->type('NORMAL')->count(),
            'normal_montant' => $query->type('NORMAL')->sum('montant_encaisse'),
            'adra_count' => $query->type('ADRA')->count(),
            'adra_montant' => $query->type('ADRA')->sum('montant_encaisse'),
            'team3_count' => $query->type('TEAM3')->count(),
            'team3_montant' => $query->type('TEAM3')->sum('montant_encaisse'),
            'divers_count' => $query->type('DIVERS')->count(),
            'divers_montant' => $query->type('DIVERS')->sum('montant_encaisse'),
        ];

        // Statistiques par mode de paiement
        $stats['by_mode_paiement'] = Recette::year($year)
            ->select('mode_paiement', DB::raw('COUNT(*) as count'), DB::raw('SUM(montant_encaisse) as montant'))
            ->groupBy('mode_paiement')
            ->get();

        return $stats;
    }

    /**
     * Obtenir les recettes par mois pour un graphique
     */
    public function getMonthlyData($year = null)
    {
        $year = $year ?: Qs::getCurrentSession();
        
        return Recette::year($year)
            ->select(
                DB::raw('MONTH(date_recette) as mois'),
                DB::raw('COUNT(*) as nombre_recettes'),
                DB::raw('SUM(montant_encaisse) as montant_total')
            )
            ->groupBy(DB::raw('MONTH(date_recette)'))
            ->orderBy('mois')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->mois => $item];
            });
    }

    /**
     * Générer une référence de recette
     */
    private function generateReference($type, $year)
    {
        return Recette::generateReference($type, $year);
    }

    /**
     * Obtenir les modes de paiement disponibles
     */
    public function getPaymentMethods()
    {
        return Recette::select('mode_paiement')
                     ->distinct()
                     ->whereNotNull('mode_paiement')
                     ->pluck('mode_paiement')
                     ->sort()
                     ->values();
    }

    /**
     * Obtenir les types de recettes disponibles
     */
    public function getRecetteTypes()
    {
        return [
            'NORMAL' => 'Paiement Normal',
            'ADRA' => 'Paiement ADRA (75%)',
            'TEAM3' => 'Paiement TEAM 3 (100%)',
            'DIVERS' => 'Recettes Diverses'
        ];
    }

    /**
     * Exporter les recettes pour Excel
     */
    public function getExportData($filters = [])
    {
        $query = Recette::with(['student', 'payment', 'myClass']);

        // Appliquer les filtres
        if (isset($filters['date_debut']) && isset($filters['date_fin'])) {
            $query->period($filters['date_debut'], $filters['date_fin']);
        }

        if (isset($filters['type_recette']) && $filters['type_recette']) {
            $query->type($filters['type_recette']);
        }

        if (isset($filters['class_id']) && $filters['class_id']) {
            $query->class($filters['class_id']);
        }

        if (isset($filters['year']) && $filters['year']) {
            $query->year($filters['year']);
        }

        return $query->orderBy('date_recette', 'desc')->get();
    }
}