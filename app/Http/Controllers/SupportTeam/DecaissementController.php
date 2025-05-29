<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Models\Decaissement;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Projet;
use App\User;

class DecaissementController extends Controller
{
    protected $year;

    public function __construct()
    {
        $this->middleware('teamSA');
        $this->year = Qs::getCurrentSession();
        
        // Vérifier et créer les tables nécessaires si elles n'existent pas
        $this->checkAndCreateRequiredTables();
    }
    
    /**
     * Vérifie et crée les tables nécessaires si elles n'existent pas
     */
    private function checkAndCreateRequiredTables()
    {
        try {
            // Vérifier et créer la table projets si elle n'existe pas
            if (!\Schema::hasTable('projets')) {
                \Schema::create('projets', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('nom');
                    $table->text('description')->nullable();
                    $table->date('date_debut')->nullable();
                    $table->date('date_fin')->nullable();
                    $table->decimal('budget', 15, 2)->nullable();
                    $table->string('statut')->default('actif');
                    $table->timestamps();
                });
                
                // Ajouter quelques projets par défaut
                \DB::table('projets')->insert([
                    ['nom' => 'Projet Éducatif', 'description' => 'Projet principal pour les activités éducatives', 'statut' => 'actif', 'created_at' => now(), 'updated_at' => now()],
                    ['nom' => 'Cantine Scolaire', 'description' => 'Gestion de la cantine scolaire', 'statut' => 'actif', 'created_at' => now(), 'updated_at' => now()],
                    ['nom' => 'Rénovation', 'description' => 'Travaux de rénovation des bâtiments', 'statut' => 'actif', 'created_at' => now(), 'updated_at' => now()]
                ]);
                
                \Log::info('Table projets créée avec succès.');
            }
            
            // Vérifier et créer la table decaissements si elle n'existe pas
            if (!\Schema::hasTable('decaissements')) {
                \Schema::create('decaissements', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->date('date_paiement');
                    $table->decimal('montant', 15, 2);
                    $table->string('montant_lettres')->nullable();
                    $table->string('motif');
                    $table->text('description')->nullable();
                    $table->string('beneficiaire');
                    $table->text('coordonnees')->nullable();
                    $table->string('methode_paiement')->default('espèces');
                    $table->string('reference')->nullable();
                    $table->string('piece')->nullable();
                    $table->text('details_bancaires')->nullable();
                    $table->string('projet_rubrique')->nullable();
                    $table->boolean('justificatif_present')->default(false);
                    $table->text('observations')->nullable();
                    $table->enum('status', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
                    $table->unsignedBigInteger('created_by');
                    $table->string('year');
                    $table->unsignedBigInteger('projet_id')->nullable();
                    $table->timestamps();

                    $table->foreign('created_by')->references('id')->on('users');
                    $table->foreign('projet_id')->references('id')->on('projets')->onDelete('set null');
                });
                
                \Log::info('Table decaissements créée avec succès.');
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création des tables: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la liste des décaissements avec filtres
     */
    public function index(Request $request)
    {
        $query = Decaissement::with(['user', 'projet'])->where('year', $this->year);

        // Filtrage par date
        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }

        // Filtrage par statut
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        // Filtrage par projet
        if ($request->filled('projet_filter')) {
            $query->where('projet_id', $request->projet_filter);
        }

        // Filtrage par utilisateur
        if ($request->filled('user_filter')) {
            $query->where('created_by', $request->user_filter);
        }

        $decaissements = $query->orderBy('date_paiement', 'desc')->get();

        // Récupérer les données pour les filtres
        $projets = Projet::all();
        $users = \App\User::all();

        return view('pages.support_team.decaissements.index', compact('decaissements', 'projets', 'users'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau décaissement
     */
    public function create()
    {
        $categories = Decaissement::getCategories();
        $methodes_paiement = Decaissement::getMethodesPaiement();
        $projets = Projet::all();

        return view('pages.support_team.decaissements.create', compact('categories', 'methodes_paiement', 'projets'));
    }

    /**
     * Enregistre un nouveau décaissement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'motif' => 'required|string|max:255',
            'beneficiaire' => 'required|string|max:255',
            'methode_paiement' => 'required|string',
            'piece' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['year'] = $this->year;
        $data['status'] = 'en_attente';

        // Gestion du fichier uploadé
        if ($request->hasFile('piece')) {
            $file = $request->file('piece');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['piece'] = $file->storeAs('decaissements', $filename, 'public');
        }

        Decaissement::create($data);

        return redirect()->route('decaissements.index')->with('flash_success', 'Décaissement créé avec succès.');
    }

    /**
     * Affiche les détails d'un décaissement
     */
    public function show($id)
    {
        $decaissement = Decaissement::with(['user', 'projet'])->findOrFail($id);
        return view('pages.support_team.decaissements.show', compact('decaissement'));
    }

    /**
     * Affiche le formulaire d'édition d'un décaissement
     */
    public function edit($id)
    {
        // Vérifier les permissions
        if (!Qs::userIsTeamSA() && !Qs::userIsAdmin()) {
            return redirect()->route('decaissements.index')->with('flash_danger', 'Accès non autorisé.');
        }

        $decaissement = Decaissement::findOrFail($id);
        $categories = Decaissement::getCategories();
        $methodes_paiement = Decaissement::getMethodesPaiement();
        $projets = Projet::all();

        return view('pages.support_team.decaissements.edit', compact('decaissement', 'categories', 'methodes_paiement', 'projets'));
    }

    /**
     * Met à jour un décaissement
     */
    public function update(Request $request, $id)
    {
        // Vérifier les permissions
        if (!Qs::userIsTeamSA() && !Qs::userIsAdmin()) {
            return redirect()->route('decaissements.index')->with('flash_danger', 'Accès non autorisé.');
        }

        $decaissement = Decaissement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'motif' => 'required|string|max:255',
            'beneficiaire' => 'required|string|max:255',
            'methode_paiement' => 'required|string',
            'description' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'details_bancaires' => 'nullable|string',
            'piece' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'projet_id' => 'nullable|exists:projets,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        // Gestion du fichier uploadé
        if ($request->hasFile('piece')) {
            // Supprimer l'ancien fichier s'il existe
            if ($decaissement->piece) {
                Storage::disk('public')->delete($decaissement->piece);
            }

            $file = $request->file('piece');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['piece'] = $file->storeAs('decaissements', $filename, 'public');
        }

        $decaissement->update($data);

        return redirect()->route('decaissements.show', $id)->with('flash_success', 'Décaissement mis à jour avec succès.');
    }

    /**
     * Supprime un décaissement
     */
    public function destroy($id)
    {
        // Vérifier les permissions
        if (!Qs::userIsTeamSA() && !Qs::userIsAdmin()) {
            return redirect()->route('decaissements.index')->with('flash_danger', 'Accès non autorisé.');
        }

        $decaissement = Decaissement::findOrFail($id);

        // Vérifier si le décaissement peut être supprimé (pas encore approuvé)
        if ($decaissement->status === 'approuve') {
            return redirect()->route('decaissements.index')->with('flash_danger', 'Impossible de supprimer un décaissement approuvé.');
        }

        // Supprimer le fichier associé s'il existe
        if ($decaissement->piece) {
            Storage::disk('public')->delete($decaissement->piece);
        }

        $decaissement->delete();

        return redirect()->route('decaissements.index')->with('flash_success', 'Décaissement supprimé avec succès.');
    }

    /**
     * Met à jour le statut d'un décaissement
     */
    public function updateStatus(Request $request, $id)
    {
        $decaissement = Decaissement::findOrFail($id);
        $decaissement->update(['status' => $request->status]);

        return back()->with('flash_success', 'Statut mis à jour avec succès.');
    }

    /**
     * Télécharge la pièce justificative d'un décaissement
     */
    public function downloadPiece($id)
    {
        $decaissement = Decaissement::findOrFail($id);

        if (!$decaissement->piece) {
            return back()->with('flash_danger', 'Aucune pièce justificative trouvée.');
        }

        $filePath = storage_path('app/public/' . $decaissement->piece);

        if (!file_exists($filePath)) {
            return back()->with('flash_danger', 'Fichier introuvable.');
        }

        return response()->download($filePath);
    }

    /**
     * Génère l'ordre de paiement imprimable
     */
    public function ordrePaiement($id)
    {
        $decaissement = Decaissement::with(['user', 'projet'])->findOrFail($id);
        return view('pages.support_team.decaissements.ordre_paiement_recu', compact('decaissement'));
    }
/**
 * Vérifie et corrige les décaissements
 */
public function verifyAndCorrectDecaissements(Request $request)
{
    // Vérifier les permissions
    if (!auth()->check() || !auth()->user()->is_teamSA) {
        return redirect()->route('decaissements.index')->with('flash_danger', 'Accès non autorisé.');
    }

    // Récupérer tous les décaissements
    $decaissements = Decaissement::with(['user', 'projet'])->get();

    foreach ($decaissements as $decaissement) {
        // Vérifier les faux décaissements (rejete ou manquants)
        if ($decaissement->status === 'rejete' || $this->isInvalidDecaissement($decaissement)) {
            // Supprimer le faux décaissement
            $this->destroy($decaissement->id);
            continue;
        }

        // Corriger les vrais décaissements
        $this->correctDecaissement($decaissement);
    }

    return redirect()->route('decaissements.index')->with('flash_success', 'Vérification et correction des décaissements terminées.');
}

/**
 * Vérifie si un décaissement est invalide
 */
protected function isInvalidDecaissement($decaissement)
{
    // Vérifier les champs obligatoires
    if (!$decaissement->montant || !$decaissement->motif || !$decaissement->beneficiaire) {
        return true;
    }

    // Vérifier l'incohérence entre montant et montant_lettres
    if ($decaissement->montant_lettres) {
        $montantEnLettres = \App\Helpers\DateHelper::convertirMontantEnLettres($decaissement->montant);
        if ($montantEnLettres !== $decaissement->montant_lettres) {
            return true;
        }
    }

    return false;
}

/**
 * Corrige un décaissement valide
 */
protected function correctDecaissement($decaissement)
{
    $data = [];

    // Mettre à jour montant_lettres si nécessaire
    if (!$decaissement->montant_lettres || $decaissement->montant_lettres !== \App\Helpers\DateHelper::convertirMontantEnLettres($decaissement->montant)) {
        $data['montant_lettres'] = \App\Helpers\DateHelper::convertirMontantEnLettres($decaissement->montant);
    }

    // Ajouter d'autres corrections si nécessaire

    if (!empty($data)) {
        $decaissement->update($data);
    }
}
}
