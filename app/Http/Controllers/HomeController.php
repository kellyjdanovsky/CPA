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
            // Utiliser la session des settings comme source principale (cohérence avec le reste du système)
            $d['current_session'] = Qs::getCurrentSession();

            // Récupérer tous les utilisateurs
            $d['users'] = $this->user->getAll();

            // Récupérer toutes les classes
            $d['classes'] = $this->my_class->all();
            $d['total_classes'] = $d['classes']->count();

            // Calculer le nombre d'élèves actifs pour la session courante
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

            // Calculer le nombre d'enseignants actifs
            $d['total_teachers'] = $d['users']->where('user_type', 'teacher')->count();

            // Calculer le taux de réussite basé sur les décisions des exam_records
            $d['success_rate'] = $this->calculateSuccessRate($d['current_session']);

            // Calculer les statistiques de promotion/redoublement
            $d['promotion_stats'] = $this->calculatePromotionStats($d['current_session']);
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
