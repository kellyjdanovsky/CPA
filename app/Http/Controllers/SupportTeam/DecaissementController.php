<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Repositories\DecaissementRepo;
use App\Models\Decaissement;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use Carbon\Carbon;

class DecaissementController extends Controller
{
    protected $decaissement, $year;

    public function __construct(DecaissementRepo $decaissement)
    {
        $this->decaissement = $decaissement;
        $this->year = Qs::getCurrentSession();

        $this->middleware('teamAccount');
    }

    /**
     * Afficher la page principale des décaissements
     */
    public function index(Request $request)
    {
        // Filtres par défaut
        $filters = [
            'date_debut' => $request->date_debut ?: Carbon::now()->startOfMonth()->format('Y-m-d'),
            'date_fin' => $request->date_fin ?: Carbon::now()->endOfMonth()->format('Y-m-d'),
            'statut' => $request->statut,
            'beneficiaire' => $request->beneficiaire,
            'projet_rubrique' => $request->projet_rubrique,
            'mode_paiement' => $request->mode_paiement,
            'year' => $request->year ?: $this->year
        ];

        // Obtenir les décaissements avec filtres
        $decaissements = $this->decaissement->getByPeriod($filters['date_debut'], $filters['date_fin'], $filters);

        // Obtenir les statistiques
        $statistics = $this->decaissement->getStatistics($filters['year'], [
            'debut' => $filters['date_debut'],
            'fin' => $filters['date_fin']
        ]);

        // Données pour le graphique mensuel
        $monthly_data = $this->decaissement->getMonthlyData($filters['year']);

        $d = [
            'decaissements' => $decaissements,
            'filters' => $filters,
            'statistics' => $statistics,
            'monthly_data' => $monthly_data,
            'statuts' => Decaissement::getStatuts(),
            'projets_rubriques' => $this->decaissement->getProjetsRubriques(),
            'payment_methods' => $this->decaissement->getPaymentMethods(),
            'year' => $this->year,
            'selected_year' => $this->year
        ];

        return view('pages.support_team.payments.decaissements.index', $d);
    }

    /**
     * Afficher le formulaire de création d'un nouveau décaissement
     */
    public function create()
    {
        $d = [
            'statuts' => Decaissement::getStatuts(),
            'projets_rubriques' => $this->decaissement->getProjetsRubriques(),
            'payment_methods' => $this->decaissement->getPaymentMethods(),
            'year' => $this->year
        ];

        return view('pages.support_team.payments.decaissements.create', $d);
    }

    /**
     * Enregistrer un nouveau décaissement
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date_decaissement' => 'required|date',
            'beneficiaire' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:1000',
            'mode_paiement' => 'required|string|max:100',
            'projet_rubrique' => 'nullable|string|max:255',
            'piece_justificative' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'observations' => 'nullable|string|max:1000'
        ], [], [
            'date_decaissement' => 'Date de décaissement',
            'beneficiaire' => 'Bénéficiaire',
            'montant' => 'Montant',
            'motif' => 'Motif',
            'mode_paiement' => 'Mode de paiement',
            'projet_rubrique' => 'Projet/Rubrique',
            'piece_justificative' => 'Pièce justificative',
            'observations' => 'Observations'
        ]);

        try {
            $data = $request->only([
                'date_decaissement',
                'beneficiaire',
                'montant',
                'motif',
                'mode_paiement',
                'projet_rubrique',
                'observations'
            ]);

            $data['created_by'] = auth()->id();
            $data['year'] = $this->year;
            
            // Gérer l'upload de pièce justificative
            if ($request->hasFile('piece_justificative')) {
                $data['piece_justificative'] = $request->file('piece_justificative');
            }

            $decaissement = $this->decaissement->create($data);

            return redirect()->route('payments.decaissements.show', $decaissement->id)
                           ->with('flash_success', 'Ordre de paiement créé avec succès. Référence: ' . $decaissement->reference_op);

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('flash_danger', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un décaissement
     */
    public function show($id)
    {
        $decaissement = $this->decaissement->find($id);
        
        if (!$decaissement) {
            return back()->with('flash_danger', 'Décaissement introuvable.');
        }

        $d['decaissement'] = $decaissement;
        
        return view('pages.support_team.payments.decaissements.show', $d);
    }

    /**
     * Afficher le formulaire de modification d'un décaissement
     */
    public function edit($id)
    {
        $decaissement = $this->decaissement->find($id);
        
        if (!$decaissement) {
            return back()->with('flash_danger', 'Décaissement introuvable.');
        }

        // Seuls les décaissements en attente peuvent être modifiés
        if ($decaissement->statut !== 'EN_ATTENTE') {
            return back()->with('flash_danger', 'Ce décaissement ne peut plus être modifié.');
        }

        $d = [
            'decaissement' => $decaissement,
            'statuts' => Decaissement::getStatuts(),
            'projets_rubriques' => $this->decaissement->getProjetsRubriques(),
            'payment_methods' => $this->decaissement->getPaymentMethods()
        ];

        return view('pages.support_team.payments.decaissements.edit', $d);
    }

    /**
     * Mettre à jour un décaissement
     */
    public function update(Request $request, $id)
    {
        $decaissement = $this->decaissement->find($id);
        
        if (!$decaissement) {
            return back()->with('flash_danger', 'Décaissement introuvable.');
        }

        if ($decaissement->statut !== 'EN_ATTENTE') {
            return back()->with('flash_danger', 'Ce décaissement ne peut plus être modifié.');
        }

        $this->validate($request, [
            'date_decaissement' => 'required|date',
            'beneficiaire' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:1000',
            'mode_paiement' => 'required|string|max:100',
            'projet_rubrique' => 'nullable|string|max:255',
            'piece_justificative' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'observations' => 'nullable|string|max:1000'
        ]);

        try {
            $data = $request->only([
                'date_decaissement',
                'beneficiaire',
                'montant',
                'motif',
                'mode_paiement',
                'projet_rubrique',
                'observations'
            ]);

            // Gérer l'upload de nouvelle pièce justificative
            if ($request->hasFile('piece_justificative')) {
                $data['piece_justificative'] = $request->file('piece_justificative');
            }

            $this->decaissement->update($id, $data);

            return redirect()->route('payments.decaissements.show', $id)
                           ->with('flash_success', 'Décaissement mis à jour avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('flash_danger', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un décaissement
     */
    public function destroy($id)
    {
        try {
            $decaissement = $this->decaissement->find($id);
            
            if (!$decaissement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Décaissement introuvable.'
                ], 404);
            }

            // Seuls les décaissements en attente peuvent être supprimés
            if ($decaissement->statut !== 'EN_ATTENTE') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce décaissement ne peut pas être supprimé.'
                ], 422);
            }

            $deleted = $this->decaissement->delete($id);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Décaissement supprimé avec succès.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression.'
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
     * Approuver un décaissement
     */
    public function approve($id)
    {
        try {
            $approved = $this->decaissement->approve($id, auth()->id());
            
            if ($approved) {
                return response()->json([
                    'success' => true,
                    'message' => 'Décaissement approuvé avec succès.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'approuver ce décaissement.'
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
     * Marquer comme payé
     */
    public function markAsPaid($id)
    {
        try {
            $paid = $this->decaissement->markAsPaid($id, auth()->id());
            
            if ($paid) {
                return response()->json([
                    'success' => true,
                    'message' => 'Décaissement marqué comme payé.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de marquer ce décaissement comme payé.'
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
     * Annuler un décaissement
     */
    public function cancel($id)
    {
        try {
            $cancelled = $this->decaissement->cancel($id);
            
            if ($cancelled) {
                return response()->json([
                    'success' => true,
                    'message' => 'Décaissement annulé avec succès.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'annuler ce décaissement.'
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
     * Valider une pièce justificative
     */
    public function validatePieceJustificative(Request $request, $id)
    {
        try {
            $validated = $this->decaissement->validatePieceJustificative($id, $request->validated);
            
            if ($validated) {
                $message = $request->validated ? 'Pièce justificative validée.' : 'Validation de la pièce justificative annulée.';
                
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la validation.'
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
     * Télécharger une pièce justificative
     */
    public function downloadPieceJustificative($id)
    {
        $decaissement = $this->decaissement->find($id);
        
        if (!$decaissement) {
            return back()->with('flash_danger', 'Décaissement introuvable.');
        }

        if (!$decaissement->hasPieceJustificative()) {
            return back()->with('flash_danger', 'Aucune pièce justificative disponible.');
        }

        try {
            return Storage::download($decaissement->piece_justificative_path, $decaissement->piece_justificative_nom);
        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur lors du téléchargement: ' . $e->getMessage());
        }
    }

    /**
     * Imprimer l'ordre de paiement
     */
    public function printOP($id)
    {
        $decaissement = $this->decaissement->find($id);
        
        if (!$decaissement) {
            return back()->with('flash_danger', 'Décaissement introuvable.');
        }

        $d = [
            'decaissement' => $decaissement,
            'school_header' => 'College Prive Adventiste Avaratetezana Ampitatafika Antananarivo Madagascar',
            'date_impression' => now()->format('d/m/Y H:i')
        ];

        $pdf = PDF::loadView('pages.support_team.payments.decaissements.print_op', $d);
        
        return $pdf->download('OP_' . $decaissement->reference_op . '.pdf');
    }

    /**
     * Imprimer plusieurs ordres de paiement (2 par page A4)
     */
    public function printMultipleOP(Request $request)
    {
        $ids = $request->decaissement_ids;
        
        if (empty($ids)) {
            return back()->with('flash_danger', 'Aucun décaissement sélectionné.');
        }

        $decaissements = $this->decaissement->getForPrint($ids);

        $d = [
            'decaissements' => $decaissements,
            'school_header' => 'College Prive Adventiste Avaratetezana Ampitatafika Antananarivo Madagascar',
            'date_impression' => now()->format('d/m/Y H:i')
        ];

        $pdf = PDF::loadView('pages.support_team.payments.decaissements.print_multiple_op', $d);
        
        return $pdf->download('Ordres_Paiement_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exporter les décaissements en Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'statut' => $request->statut,
                'beneficiaire' => $request->beneficiaire,
                'projet_rubrique' => $request->projet_rubrique,
                'mode_paiement' => $request->mode_paiement,
                'year' => $request->year ?: $this->year
            ];

            $decaissements = $this->decaissement->getExportData($filters);

            $filename = 'decaissements_' . date('Y-m-d') . '.xlsx';
            
            return response()->streamDownload(function () use ($decaissements) {
                $handle = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($handle, [
                    'Date',
                    'Référence OP',
                    'Bénéficiaire',
                    'Montant',
                    'Montant en Lettres',
                    'Motif',
                    'Mode de Paiement',
                    'Projet/Rubrique',
                    'Statut',
                    'Pièce Justificative',
                    'Créé par',
                    'Approuvé par',
                    'Payé par',
                    'Observations'
                ]);

                // Données
                foreach ($decaissements as $dec) {
                    fputcsv($handle, [
                        $dec->date_decaissement,
                        $dec->reference_op,
                        $dec->beneficiaire,
                        number_format($dec->montant, 2),
                        $dec->montant_lettres,
                        $dec->motif,
                        $dec->mode_paiement,
                        $dec->projet_rubrique,
                        $dec->statut,
                        $dec->hasPieceJustificative() ? 'Oui' : 'Non',
                        $dec->creator->name ?? '',
                        $dec->approver->name ?? '',
                        $dec->payer->name ?? '',
                        $dec->observations
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
     * Obtenir les statistiques pour le dashboard
     */
    public function getStatistics(Request $request)
    {
        try {
            $year = $request->year ?: $this->year;
            $period = null;
            
            if ($request->date_debut && $request->date_fin) {
                $period = [
                    'debut' => $request->date_debut,
                    'fin' => $request->date_fin
                ];
            }

            $statistics = $this->decaissement->getStatistics($year, $period);
            $monthly_data = $this->decaissement->getMonthlyData($year);

            return response()->json([
                'success' => true,
                'statistics' => $statistics,
                'monthly_data' => $monthly_data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Journal des décaissements
     */
    public function journal(Request $request)
    {
        return $this->index($request);
    }
}