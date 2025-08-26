<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Repositories\RecetteRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\PaymentRepo;
use App\Models\Recette;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;

class RecetteController extends Controller
{
    protected $recette, $my_class, $payment, $year;

    public function __construct(RecetteRepo $recette, MyClassRepo $my_class, PaymentRepo $payment)
    {
        $this->recette = $recette;
        $this->my_class = $my_class;
        $this->payment = $payment;
        $this->year = Qs::getCurrentSession();

        $this->middleware('teamAccount');
    }

    /**
     * Afficher la page principale des recettes
     */
    public function index(Request $request)
    {
        // Filtres par défaut
        $filters = [
            'date_debut' => $request->date_debut ?: Carbon::now()->startOfMonth()->format('Y-m-d'),
            'date_fin' => $request->date_fin ?: Carbon::now()->endOfMonth()->format('Y-m-d'),
            'type_recette' => $request->type_recette,
            'class_id' => $request->class_id,
            'mode_paiement' => $request->mode_paiement,
            'year' => $request->year ?: $this->year
        ];

        // Obtenir les recettes avec filtres
        $recettes = $this->recette->getByPeriod($filters['date_debut'], $filters['date_fin'], $filters);

        // Obtenir les statistiques
        $statistics = $this->recette->getStatistics($filters['year'], [
            'debut' => $filters['date_debut'],
            'fin' => $filters['date_fin']
        ]);

        // Données pour le graphique mensuel
        $monthly_data = $this->recette->getMonthlyData($filters['year']);

        $d = [
            'recettes' => $recettes,
            'filters' => $filters,
            'statistics' => $statistics,
            'monthly_data' => $monthly_data,
            'my_classes' => $this->my_class->all(),
            'payment_methods' => $this->recette->getPaymentMethods(),
            'recette_types' => $this->recette->getRecetteTypes(),
            'year' => $this->year,
            'selected_year' => $this->year
        ];

        return view('pages.support_team.payments.recettes.index', $d);
    }

    /**
     * Afficher le formulaire de création d'une recette manuelle
     */
    public function create()
    {
        $d = [
            'my_classes' => $this->my_class->all(),
            'payments' => $this->payment->getPayment(['year' => $this->year])->get(),
            'payment_methods' => $this->recette->getPaymentMethods(),
            'recette_types' => $this->recette->getRecetteTypes(),
            'year' => $this->year
        ];

        return view('pages.support_team.payments.recettes.create', $d);
    }

    /**
     * Enregistrer une nouvelle recette manuelle
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date_recette' => 'required|date',
            'beneficiaire_nom' => 'required|string|max:255',
            'montant_encaisse' => 'required|numeric|min:0.01',
            'mode_paiement' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'observations' => 'nullable|string|max:1000'
        ], [], [
            'date_recette' => 'Date de recette',
            'beneficiaire_nom' => 'Nom du bénéficiaire',
            'montant_encaisse' => 'Montant encaissé',
            'mode_paiement' => 'Mode de paiement',
            'description' => 'Description',
            'observations' => 'Observations'
        ]);

        try {
            $data = $request->only([
                'date_recette',
                'beneficiaire_nom',
                'montant_encaisse',
                'mode_paiement',
                'description',
                'observations'
            ]);

            $data['created_by'] = auth()->id();
            $data['year'] = $this->year;

            $recette = $this->recette->createManualRecette($data);

            return redirect()->route('payments.recettes.index')
                           ->with('flash_success', 'Recette créée avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('flash_danger', 'Erreur lors de la création de la recette: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une recette
     */
    public function show($id)
    {
        $recette = $this->recette->find($id);
        
        if (!$recette) {
            return back()->with('flash_danger', 'Recette introuvable.');
        }

        $d['recette'] = $recette;
        
        return view('pages.support_team.payments.recettes.show', $d);
    }

    /**
     * Afficher le formulaire de modification d'une recette
     */
    public function edit($id)
    {
        $recette = $this->recette->find($id);
        
        if (!$recette) {
            return back()->with('flash_danger', 'Recette introuvable.');
        }

        // Seules les recettes manuelles (DIVERS) peuvent être modifiées
        if ($recette->type_recette !== 'DIVERS') {
            return back()->with('flash_danger', 'Cette recette ne peut pas être modifiée car elle est générée automatiquement.');
        }

        $d = [
            'recette' => $recette,
            'my_classes' => $this->my_class->all(),
            'payment_methods' => $this->recette->getPaymentMethods(),
            'recette_types' => $this->recette->getRecetteTypes()
        ];

        return view('pages.support_team.payments.recettes.edit', $d);
    }

    /**
     * Mettre à jour une recette
     */
    public function update(Request $request, $id)
    {
        $recette = $this->recette->find($id);
        
        if (!$recette) {
            return back()->with('flash_danger', 'Recette introuvable.');
        }

        // Seules les recettes manuelles peuvent être modifiées
        if ($recette->type_recette !== 'DIVERS') {
            return back()->with('flash_danger', 'Cette recette ne peut pas être modifiée.');
        }

        $this->validate($request, [
            'date_recette' => 'required|date',
            'beneficiaire_nom' => 'required|string|max:255',
            'montant_encaisse' => 'required|numeric|min:0.01',
            'mode_paiement' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'observations' => 'nullable|string|max:1000'
        ]);

        try {
            $data = $request->only([
                'date_recette',
                'beneficiaire_nom',
                'montant_encaisse',
                'mode_paiement',
                'description',
                'observations'
            ]);

            $this->recette->update($id, $data);

            return redirect()->route('payments.recettes.index')
                           ->with('flash_success', 'Recette mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('flash_danger', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une recette
     */
    public function destroy($id)
    {
        try {
            $recette = $this->recette->find($id);
            
            if (!$recette) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recette introuvable.'
                ], 404);
            }

            // Seules les recettes manuelles peuvent être supprimées
            if ($recette->type_recette !== 'DIVERS') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette recette ne peut pas être supprimée car elle est générée automatiquement.'
                ], 422);
            }

            $deleted = $this->recette->delete($id);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recette supprimée avec succès.'
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
     * Synchroniser les recettes avec les reçus existants
     */
    public function syncWithReceipts(Request $request)
    {
        try {
            $year = $request->year ?: $this->year;
            $created_count = $this->recette->syncWithReceipts($year);

            return response()->json([
                'success' => true,
                'message' => "$created_count recette(s) synchronisée(s) avec succès.",
                'created_count' => $created_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporter les recettes en Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_recette' => $request->type_recette,
                'class_id' => $request->class_id,
                'mode_paiement' => $request->mode_paiement,
                'year' => $request->year ?: $this->year
            ];

            $recettes = $this->recette->getExportData($filters);

            $filename = 'recettes_' . date('Y-m-d') . '.xlsx';
            
            return response()->streamDownload(function () use ($recettes) {
                $handle = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($handle, [
                    'Date',
                    'Référence',
                    'Bénéficiaire',
                    'Classe',
                    'Paiement',
                    'Type',
                    'Montant',
                    'Mode de Paiement',
                    'Description',
                    'Créé par',
                    'Observations'
                ]);

                // Données
                foreach ($recettes as $rec) {
                    fputcsv($handle, [
                        $rec->date_recette,
                        $rec->reference_recette,
                        $rec->beneficiaire,
                        $rec->myClass->name ?? '',
                        $rec->payment->title ?? '',
                        $rec->type_recette,
                        number_format($rec->montant_encaisse, 2),
                        $rec->mode_paiement,
                        $rec->description,
                        $rec->creator->name ?? '',
                        $rec->observations
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
     * Exporter les recettes en PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $filters = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_recette' => $request->type_recette,
                'class_id' => $request->class_id,
                'mode_paiement' => $request->mode_paiement,
                'year' => $request->year ?: $this->year
            ];

            $recettes = $this->recette->getExportData($filters);
            
            $statistics = $this->recette->getStatistics($filters['year'], [
                'debut' => $filters['date_debut'],
                'fin' => $filters['date_fin']
            ]);

            $d = [
                'recettes' => $recettes,
                'filters' => $filters,
                'statistics' => $statistics,
                'date_impression' => now()->format('d/m/Y H:i'),
                'school_header' => 'College Prive Adventiste Avaratetezana Ampitatafika Antananarivo Madagascar'
            ];

            $pdf = PDF::loadView('pages.support_team.payments.recettes.export_pdf', $d);
            
            return $pdf->download('rapport_recettes_' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
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

            $statistics = $this->recette->getStatistics($year, $period);
            $monthly_data = $this->recette->getMonthlyData($year);

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
     * Obtenir les données pour les graphiques
     */
    public function getChartData(Request $request)
    {
        try {
            $year = $request->year ?: $this->year;
            $type = $request->type ?: 'monthly'; // monthly, weekly, daily

            switch ($type) {
                case 'monthly':
                    $data = $this->recette->getMonthlyData($year);
                    break;
                default:
                    $data = $this->recette->getMonthlyData($year);
            }

            return response()->json([
                'success' => true,
                'chart_data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des données graphiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tableau de bord des recettes
     */
    public function dashboard()
    {
        $statistics = $this->recette->getStatistics($this->year);
        $monthly_data = $this->recette->getMonthlyData($this->year);
        
        // Recettes récentes
        $recent_recettes = $this->recette->getByPeriod(
            Carbon::now()->subDays(7)->format('Y-m-d'),
            Carbon::now()->format('Y-m-d')
        )->take(10);

        $d = [
            'statistics' => $statistics,
            'monthly_data' => $monthly_data,
            'recent_recettes' => $recent_recettes,
            'year' => $this->year
        ];

        return view('pages.support_team.payments.recettes.dashboard', $d);
    }
}