<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjetController extends Controller
{
    public function __construct()
    {
        $this->middleware('teamSA');
    }

    /**
     * Affiche la liste des projets
     */
    public function index()
    {
        $projets = Projet::orderBy('nom')->get();
        return view('pages.support_team.projets.index', compact('projets'));
    }

    /**
     * Affiche le formulaire de création d'un projet
     */
    public function create()
    {
        return view('pages.support_team.projets.create');
    }

    /**
     * Enregistre un nouveau projet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'budget' => 'nullable|numeric|min:0',
            'statut' => 'required|string|in:actif,inactif,termine',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Projet::create($request->all());

        return redirect()->route('projets.index')->with('flash_success', 'Projet créé avec succès');
    }

    /**
     * Affiche les détails d'un projet
     */
    public function show($id)
    {
        $projet = Projet::findOrFail($id);
        return view('pages.support_team.projets.show', compact('projet'));
    }

    /**
     * Affiche le formulaire de modification d'un projet
     */
    public function edit($id)
    {
        $projet = Projet::findOrFail($id);
        return view('pages.support_team.projets.edit', compact('projet'));
    }

    /**
     * Met à jour un projet
     */
    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'budget' => 'nullable|numeric|min:0',
            'statut' => 'required|string|in:actif,inactif,termine',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $projet->update($request->all());

        return redirect()->route('projets.index')->with('flash_success', 'Projet mis à jour avec succès');
    }

    /**
     * Supprime un projet
     */
    public function destroy($id)
    {
        $projet = Projet::findOrFail($id);
        $projet->delete();

        return redirect()->route('projets.index')->with('flash_success', 'Projet supprimé avec succès');
    }
}
