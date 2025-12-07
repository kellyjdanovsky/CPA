<?php

namespace App\Repositories;

use App\Models\Decaissement;
use App\Helpers\Qs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DecaissementRepo
{
    /**
     * Obtenir tous les décaissements
     */
    public function getAll()
    {
        return Decaissement::with(['creator', 'approver', 'payer'])->get();
    }

    /**
     * Obtenir les décaissements par année
     */
    public function getByYear($year)
    {
        return Decaissement::with(['creator', 'approver', 'payer'])
                          ->year($year)
                          ->orderBy('date_decaissement', 'desc')
                          ->get();
    }

    /**
     * Obtenir les décaissements par période
     */
    public function getByPeriod($date_debut, $date_fin, $filters = [])
    {
        $query = Decaissement::with(['creator', 'approver', 'payer'])
                            ->period($date_debut, $date_fin);

        // Appliquer les filtres
        if (isset($filters['statut']) && $filters['statut']) {
            $query->statut($filters['statut']);
        }

        if (isset($filters['beneficiaire']) && $filters['beneficiaire']) {
            $query->where('beneficiaire', 'LIKE', '%' . $filters['beneficiaire'] . '%');
        }

        if (isset($filters['projet_rubrique']) && $filters['projet_rubrique']) {
            $query->projet($filters['projet_rubrique']);
        }

        if (isset($filters['mode_paiement']) && $filters['mode_paiement']) {
            $query->where('mode_paiement', $filters['mode_paiement']);
        }

        if (isset($filters['year']) && $filters['year']) {
            $query->year($filters['year']);
        }

        return $query->orderBy('date_decaissement', 'desc')->paginate(20)->withQueryString();
    }

    /**
     * Obtenir les décaissements par statut
     */
    public function getByStatut($statut, $year = null)
    {
        $query = Decaissement::with(['creator', 'approver', 'payer'])
                            ->statut($statut);
        
        if ($year) {
            $query->year($year);
        }
        
        return $query->orderBy('date_decaissement', 'desc')->get();
    }

    /**
     * Trouver un décaissement par ID
     */
    public function find($id)
    {
        return Decaissement::with(['creator', 'approver', 'payer'])->find($id);
    }

    /**
     * Créer un nouveau décaissement
     */
    public function create($data)
    {
        // Générer la référence OP
        $data['reference_op'] = Decaissement::generateReferenceOP($data['year']);
        
        // Convertir le montant en lettres
        $data['montant_lettres'] = Decaissement::nombreEnLettres($data['montant']);
        
        // Gérer l'upload de pièce justificative si présent
        if (isset($data['piece_justificative']) && $data['piece_justificative']) {
            $this->handleFileUpload($data);
        }

        return Decaissement::create($data);
    }

    /**
     * Mettre à jour un décaissement
     */
    public function update($id, $data)
    {
        $decaissement = $this->find($id);
        
        if ($decaissement) {
            // Gérer l'upload de nouvelle pièce justificative si présent
            if (isset($data['piece_justificative']) && $data['piece_justificative']) {
                // Supprimer l'ancien fichier
                if ($decaissement->piece_justificative_path) {
                    Storage::delete($decaissement->piece_justificative_path);
                }
                
                $this->handleFileUpload($data);
            }

            // Mettre à jour le montant en lettres si le montant a changé
            if (isset($data['montant']) && $data['montant'] != $decaissement->montant) {
                $data['montant_lettres'] = Decaissement::nombreEnLettres($data['montant']);
            }

            return $decaissement->update($data);
        }
        
        return false;
    }

    /**
     * Supprimer un décaissement
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $decaissement = $this->find($id);
            
            if ($decaissement) {
                // Supprimer le fichier de pièce justificative
                if ($decaissement->piece_justificative_path) {
                    Storage::delete($decaissement->piece_justificative_path);
                }
                
                return $decaissement->delete();
            }
            
            return false;
        });
    }

    /**
     * Approuver un décaissement
     */
    public function approve($id, $approved_by)
    {
        $decaissement = $this->find($id);
        
        if ($decaissement && $decaissement->statut === 'EN_ATTENTE') {
            return $decaissement->update([
                'statut' => 'APPROUVE',
                'approved_by' => $approved_by
            ]);
        }
        
        return false;
    }

    /**
     * Marquer comme payé
     */
    public function markAsPaid($id, $paid_by)
    {
        $decaissement = $this->find($id);
        
        if ($decaissement && $decaissement->statut === 'APPROUVE') {
            return $decaissement->update([
                'statut' => 'PAYE',
                'paid_by' => $paid_by
            ]);
        }
        
        return false;
    }

    /**
     * Annuler un décaissement
     */
    public function cancel($id)
    {
        $decaissement = $this->find($id);
        
        if ($decaissement && in_array($decaissement->statut, ['EN_ATTENTE', 'APPROUVE'])) {
            return $decaissement->update(['statut' => 'ANNULE']);
        }
        
        return false;
    }

    /**
     * Gérer l'upload de fichier
     */
    private function handleFileUpload(&$data)
    {
        if (isset($data['piece_justificative'])) {
            $file = $data['piece_justificative'];
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('decaissements/pieces_justificatives', $filename, 'public');
            
            $data['piece_justificative_path'] = $path;
            $data['piece_justificative_nom'] = $file->getClientOriginalName();
            
            // Supprimer le fichier des données pour éviter l'erreur de sauvegarde
            unset($data['piece_justificative']);
        }
    }

    /**
     * Valider une pièce justificative
     */
    public function validatePieceJustificative($id, $validated = true)
    {
        $decaissement = $this->find($id);
        
        if ($decaissement) {
            return $decaissement->update(['piece_justificative_valide' => $validated]);
        }
        
        return false;
    }

    /**
     * Obtenir les statistiques de décaissements
     */
    public function getStatistics($year = null, $period = null)
    {
        $year = $year ?: Qs::getCurrentSession();
        
        $query = Decaissement::year($year);
        
        if ($period) {
            $query->period($period['debut'], $period['fin']);
        }

        $stats = [
            'total_decaissements' => $query->count(),
            'total_montant' => $query->sum('montant'),
            'en_attente_count' => $query->statut('EN_ATTENTE')->count(),
            'en_attente_montant' => $query->statut('EN_ATTENTE')->sum('montant'),
            'approuve_count' => $query->statut('APPROUVE')->count(),
            'approuve_montant' => $query->statut('APPROUVE')->sum('montant'),
            'paye_count' => $query->statut('PAYE')->count(),
            'paye_montant' => $query->statut('PAYE')->sum('montant'),
            'annule_count' => $query->statut('ANNULE')->count(),
            'annule_montant' => $query->statut('ANNULE')->sum('montant'),
        ];

        // Statistiques par projet/rubrique
        $stats['by_projet'] = Decaissement::year($year)
            ->select('projet_rubrique', DB::raw('COUNT(*) as count'), DB::raw('SUM(montant) as montant'))
            ->whereNotNull('projet_rubrique')
            ->groupBy('projet_rubrique')
            ->get();

        // Statistiques par mode de paiement
        $stats['by_mode_paiement'] = Decaissement::year($year)
            ->select('mode_paiement', DB::raw('COUNT(*) as count'), DB::raw('SUM(montant) as montant'))
            ->groupBy('mode_paiement')
            ->get();

        return $stats;
    }

    /**
     * Obtenir les décaissements par mois pour un graphique
     */
    public function getMonthlyData($year = null)
    {
        $year = $year ?: Qs::getCurrentSession();
        
        return Decaissement::year($year)
            ->select(
                DB::raw('MONTH(date_decaissement) as mois'),
                DB::raw('COUNT(*) as nombre_decaissements'),
                DB::raw('SUM(montant) as montant_total')
            )
            ->groupBy(DB::raw('MONTH(date_decaissement)'))
            ->orderBy('mois')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->mois => $item];
            });
    }

    /**
     * Obtenir les projets/rubriques disponibles
     */
    public function getProjetsRubriques()
    {
        return Decaissement::select('projet_rubrique')
                          ->distinct()
                          ->whereNotNull('projet_rubrique')
                          ->pluck('projet_rubrique')
                          ->sort()
                          ->values();
    }

    /**
     * Obtenir les modes de paiement disponibles
     */
    public function getPaymentMethods()
    {
        return Decaissement::select('mode_paiement')
                          ->distinct()
                          ->whereNotNull('mode_paiement')
                          ->pluck('mode_paiement')
                          ->sort()
                          ->values();
    }

    /**
     * Exporter les décaissements pour Excel
     */
    public function getExportData($filters = [])
    {
        $query = Decaissement::with(['creator', 'approver', 'payer']);

        // Appliquer les filtres
        if (isset($filters['date_debut']) && isset($filters['date_fin'])) {
            $query->period($filters['date_debut'], $filters['date_fin']);
        }

        if (isset($filters['statut']) && $filters['statut']) {
            $query->statut($filters['statut']);
        }

        if (isset($filters['beneficiaire']) && $filters['beneficiaire']) {
            $query->where('beneficiaire', 'LIKE', '%' . $filters['beneficiaire'] . '%');
        }

        if (isset($filters['projet_rubrique']) && $filters['projet_rubrique']) {
            $query->projet($filters['projet_rubrique']);
        }

        if (isset($filters['year']) && $filters['year']) {
            $query->year($filters['year']);
        }

        return $query->orderBy('date_decaissement', 'desc')->get();
    }

    /**
     * Obtenir les décaissements pour impression OP
     */
    public function getForPrint($ids = [])
    {
        $query = Decaissement::with(['creator', 'approver']);
        
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        
        return $query->get();
    }
}