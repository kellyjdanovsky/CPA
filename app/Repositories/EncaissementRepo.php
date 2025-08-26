<?php

namespace App\Repositories;

use App\Models\Encaissement;
use App\Models\PaymentRecord;
use App\Models\Recette;
use App\Repositories\RecetteRepo;
use App\User;
use App\Helpers\Qs;
use Illuminate\Support\Facades\DB;

class EncaissementRepo
{
    /**
     * Obtenir tous les encaissements
     */
    public function getAll()
    {
        return Encaissement::with(['student', 'payment', 'myClass', 'creator'])->get();
    }

    /**
     * Obtenir les encaissements par année
     */
    public function getByYear($year)
    {
        return Encaissement::with(['student', 'payment', 'myClass', 'creator'])
                          ->year($year)
                          ->orderBy('date_encaissement', 'desc')
                          ->get();
    }

    /**
     * Obtenir les encaissements par classe et année
     */
    public function getByClassAndYear($class_id, $year)
    {
        return Encaissement::with(['student', 'payment', 'myClass', 'creator'])
                          ->class($class_id)
                          ->year($year)
                          ->orderBy('date_encaissement', 'desc')
                          ->get();
    }

    /**
     * Obtenir les encaissements par type
     */
    public function getByType($type, $year = null)
    {
        $query = Encaissement::with(['student', 'payment', 'myClass', 'creator'])
                            ->type($type);
        
        if ($year) {
            $query->year($year);
        }
        
        return $query->orderBy('date_encaissement', 'desc')->get();
    }

    /**
     * Obtenir les encaissements par période
     */
    public function getByPeriod($date_debut, $date_fin, $filters = [])
    {
        $query = Encaissement::with(['student', 'payment', 'myClass', 'creator'])
                            ->period($date_debut, $date_fin);

        // Appliquer les filtres
        if (isset($filters['type_encaissement']) && $filters['type_encaissement']) {
            $query->type($filters['type_encaissement']);
        }

        if (isset($filters['class_id']) && $filters['class_id']) {
            $query->class($filters['class_id']);
        }

        if (isset($filters['student_id']) && $filters['student_id']) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['year']) && $filters['year']) {
            $query->year($filters['year']);
        }

        return $query->orderBy('date_encaissement', 'desc')->get();
    }

    /**
     * Trouver un encaissement par ID
     */
    public function find($id)
    {
        return Encaissement::with(['student', 'payment', 'myClass', 'creator'])->find($id);
    }

    /**
     * Créer un nouvel encaissement
     */
    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            // Créer l'encaissement
            $encaissement = Encaissement::create($data);
            
            // Créer automatiquement une entrée dans les recettes
            $this->createRecetteFromEncaissement($encaissement);
            
            return $encaissement;
        });
    }

    /**
     * Mettre à jour un encaissement
     */
    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $encaissement = $this->find($id);
            if ($encaissement) {
                $encaissement->update($data);
                
                // Mettre à jour la recette associée
                $this->updateRecetteFromEncaissement($encaissement);
                
                return $encaissement;
            }
            return false;
        });
    }

    /**
     * Supprimer un encaissement
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $encaissement = $this->find($id);
            if ($encaissement) {
                // Supprimer la recette associée
                if ($encaissement->recette) {
                    $encaissement->recette()->delete();
                }
                
                return $encaissement->delete();
            }
            return false;
        });
    }

    /**
     * Obtenir les étudiants éligibles pour un encaissement
     */
    public function getEligibleStudents($class_id, $payment_id, $type)
    {
        return User::whereHas('student_records', function ($query) use ($class_id, $type) {
            $query->where('my_class_id', $class_id)
                  ->where('year', Qs::getCurrentSession());
            
            if ($type === 'ADRA') {
                $query->where('adm_no', 'LIKE', '%ADRA%');
            } elseif ($type === 'TEAM3') {
                $query->where('adm_no', 'LIKE', '%TEAM3%');
            }
        })->whereHas('payment_records', function ($query) use ($payment_id) {
            $query->where('payment_id', $payment_id)
                  ->where('paid', 0); // Seulement les paiements non soldés
        })->with(['student_records' => function ($query) use ($class_id) {
            $query->where('my_class_id', $class_id);
        }, 'payment_records' => function ($query) use ($payment_id) {
            $query->where('payment_id', $payment_id);
        }])->get();
    }

    /**
     * Traiter un encaissement en lot
     */
    public function processBatchEncaissement($class_id, $payment_id, $type, $students_data)
    {
        return DB::transaction(function () use ($class_id, $payment_id, $type, $students_data) {
            $encaissements = [];
            $year = Qs::getCurrentSession();
            
            foreach ($students_data as $student_data) {
                $student_id = $student_data['student_id'];
                $payment_record_id = $student_data['payment_record_id'];
                $montant_original = $student_data['montant_original'];
                
                // Calculer le montant à encaisser
                $montant_encaisse = Encaissement::calculateMontantEncaisse($montant_original, $type);
                $pourcentage = $type === 'ADRA' ? 75 : 100;
                
                // Créer l'encaissement
                $encaissement = $this->create([
                    'student_id' => $student_id,
                    'payment_id' => $payment_id,
                    'payment_record_id' => $payment_record_id,
                    'class_id' => $class_id,
                    'type_encaissement' => $type,
                    'montant_original' => $montant_original,
                    'pourcentage_pris_en_charge' => $pourcentage,
                    'montant_encaisse' => $montant_encaisse,
                    'date_encaissement' => now()->format('Y-m-d'),
                    'reference_encaissement' => Encaissement::generateReference($type, $year),
                    'created_by' => auth()->id(),
                    'year' => $year,
                    'observations' => $student_data['observations'] ?? null
                ]);
                
                $encaissements[] = $encaissement;
            }
            
            return $encaissements;
        });
    }

    /**
     * Créer une recette à partir d'un encaissement
     */
    private function createRecetteFromEncaissement($encaissement)
    {
        $recetteRepo = new RecetteRepo();
        
        return $recetteRepo->create([
            'date_recette' => $encaissement->date_encaissement,
            'student_id' => $encaissement->student_id,
            'class_id' => $encaissement->class_id,
            'payment_id' => $encaissement->payment_id,
            'payment_record_id' => $encaissement->payment_record_id,
            'encaissement_id' => $encaissement->id,
            'montant_encaisse' => $encaissement->montant_encaisse,
            'mode_paiement' => 'Encaissement ' . $encaissement->type_encaissement,
            'reference_recette' => Recette::generateReference($encaissement->type_encaissement, $encaissement->year),
            'type_recette' => $encaissement->type_encaissement,
            'description' => 'Encaissement automatique depuis ' . $encaissement->type_encaissement,
            'created_by' => $encaissement->created_by,
            'year' => $encaissement->year,
            'observations' => $encaissement->observations
        ]);
    }

    /**
     * Mettre à jour la recette associée à un encaissement
     */
    private function updateRecetteFromEncaissement($encaissement)
    {
        $recette = Recette::where('encaissement_id', $encaissement->id)->first();
        
        if ($recette) {
            $recetteRepo = new RecetteRepo();
            return $recetteRepo->update($recette->id, [
                'date_recette' => $encaissement->date_encaissement,
                'montant_encaisse' => $encaissement->montant_encaisse,
                'observations' => $encaissement->observations
            ]);
        }
        
        return false;
    }

    /**
     * Obtenir les statistiques d'encaissement
     */
    public function getStatistics($year = null)
    {
        $year = $year ?: Qs::getCurrentSession();
        
        return [
            'total_encaissements' => Encaissement::year($year)->count(),
            'total_montant' => Encaissement::year($year)->sum('montant_encaisse'),
            'adra_count' => Encaissement::year($year)->type('ADRA')->count(),
            'adra_montant' => Encaissement::year($year)->type('ADRA')->sum('montant_encaisse'),
            'team3_count' => Encaissement::year($year)->type('TEAM3')->count(),
            'team3_montant' => Encaissement::year($year)->type('TEAM3')->sum('montant_encaisse'),
        ];
    }
}