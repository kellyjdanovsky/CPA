<?php
namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Qs;
use App\Models\StudentRecord;
use App\Models\PaymentRecord;

class AnnualReportController extends Controller
{
    public function index(Request $request)
    {
        $session = Qs::getCurrentSession();
        $effectifs = StudentRecord::where('session', $session)->count();
        $recettes = PaymentRecord::where('year', $session)->sum('amt_paid');
        $impayes = PaymentRecord::where('year', $session)->sum('balance');
        
        $data = compact('session', 'effectifs', 'recettes', 'impayes');
        return view('pages.support_team.reports.annual_report', $data);
    }
    
    public function printReport(Request $request)
    {
        $session = Qs::getCurrentSession();
        $effectifs = StudentRecord::where('session', $session)->count();
        $recettes = PaymentRecord::where('year', $session)->sum('amt_paid');
        $data = compact('session', 'effectifs', 'recettes');
        return view('pages.support_team.reports.annual_report_print', $data);
    }
    
    public function exportExcel(Request $request)
    {
        // Logique d'exportation Excel (mock)
        return back()->with('flash_success', 'Exportation Excel générée avec succès.');
    }
}