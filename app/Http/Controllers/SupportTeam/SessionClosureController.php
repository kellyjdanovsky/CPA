<?php
namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Qs;
use App\Models\Setting;

class SessionClosureController extends Controller
{
    public function wizard()
    {
        $session = Qs::getCurrentSession();
        return view('pages.support_team.sessions.closure_wizard', compact('session'));
    }
    
    public function executeStep(Request $request)
    {
        $step = $request->input('step');
        // Logique pour chaque étape
        return response()->json(['success' => true, 'message' => "Étape $step validée avec succès."]);
    }
    
    public function printClosureReport(Request $request)
    {
        $session = Qs::getCurrentSession();
        return view('pages.support_team.sessions.closure_report_print', compact('session'));
    }
}