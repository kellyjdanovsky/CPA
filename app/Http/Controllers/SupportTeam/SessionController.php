<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\StudentRecord;
use App\Models\Payment;
use App\Models\PaymentRecord;
use App\Repositories\StudentRepo;
use App\Repositories\MyClassRepo;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    protected $student, $my_class;

    public function __construct(StudentRepo $student, MyClassRepo $my_class)
    {
        $this->middleware('teamSA', ['except' => ['changeSession', 'getSessions']]);
        $this->middleware('super_admin', ['only' => ['destroy']]);
        
        $this->student = $student;
        $this->my_class = $my_class;
    }

    /**
     * Afficher la liste des sessions
     */
    public function index()
    {
        $data['sessions'] = Session::getAllSorted();
        return view('pages.support_team.sessions.index', $data);
    }

    /**
     * Créer une nouvelle session
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:sessions,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        Session::create($request->all());

        return back()->with('flash_success', 'Session créée avec succès');
    }

    /**
     * Modifier une session
     */
    public function update(Request $request, $id)
    {
        $session = Session::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:sessions,name,' . $id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        $session->update($request->all());

        return back()->with('flash_success', 'Session modifiée avec succès');
    }

    /**
     * Supprimer une session
     */
    public function destroy($id)
    {
        $session = Session::findOrFail($id);
        
        // Vérifier s'il y a des données liées
        $hasData = StudentRecord::where('session', $session->name)->exists() ||
                   Payment::where('year', $session->name)->exists();
        
        if ($hasData) {
            return back()->with('flash_danger', 'Impossible de supprimer cette session car elle contient des données');
        }

        $session->delete();
        return back()->with('flash_success', 'Session supprimée avec succès');
    }

    /**
     * Changer la session active (identique au système des settings)
     */
    public function changeSession(Request $request)
    {
        $sessionName = $request->session_name;

        // Vérifier que la session existe (soit dans notre table sessions, soit dans le format standard)
        $sessionExists = Session::where('name', $sessionName)->exists();

        // Valider le format de session (YYYY-YYYY)
        if (!$sessionExists && !preg_match('/^\d{4}-\d{4}$/', $sessionName)) {
            return response()->json(['success' => false, 'message' => 'Format de session invalide']);
        }

        // Mettre à jour le setting current_session (comme dans les settings)
        $settingRepo = new \App\Repositories\SettingRepo();
        $settingRepo->update('current_session', $sessionName);

        // Aussi stocker dans la session utilisateur pour compatibilité
        session(['selected_school_year' => $sessionName]);

        return response()->json([
            'success' => true,
            'message' => 'Session changée vers ' . $sessionName,
            'session_name' => $sessionName
        ]);
    }

    /**
     * Obtenir toutes les sessions pour le dropdown (identique au système des settings)
     */
    public function getSessions()
    {
        // Générer les sessions comme dans les settings (3 ans avant à 1 an après)
        $sessions = [];
        $currentYear = date('Y');

        for ($y = $currentYear - 3; $y <= $currentYear + 1; $y++) {
            $sessionName = ($y - 1) . '-' . $y;
            $sessions[] = [
                'name' => $sessionName,
                'is_active' => false // Pas utilisé dans ce contexte
            ];
        }

        // Inverser pour avoir les plus récentes en premier
        $sessions = array_reverse($sessions);

        $currentSession = Qs::getCurrentSession();

        return response()->json([
            'sessions' => $sessions,
            'current_session' => $currentSession
        ]);
    }

    /**
     * Définir une session comme active par défaut
     */
    public function setActive($id)
    {
        $session = Session::findOrFail($id);
        $session->setAsActive();

        return back()->with('flash_success', 'Session ' . $session->name . ' définie comme active');
    }
}
