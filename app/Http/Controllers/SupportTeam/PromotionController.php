<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $my_class, $student;

    public function __construct(MyClassRepo $my_class, StudentRepo $student)
    {
        $this->middleware('teamSA');

        $this->my_class = $my_class;
        $this->student = $student;
    }

    public function promotion($fc = NULL, $fs = NULL, $tc = NULL, $ts = NULL)
    {
        $d['old_year'] = $old_yr = Qs::getSetting('current_session');
        $old_yr = explode('-', $old_yr);
        $d['new_year'] = ++$old_yr[0].'-'.++$old_yr[1];
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['selected'] = false;

        if($fc && $fs && $tc && $ts){
            $d['selected'] = true;
            $d['fc'] = $fc;
            $d['fs'] = $fs;
            $d['tc'] = $tc;
            $d['ts'] = $ts;
            $d['students'] = $sts = $this->student->getRecord(['my_class_id' => $fc, 'section_id' => $fs, 'session' => $d['old_year']])->get();

            if($sts->count() < 1){
                return redirect()->route('students.promotion')->with('flash_success', __('msg.nstp'));
            }
        }

        return view('pages.support_team.students.promotion.index', $d);
    }

    public function selector(Request $req)
    {
        return redirect()->route('students.promotion', [$req->fc, $req->fs, $req->tc, $req->ts]);
    }

    public function promote(Request $req, $fc, $fs, $tc, $ts)
    {
        $oy = Qs::getSetting('current_session'); $d = [];
        $old_yr = explode('-', $oy);
        $ny = ++$old_yr[0].'-'.++$old_yr[1];
        $students = $this->student->getRecord(['my_class_id' => $fc, 'section_id' => $fs, 'session' => $oy ])->get()->sortBy('user.name');

        if($students->count() < 1){
            return redirect()->route('students.promotion')->with('flash_danger', __('msg.srnf'));
        }

        foreach($students as $st){
            $p = 'p-'.$st->id;
            $p = $req->$p;

            if($p === 'P'){ // Réinscrire (Promouvoir) - Nouvelle classe dans la session suivante
                // Vérifier si un enregistrement existe déjà pour cet élève dans la nouvelle session
                $existing_record = $this->student->getRecord([
                    'user_id' => $st->user_id,
                    'session' => $ny
                ])->first();

                if (!$existing_record) {
                    // Créer un nouvel enregistrement pour la session suivante
                    $newStudentData = [
                        'user_id' => $st->user_id,
                        'my_class_id' => $tc,
                        'section_id' => $ts,
                        'session' => $ny,
                        'adm_no' => $this->generateAdmissionNumber($ny),
                        'my_parent_id' => $st->my_parent_id,
                        'dorm_id' => $st->dorm_id,
                        'dorm_room_no' => $st->dorm_room_no,
                        'house' => $st->house,
                        'age' => $st->age,
                        'year_admitted' => $st->year_admitted,
                        'grad' => 0,
                    ];
                    $this->student->createRecord($newStudentData);
                }
            }

            if($p === 'D'){ // Redoubler - Même classe dans la session suivante
                // Vérifier si un enregistrement existe déjà pour cet élève dans la nouvelle session
                $existing_record = $this->student->getRecord([
                    'user_id' => $st->user_id,
                    'session' => $ny
                ])->first();

                if (!$existing_record) {
                    // Créer un nouvel enregistrement pour la session suivante (même classe)
                    $newStudentData = [
                        'user_id' => $st->user_id,
                        'my_class_id' => $fc, // Même classe
                        'section_id' => $fs,  // Même section
                        'session' => $ny,
                        'adm_no' => $this->generateAdmissionNumber($ny),
                        'my_parent_id' => $st->my_parent_id,
                        'dorm_id' => $st->dorm_id,
                        'dorm_room_no' => $st->dorm_room_no,
                        'house' => $st->house,
                        'age' => $st->age,
                        'year_admitted' => $st->year_admitted,
                        'grad' => 0,
                    ];
                    $this->student->createRecord($newStudentData);
                }
            }

            if($p === 'G'){ // Quitter (Diplômé) - Marquer comme diplômé dans la session actuelle
                // Mettre à jour l'enregistrement actuel comme diplômé
                $d = [
                    'grad' => 1,
                    'grad_date' => $oy,
                ];
                $this->student->updateRecord($st->id, $d);
            }

            // Créer l'enregistrement de promotion
            $promote = [
                'from_class' => $fc,
                'from_section' => $fs,
                'grad' => ($p === 'G') ? 1 : 0,
                'to_class' => ($p === 'P') ? $tc : $fc, // Nouvelle classe si promu, même classe sinon
                'to_section' => ($p === 'P') ? $ts : $fs, // Nouvelle section si promu, même section sinon
                'student_id' => $st->user_id,
                'from_session' => $oy,
                'to_session' => ($p === 'G') ? $oy : $ny, // Session actuelle si diplômé, suivante sinon
                'status' => $p,
            ];

            $this->student->createPromotion($promote);
        }
        return redirect()->route('students.promotion')->with('flash_success', __('msg.update_ok'));
    }

    public function manage()
    {
        $data['promotions'] = $this->student->getAllPromotions();
        $data['old_year'] = Qs::getCurrentSession();
        $data['new_year'] = Qs::getNextSession();
        
        // Récupérer les élèves diplômés de la session actuelle
        $data['graduated_students'] = $this->student->getGradRecord(['session' => $data['old_year']])->get();

        // Calculer les statistiques de promotion
        $promotions = $data['promotions'];
        $data['stats'] = [
            'total' => $promotions->count(),
            'reinscrit' => $promotions->where('status', 'P')->count(), // Promus (Réinscrits)
            'redouble' => $promotions->where('status', 'D')->count(),   // Non promus (Redoublants)
            'quitte' => $promotions->where('status', 'G')->count(),     // Diplômés (Quittés)
        ];

        // Ajouter les statistiques des élèves diplômés
        $data['stats']['graduated_total'] = $data['graduated_students']->count();
        $data['stats']['combined_total'] = $data['stats']['total'] + $data['stats']['graduated_total'];

        // Calculer les pourcentages
        $total = $data['stats']['total'];
        $data['stats']['reinscrit_percent'] = $total > 0 ? round(($data['stats']['reinscrit'] / $total) * 100, 1) : 0;
        $data['stats']['redouble_percent'] = $total > 0 ? round(($data['stats']['redouble'] / $total) * 100, 1) : 0;
        $data['stats']['quitte_percent'] = $total > 0 ? round(($data['stats']['quitte'] / $total) * 100, 1) : 0;

        return view('pages.support_team.students.promotion.reset', $data);
    }

    public function reset($promotion_id)
    {
        $this->reset_single($promotion_id);

        return redirect()->route('students.promotion_manage')->with('flash_success', __('msg.update_ok'));
    }

    public function reset_all()
    {
        $next_session = Qs::getNextSession();
        $where = ['from_session' => Qs::getCurrentSession(), 'to_session' => $next_session];
        $proms = $this->student->getPromotions($where);

        if ($proms->count()){
          foreach ($proms as $prom){
              $this->reset_single($prom->id);

              // Delete Marks if Already Inserted for New Session
              $this->delete_old_marks($prom->student_id, $next_session);
          }
        }

        return Qs::jsonUpdateOk();
    }

    public function reset_graduated($student_id)
    {
        // Réinitialiser un élève diplômé - le remettre comme élève actif normal
        $current_session = Qs::getCurrentSession();
        
        // Trouver l'enregistrement de l'élève diplômé
        $graduated_record = $this->student->getGradRecord(['user_id' => $student_id, 'session' => $current_session])->first();
        
        if ($graduated_record) {
            // Mettre à jour l'enregistrement existant pour le remettre comme élève actif
            $data['grad'] = 0;
            $data['grad_date'] = null;
            $this->student->updateRecord($graduated_record->id, $data);
        }
        
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function reset_all_graduated()
    {
        // Réinitialiser tous les élèves diplômés de la session actuelle
        $current_session = Qs::getCurrentSession();
        $graduated_students = $this->student->getGradRecord(['session' => $current_session])->get();
        
        if ($graduated_students->count() > 0) {
            foreach ($graduated_students as $student) {
                // Mettre à jour l'enregistrement existant pour le remettre comme élève actif
                $data['grad'] = 0;
                $data['grad_date'] = null;
                $this->student->updateRecord($student->id, $data);
            }
        }
        
        return Qs::jsonUpdateOk();
    }

    protected function delete_old_marks($student_id, $year)
    {
        Mark::where(['student_id' => $student_id, 'year' => $year])->delete();
    }

    protected function reset_single($promotion_id)
    {
        $prom = $this->student->findPromotion($promotion_id);

        // Trouver l'enregistrement de l'élève pour la session spécifique
        $student_record = $this->student->getRecord([
            'user_id' => $prom->student_id,
            'session' => $prom->from_session
        ])->first();

        if ($student_record) {
            // Mettre à jour l'enregistrement existant au lieu d'en créer un nouveau
            $data['my_class_id'] = $prom->from_class;
            $data['section_id'] = $prom->from_section;
            $data['session'] = $prom->from_session;
            $data['grad'] = 0;
            $data['grad_date'] = null;

            $this->student->updateRecord($student_record->id, $data);
        }

        return $this->student->deletePromotion($promotion_id);
    }

    /**
     * Générer un numéro d'admission unique pour une session
     */
    private function generateAdmissionNumber($session)
    {
        $year = explode('-', $session)[1]; // Prendre la deuxième année
        $prefix = 'CPA/PRS802/' . $year . '/';
        $number = rand(1000, 9999);
        $admNo = $prefix . $number;

        // Vérifier l'unicité
        while (\App\Models\StudentRecord::where('adm_no', $admNo)->exists()) {
            $number = rand(1000, 9999);
            $admNo = $prefix . $number;
        }

        return $admNo;
    }


}
