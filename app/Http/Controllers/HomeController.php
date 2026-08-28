<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Repositories\UserRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;

class HomeController extends Controller
{
    protected $user;
    protected $my_class;
    protected $student;

    public function __construct(UserRepo $user, MyClassRepo $my_class, StudentRepo $student)
    {
        $this->user = $user;
        $this->my_class = $my_class;
        $this->student = $student;
    }


    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function privacy_policy()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.privacy_policy', $data);
    }

    public function terms_of_use()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.terms_of_use', $data);
    }

    public function dashboard()
    {
        $d = [];
        if(Qs::userIsTeamSAT()){
            $d['current_session'] = Qs::getCurrentSession();
            $d['users'] = $this->user->getAll();
            $d['classes'] = $this->my_class->all();
            $d['total_classes'] = $d['classes']->count();

            $d['class_student_counts'] = [];
            $total_active_students = 0;
            foreach($d['classes'] as $class) {
                $class_count = $this->student->getRecord([
                    'my_class_id' => $class->id,
                    'session' => $d['current_session']
                ])->count();
                $d['class_student_counts'][$class->id] = $class_count;
                $total_active_students += $class_count;
            }
            $d['total_active_students'] = $total_active_students;
            $d['total_teachers'] = $d['users']->where('user_type', 'teacher')->count();
            $d['success_rate'] = $this->calculateSuccessRate($d['current_session']);
            $d['promotion_stats'] = $this->calculatePromotionStats($d['current_session']);

            // Financial KPIs
            $current_year = $d['current_session'];
            $d['total_receipts_month'] = \App\Models\Receipt::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amt_paid');
            $d['total_decaissements_month'] = \App\Models\Decaissement::whereMonth('date_decaissement', now()->month)->whereYear('date_decaissement', now()->year)->where('statut', 'PAYE')->sum('montant');
            
            $total_unpaid = \App\Models\PaymentRecord::where('year', $current_year)->where('paid', 0)->sum('balance');
            $total_paid = \App\Models\PaymentRecord::where('year', $current_year)->sum('amt_paid');
            $total_expected = \App\Models\Payment::where('year', $current_year)->sum('amount') * $total_active_students; // approximate
            $d['recovery_rate'] = $total_expected > 0 ? round(($total_paid / $total_expected) * 100, 1) : 0;
            
            // Repartition des paiements pour doughnut chart
            $d['payment_status'] = [
                'paye' => \App\Models\PaymentRecord::where('year', $current_year)->where('paid', 1)->count(),
                'partiel' => \App\Models\PaymentRecord::where('year', $current_year)->where('paid', 0)->where('amt_paid', '>', 0)->count(),
                'impaye' => \App\Models\PaymentRecord::where('year', $current_year)->where('paid', 0)->where('amt_paid', 0)->count(),
            ];

            // Top 10 unpaid students
            $d['top_unpaid'] = \App\Models\PaymentRecord::where('year', $current_year)->where('paid', 0)->where('balance', '>', 0)
                ->select('student_id', \Illuminate\Support\Facades\DB::raw('SUM(balance) as total_balance'))
                ->groupBy('student_id')->orderByDesc('total_balance')->limit(10)
                ->with('student.my_class')->get();

            // Monthly revenue chart data (last 12 months)
            $monthly_revenue = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthly_revenue[] = [
                    'month' => $date->format('M Y'),
                    'recettes' => \App\Models\Receipt::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('amt_paid'),
                    'depenses' => \App\Models\Decaissement::whereMonth('date_decaissement', $date->month)->whereYear('date_decaissement', $date->year)->where('statut', 'PAYE')->sum('montant'),
                ];
            }
            $d['monthly_revenue'] = $monthly_revenue;

            // Alerts
            $backupDir = storage_path('app/backups');
            $d['pending_backups'] = true;
            if (\Illuminate\Support\Facades\File::exists($backupDir)) {
                $files = \Illuminate\Support\Facades\File::files($backupDir);
                foreach ($files as $file) {
                    if (\Carbon\Carbon::createFromTimestamp(\Illuminate\Support\Facades\File::lastModified($file))->diffInDays(now()) < 7) {
                        $d['pending_backups'] = false;
                        break;
                    }
                }
            }

            $d['unpaid_count_critical'] = \App\Models\PaymentRecord::where('year', $current_year)->where('paid', 0)->where('balance', '>', 0)->count();
            
            $currentMonth = now()->month;
            $d['term_ending'] = in_array($currentMonth, [5, 6, 11, 12]) && now()->day > 15;

            // Recent activity
            $d['recent_receipts'] = \App\Models\Receipt::latest()->limit(10)->with('pr.student')->get();
            $d['recent_notifications'] = \App\Models\InternalNotification::latest()->limit(5)->get();
        }

        return view('pages.support_team.dashboard', $d);
    }

    /**
     * Calculer le taux de réussite basé sur les décisions des exam_records
     */
    private function calculateSuccessRate($session)
    {
        $examRecords = \App\Models\ExamRecord::where('year', $session)->get();

        if ($examRecords->count() == 0) {
            return 85; // Valeur par défaut si pas de données
        }

        $totalStudents = $examRecords->count();
        $successfulStudents = $examRecords->whereIn('decision', ['Passant', 'Promu', 'Admis'])->count();

        return $totalStudents > 0 ? round(($successfulStudents / $totalStudents) * 100, 1) : 85;
    }

    /**
     * Calculer les statistiques de promotion/redoublement
     */
    private function calculatePromotionStats($session)
    {
        $examRecords = \App\Models\ExamRecord::where('year', $session)->get();

        $stats = [
            'total' => $examRecords->count(),
            'passants' => $examRecords->where('decision', 'Passant')->count(),
            'redoublants' => $examRecords->where('decision', 'Redoublant')->count(),
            'quittés' => $examRecords->where('decision', 'Quitté')->count(),
            'promus' => $examRecords->whereIn('decision', ['Passant', 'Promu'])->count(),
        ];

        // Calculer les pourcentages
        if ($stats['total'] > 0) {
            $stats['passants_percent'] = round(($stats['passants'] / $stats['total']) * 100, 1);
            $stats['redoublants_percent'] = round(($stats['redoublants'] / $stats['total']) * 100, 1);
            $stats['quittés_percent'] = round(($stats['quittés'] / $stats['total']) * 100, 1);
            $stats['promus_percent'] = round(($stats['promus'] / $stats['total']) * 100, 1);
        } else {
            $stats['passants_percent'] = 0;
            $stats['redoublants_percent'] = 0;
            $stats['quittés_percent'] = 0;
            $stats['promus_percent'] = 0;
        }

        return $stats;
    }
}
