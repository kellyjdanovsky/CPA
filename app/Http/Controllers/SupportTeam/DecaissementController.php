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
        $decaissement = Decaissement::findOrFail($id);

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
        $decaissement = Decaissement::findOrFail($id);

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
        return view('pages.support_team.decaissements.ordre_paiement', compact('decaissement'));
    }
}
