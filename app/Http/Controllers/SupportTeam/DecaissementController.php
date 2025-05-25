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
use App\Models\User;

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
        $projets = \App\Models\Projet::orderBy('nom')->get(); // Utilisation du namespace complet
        $users = \App\Models\User::whereHas('user_type', function ($q) {
            $q->whereIn('title', ['super_admin', 'admin', 'support_team']); // Ajustez les rôles si nécessaire
        })->orderBy('name')->get(); // Utilisation du namespace complet

        return view('pages.support_team.decaissements.index', compact('decaissements', 'projets', 'users'));
    }

    /**
     * Affiche le formulaire de création d'un décaissement
     */
    public function create()
    {
        $methodes_paiement = Decaissement::getMethodesPaiement();
        $categories = Decaissement::getCategories();

        return view('pages.support_team.decaissements.create', compact('methodes_paiement', 'categories'));
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
            'methode_paiement' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'piece' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
            'details_bancaires' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['year'] = $this->year;
        $data['status'] = 'en_attente';

        // Traitement du fichier pièce jointe
        if ($request->hasFile('piece')) {
            $file = $request->file('piece');
            $filename = 'decaissement_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/decaissements', $filename);
            $data['piece'] = $filename;
        }

        Decaissement::create($data);

        return redirect()->route('decaissements.index')->with('flash_success', 'Décaissement enregistré avec succès');
    }

    /**
     * Affiche les détails d'un décaissement
     */
    public function show($id)
    {
        $decaissement = Decaissement::findOrFail($id);

        return view('pages.support_team.decaissements.show', compact('decaissement'));
    }

    /**
     * Affiche le formulaire de modification d'un décaissement
     */
    public function edit($id)
    {
        $decaissement = Decaissement::findOrFail($id);
        $methodes_paiement = Decaissement::getMethodesPaiement();
        $categories = Decaissement::getCategories();

        return view('pages.support_team.decaissements.edit', compact('decaissement', 'methodes_paiement', 'categories'));
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
            'methode_paiement' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'piece' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
            'details_bancaires' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        // Traitement du fichier pièce jointe
        if ($request->hasFile('piece')) {
            // Supprimer l'ancien fichier s'il existe
            if ($decaissement->piece) {
                Storage::delete('public/decaissements/' . $decaissement->piece);
            }

            $file = $request->file('piece');
            $filename = 'decaissement_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/decaissements', $filename);
            $data['piece'] = $filename;
        }

        $decaissement->update($data);

        return redirect()->route('decaissements.index')->with('flash_success', 'Décaissement mis à jour avec succès');
    }

    /**
     * Supprime un décaissement
     */
    public function destroy($id)
    {
        $decaissement = Decaissement::findOrFail($id);

        // Supprimer le fichier pièce jointe s'il existe
        if ($decaissement->piece) {
            Storage::delete('public/decaissements/' . $decaissement->piece);
        }

        $decaissement->delete();

        return redirect()->route('decaissements.index')->with('flash_success', 'Décaissement supprimé avec succès');
    }

    /**
     * Change le statut d'un décaissement
     */
    public function updateStatus(Request $request, $id)
    {
        $decaissement = Decaissement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:en_attente,approuve,rejete',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $decaissement->status = $request->status;
        $decaissement->save();

        $status_text = [
            'en_attente' => 'en attente',
            'approuve' => 'approuvé',
            'rejete' => 'rejeté'
        ][$request->status];

        return redirect()->back()->with('flash_success', 'Statut du décaissement mis à jour : ' . $status_text);
    }

    /**
     * Télécharge la pièce jointe d'un décaissement
     */
    public function downloadPiece($id)
    {
        $decaissement = Decaissement::findOrFail($id);

        if (!$decaissement->piece) {
            return redirect()->back()->with('flash_danger', 'Aucune pièce jointe disponible');
        }

        $path = storage_path('app/public/decaissements/' . $decaissement->piece);

        if (!file_exists($path)) {
            return redirect()->back()->with('flash_danger', 'Fichier introuvable');
        }

        return response()->download($path);
    }
}
