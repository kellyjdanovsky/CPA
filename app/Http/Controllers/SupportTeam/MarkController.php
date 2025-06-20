<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Http\Requests\Mark\MarkSelector;
use App\Models\Setting;
use App\Repositories\ExamRepo;
use App\Repositories\MarkRepo;
use App\Repositories\MyClassRepo;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MarkController extends Controller
{
    protected $my_class, $exam, $student, $year, $user, $mark;

    public function __construct(MyClassRepo $my_class, ExamRepo $exam, StudentRepo $student, MarkRepo $mark)
    {
        $this->exam =  $exam;
        $this->mark =  $mark;
        $this->student =  $student;
        $this->my_class =  $my_class;
        $this->year =  Qs::getSetting('current_session');

       // $this->middleware('teamSAT', ['except' => ['show', 'year_selected', 'year_selector', 'print_view'] ]);
    }

    public function index()
    {
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['subjects'] = $this->my_class->getAllSubjects();
        $d['selected'] = false;

        return view('pages.support_team.marks.index', $d);
    }

    public function year_selector($student_id)
    {
       return $this->verifyStudentExamYear($student_id);
    }

    public function year_selected(Request $req, $student_id)
    {
        if(!$this->verifyStudentExamYear($student_id, $req->year)){
            return $this->noStudentRecord();
        }

        $student_id = Qs::hash($student_id);
        return redirect()->route('marks.show', [$student_id, $req->year]);
    }

    public function show($student_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if(Auth::user()->id != $student_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($student_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        if(Mk::examIsLocked() && !Qs::userIsTeamSA()){
            Session::put('marks_url', route('marks.show', [Qs::hash($student_id), $year]));

            if(!$this->checkPinVerified($student_id)){
                return redirect()->route('pins.enter', Qs::hash($student_id));
            }
        }

        if(!$this->verifyStudentExamYear($student_id, $year)){
            return $this->noStudentRecord();
        }

        $wh = ['student_id' => $student_id, 'year' => $year ];
        $d['marks'] = $this->exam->getMark($wh);
        $d['exam_records'] = $exr = $this->exam->getRecord($wh);
        $d['exams'] = $this->exam->getExam(['year' => $year]);
        $d['sr'] = $this->student->getRecord(['user_id' => $student_id])->first();
        $d['my_class'] = $mc = $this->my_class->getMC(['id' => $exr->first()->my_class_id])->first();
        $d['class_type'] = $this->my_class->findTypeByClass($mc->id);
        $d['subjects'] = $this->my_class->findSubjectByClass($mc->id);
        $d['year'] = $year;
        $d['student_id'] = $student_id;
        $d['skills'] = $this->exam->getSkillByClassType() ?: NULL;
        $d['all_classes'] = $this->my_class->all(); // Ajouter toutes les classes pour le formulaire de décision
        //$d['ct'] = $d['class_type']->code;
        //$d['mark_type'] = Qs::getMarkType($d['ct']);

        // Calculate Director's Comment for show page (annual average)
        // Assuming $d['sr']->ave holds the overall annual average. This might need adjustment.
        $annual_average = $d['sr']->ave ?? 0; // Default to 0 if 'ave' is not set
        $d['director_comment'] = $this->getDirectorComment($annual_average);

        return view('pages.support_team.marks.show.index', $d);
    }

    public function print_view($student_id, $exam_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if(Auth::user()->id != $student_id && !Qs::userIsTeamSA() && !Qs::userIsMyChild($student_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        if(Mk::examIsLocked() && !Qs::userIsTeamSA()){
            Session::put('marks_url', route('marks.show', [Qs::hash($student_id), $year]));

            if(!$this->checkPinVerified($student_id)){
                return redirect()->route('pins.enter', Qs::hash($student_id));
            }
        }

        if(!$this->verifyStudentExamYear($student_id, $year)){
            return $this->noStudentRecord();
        }

        $wh = ['student_id' => $student_id, 'exam_id' => $exam_id, 'year' => $year ];
        $d['marks'] = $mks = $this->exam->getMark($wh);
        // dd($d['marks']);
        $d['exr'] = $exr = $this->exam->getRecord($wh)->first();
        $d['my_class'] = $mc = $this->my_class->find($exr->my_class_id);
        $d['section_id'] = $exr->section_id;
        $d['ex'] = $exam = $this->exam->find($exam_id);
        $d['tex'] = 'tex'.$exam->term;
        $d['sr'] = $sr =$this->student->getRecord(['user_id' => $student_id])->first();
        $d['class_type'] = $this->my_class->findTypeByClass($mc->id);
        $d['subjects'] = $this->my_class->findSubjectByClass($mc->id);

        $d['ct'] = $ct = $d['class_type']->code;
        $d['year'] = $year;
        $d['student_id'] = $student_id;
        $d['exam_id'] = $exam_id;

        $d['skills'] = $this->exam->getSkillByClassType() ?: NULL;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });

        //$d['mark_type'] = Qs::getMarkType($ct);

        // Calculate Director's Comment for print view (specific exam average)
        // Assuming $d['exr']->ave holds the average for the specific exam.
        $exam_average = $d['exr']->ave ?? 0; // Default to 0 if 'ave' is not set
        $d['director_comment'] = $this->getDirectorComment($exam_average);

        // Si c'est le 3ème trimestre, récupérer les moyennes des trimestres précédents pour calculer la moyenne annuelle
        if($exam->term == 3) {
            // Récupérer les examens du 1er et 2ème trimestre de la même année
            $term1_exam = $this->exam->getExam(['year' => $year, 'term' => 1])->first();
            $term2_exam = $this->exam->getExam(['year' => $year, 'term' => 2])->first();
            
            $d['term1_average'] = 0;
            $d['term2_average'] = 0;
            
            // Si les examens existent, récupérer les moyennes
            if($term1_exam) {
                $term1_record = $this->exam->getRecord([
                    'student_id' => $student_id, 
                    'exam_id' => $term1_exam->id, 
                    'year' => $year
                ])->first();
                $d['term1_average'] = $term1_record ? ($term1_record->ave ?? 0) : 0;
            }
            
            if($term2_exam) {
                $term2_record = $this->exam->getRecord([
                    'student_id' => $student_id, 
                    'exam_id' => $term2_exam->id, 
                    'year' => $year
                ])->first();
                $d['term2_average'] = $term2_record ? ($term2_record->ave ?? 0) : 0;
            }
            
            // Calculer la moyenne annuelle
            $d['annual_average'] = ($d['term1_average'] + $d['term2_average'] + $exam_average) / 3;
            
            // Récupérer toutes les classes pour le menu déroulant de la classe de passage
            $d['all_classes'] = $this->my_class->all();
            
            // Récupérer les informations de décision pour l'impression
            $d['rang'] = $exr; // Utiliser l'enregistrement d'examen qui contient les champs decision, next_class_id, observations
        }

        return view('pages.support_team.marks.print.index', $d);
    }
    
    public function print_multiple($student_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if(Auth::user()->id != $student_id && !Qs::userIsTeamSA() && !Qs::userIsMyChild($student_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        if(Mk::examIsLocked() && !Qs::userIsTeamSA()){
            Session::put('marks_url', route('marks.show', [Qs::hash($student_id), $year]));

            if(!$this->checkPinVerified($student_id)){
                return redirect()->route('pins.enter', Qs::hash($student_id));
            }
        }

        if(!$this->verifyStudentExamYear($student_id, $year)){
            return $this->noStudentRecord();
        }
        
        // Récupérer les IDs des examens sélectionnés depuis la requête
        $exam_ids = request()->query('exams');
        if (!$exam_ids) {
            return back()->with('flash_danger', 'Aucun examen sélectionné');
        }
        
        $exam_ids = explode(',', $exam_ids);
        
        // Vérifier que les examens existent
        $exams = $this->exam->getExam(['year' => $year])->whereIn('id', $exam_ids);
        if ($exams->count() < 1) {
            return back()->with('flash_danger', 'Examens non trouvés');
        }
        
        // Récupérer les informations de l'étudiant
        $sr = $this->student->getRecord(['user_id' => $student_id])->first();
        if (!$sr) {
            return $this->noStudentRecord();
        }
        
        // Récupérer la classe et le type de classe
        $my_class = $this->my_class->find($sr->my_class_id);
        $class_type = $this->my_class->findTypeByClass($my_class->id);
        
        // Récupérer les matières de la classe
        $subjects = $this->my_class->findSubjectByClass($my_class->id);
        
        // Préparer les données pour la vue
        $d['exams'] = $exams;
        $d['sr'] = $sr;
        $d['my_class'] = $my_class;
        $d['section_id'] = $sr->section_id;
        $d['class_type'] = $class_type;
        $d['subjects'] = $subjects;
        $d['year'] = $year;
        $d['student_id'] = $student_id;
        $d['ct'] = $class_type->code;
        
        // Récupérer les notes pour chaque examen
        $all_marks = [];
        $all_exam_records = [];
        
        foreach ($exams as $exam) {
            $wh = ['student_id' => $student_id, 'exam_id' => $exam->id, 'year' => $year];
            $marks = $this->exam->getMark($wh);
            $exam_record = $this->exam->getRecord($wh)->first();
            
            if ($marks->count() > 0 && $exam_record) {
                $all_marks[$exam->id] = $marks;
                $all_exam_records[$exam->id] = $exam_record;
            }
        }
        
        $d['all_marks'] = $all_marks;
        $d['all_exam_records'] = $all_exam_records;
        
        // Récupérer les compétences et les paramètres
        $d['skills'] = $this->exam->getSkillByClassType() ?: NULL;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });
        
        return view('pages.support_team.marks.print.multiple', $d);
    }

    public function selector(MarkSelector $req)
    {
        $data = $req->only(['exam_id', 'my_class_id', 'section_id', 'subject_id']);
        $d2 = $req->only(['exam_id', 'my_class_id', 'section_id']);
        $d = $req->only(['my_class_id', 'section_id']);
        $d['session'] = $data['year'] = $d2['year'] = $this->year;

        $students = $this->student->getRecord($d)->get();
        if($students->count() < 1){
            return back()->with('pop_error', __('msg.rnf'));
        }

        foreach ($students as $s){
            $data['student_id'] = $d2['student_id'] = $s->user_id;
            $this->exam->createMark($data);
            $this->exam->createRecord($d2);
        }

        return redirect()->route('marks.manage', [$req->exam_id, $req->my_class_id, $req->section_id, $req->subject_id]);
    }

    public function manage($exam_id, $class_id, $section_id, $subject_id)
    {
        $d = ['exam_id' => $exam_id, 'my_class_id' => $class_id, 'section_id' => $section_id, 'subject_id' => $subject_id, 'year' => $this->year];

        $d['marks'] = $this->exam->getMark($d);
        if($d['marks']->count() < 1){
            return $this->noStudentRecord();
        }

        $d['m'] =  $d['marks']->first();
        $d['exams'] = $this->exam->all();
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['subjects'] = $this->my_class->getAllSubjects();
        if(Qs::userIsTeacher()){
            $d['subjects'] = $this->my_class->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }
        $d['selected'] = true;
        $d['class_type'] = $this->my_class->findTypeByClass($class_id);


        return view('pages.support_team.marks.manage', $d);
    }

    public function update(Request $req, $exam_id, $class_id, $section_id, $subject_id)
    {

        $p = ['exam_id' => $exam_id, 'my_class_id' => $class_id, 'section_id' => $section_id, 'subject_id' => $subject_id, 'year' => $this->year];

        $d = $d3 = $all_st_ids = [];

        $exam = $this->exam->find($exam_id);
        $marks = $this->exam->getMark($p);
        $class_type = $this->my_class->findTypeByClass($class_id);

        $mks = $req->all();

        /** Test, Exam, Grade **/
        foreach($marks->sortBy('user.name') as $mk)
        {
            $all_st_ids[] = $mk->student_id;

                $d['t1'] = $t1 = $mks['t1_'.$mk->id];
                $d['t2'] = $t2 = $mks['t2_'.$mk->id];
                $d['tca'] = $tca = $t1 + $t2;
                $d['exm'] = $exm = $mks['exm_'.$mk->id];


            /** SubTotal Grade, Remark, Cum, CumAvg**/

            $subject = Subject::where('id', $subject_id)->first(); // Fetch the subject by its id
            $coef = $subject->coef; // Retrieve the coef value of the subject

            // Calculer le nombre de notes saisies
            $note_count = 0;
            $sum = 0;
            
            // Vérifier si DS1 est saisi
            if ($t1 !== null && $t1 !== '') {
                $note_count++;
                $sum += $t1;
            }
            
            // Vérifier si DS2 est saisi
            if ($t2 !== null && $t2 !== '') {
                $note_count++;
                $sum += $t2;
            }
            
            // Vérifier si l'examen est saisi
            if ($exm !== null && $exm !== '') {
                $note_count++;
                $sum += $exm;
            }
            
            // Calculer la moyenne en divisant par le nombre de notes saisies
            $total = ($note_count > 0) ? ($sum / $note_count) : 0;
            
            // Calculer la note sur 20 avant d'appliquer le coefficient (pour les remarques)
            $note_sur_20 = $total;
            
            // Appliquer le coefficient seulement s'il y a au moins une note
            if ($note_count > 0) {
                $total *= $coef; // Multiply the total by the coef
            } else {
                $total = 0; // Si aucune note, le total est 0 (sans appliquer le coefficient)
            }

            $d['tex'.$exam->term] = $total; // Store the final total
            
            // Ajouter la remarque pédagogique basée sur la note sur 20
            $d['comment'] = \App\Helpers\MarkComment::getComment($note_sur_20);

            if($total > 100){
                $d['tex'.$exam->term] = $d['t1'] = $d['t2'] = $d['t3'] = $d['t4'] = $d['tca'] = $d['exm'] = $d['comment'] = NULL;
            }

         /*   if($exam->term < 3){
                $grade = $this->mark->getGrade($total, $class_type->id);
            }

            if($exam->term == 3){
                $d['cum'] = $this->mark->getSubCumTotal($total, $st_id, $subject_id, $class_id, $this->year);
                $d['cum_ave'] = $cav = $this->mark->getSubCumAvg($total, $st_id, $subject_id, $class_id, $this->year);
                $grade = $this->mark->getGrade(round($cav), $class_type->id);
            }*/
            $grade = $this->mark->getGrade($total, $class_type->id);
            $d['grade_id'] = $grade ? $grade->id : NULL;

            $this->exam->updateMark($mk->id, $d);
        }

        /** Sub Position Begin  **/

        foreach($marks->sortBy('user.name') as $mk)
        {

            $d2['sub_pos'] = $this->mark->getSubPos($mk->student_id, $exam, $class_id, $subject_id, $this->year);

            $this->exam->updateMark($mk->id, $d2);
        }

        /*Sub Position End*/

        /* Exam Record Update */

        unset( $p['subject_id'] );

        foreach ($all_st_ids as $st_id) {

            $p['student_id'] =$st_id;
            $d3['total'] = $this->mark->getExamTotalTerm($exam, $st_id, $class_id, $this->year);
            $d3['ave'] = $this->mark->getExamAvgTerm($exam, $st_id, $class_id, $section_id, $this->year);
            $d3['class_ave'] = $this->mark->getClassAvg($exam, $class_id, $this->year);
            $d3['pos'] = $this->mark->getPos($st_id, $exam, $class_id, $section_id, $this->year);

            $this->exam->updateRecord($p, $d3);
        }
        /*Exam Record End*/

       return Qs::jsonUpdateOk();
    }

    public function batch_fix()
    {
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['selected'] = false;

        return view('pages.support_team.marks.batch_fix', $d);
    }

    public function batch_update(Request $req): \Illuminate\Http\JsonResponse
    {
        $exam_id = $req->exam_id;
        $class_id = $req->my_class_id;
        $section_id = $req->section_id;

        $w = ['exam_id' => $exam_id, 'my_class_id' => $class_id, 'section_id' => $section_id, 'year' => $this->year];

        $exam = $this->exam->find($exam_id);
        $exrs = $this->exam->getRecord($w);
        $marks = $this->exam->getMark($w);

        /** Marks Fix Begin **/

        $class_type = $this->my_class->findTypeByClass($class_id);
        $tex = 'tex'.$exam->term;

        foreach($marks as $mk){

            $total = $mk->$tex;
            $d['grade_id'] = $this->mark->getGrade($total, $class_type->id);

            /*      if($exam->term == 3){
                      $d['cum'] = $this->mark->getSubCumTotal($total, $mk->student_id, $mk->subject_id, $class_id, $this->year);
                      $d['cum_ave'] = $cav = $this->mark->getSubCumAvg($total, $mk->student_id, $mk->subject_id, $class_id, $this->year);
                      $grade = $this->mark->getGrade(round($mk->cum_ave), $class_type->id);
                  }*/

            $this->exam->updateMark($mk->id, $d);
        }

        /* Marks Fix End*/

        /** Exam Record Update  **/
        foreach($exrs as $exr){

            $st_id = $exr->student_id;

            $d3['total'] = $this->mark->getExamTotalTerm($exam, $st_id, $class_id, $this->year);
            $d3['ave'] = $this->mark->getExamAvgTerm($exam, $st_id, $class_id, $section_id, $this->year);
            $d3['class_ave'] = $this->mark->getClassAvg($exam, $class_id, $this->year);
            $d3['pos'] = $this->mark->getPos($st_id, $exam, $class_id, $section_id, $this->year);

            $this->exam->updateRecord(['id' => $exr->id], $d3);
        }

        /** END Exam Record Update END **/

        return Qs::jsonUpdateOk();
    }

    public function comment_update(Request $req, $exr_id)
    {
        $d = Qs::userIsTeamSA() ? $req->only(['t_comment', 'p_comment']) : $req->only(['t_comment']);

        $this->exam->updateRecord(['id' => $exr_id], $d);
        return Qs::jsonUpdateOk();
    }

    public function skills_update(Request $req, $skill, $exr_id)
    {
        $d = [];
        if($skill == 'AF' || $skill == 'PS'){
            $sk = strtolower($skill);
            $d[$skill] = implode(',', $req->$sk);
        }

        $this->exam->updateRecord(['id' => $exr_id], $d);
        return Qs::jsonUpdateOk();
    }

    public function bulk($class_id = NULL, $section_id = NULL, $year = NULL)
    {
        $d['my_classes'] = $this->my_class->all();
        $d['selected'] = false;

        if($class_id && $section_id){
            $d['sections'] = $this->my_class->getAllSections()->where('my_class_id', $class_id);
            $d['students'] = $st = $this->student->getRecord(['my_class_id' => $class_id, 'section_id' => $section_id])->get()->sortBy('user.name');
            if($st->count() < 1){
                return redirect()->route('marks.bulk')->with('flash_danger', __('msg.srnf'));
            }
            $d['selected'] = true;
            $d['my_class_id'] = $class_id;
            $d['section_id'] = $section_id;
            $d['year'] = $year ?: $this->year;

            // Récupérer toutes les années scolaires disponibles pour les élèves de cette classe/section
            $student_ids = $st->pluck('user_id')->toArray();
            $d['available_years'] = \App\Models\Mark::whereIn('student_id', $student_ids)
                                    ->select('year')
                                    ->distinct()
                                    ->orderBy('year', 'desc')
                                    ->pluck('year')
                                    ->toArray();
        }

        return view('pages.support_team.marks.bulk', $d);
    }

    public function bulk_select(Request $req)
    {
        return redirect()->route('marks.bulk', [$req->my_class_id, $req->section_id, $req->year]);
    }

    public function tabulation($exam_id = NULL, $class_id = NULL, $section_id = NULL)
    {
        $d['my_classes'] = $this->my_class->all();
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['selected'] = FALSE;

        if($class_id && $exam_id && $section_id){

            $wh = ['my_class_id' => $class_id, 'section_id' => $section_id, 'exam_id' => $exam_id, 'year' => $this->year];

            $sub_ids = $this->mark->getSubjectIDs($wh);
            $st_ids = $this->mark->getStudentIDs($wh);

            if(count($sub_ids) < 1 OR count($st_ids) < 1) {
                return Qs::goWithDanger('marks.tabulation', __('msg.srnf'));
            }

            $d['subjects'] = $this->my_class->getSubjectsByIDs($sub_ids);
            $d['students'] = $this->student->getRecordByUserIDs($st_ids)->get()->sortBy('user.name');
            $d['sections'] = $this->my_class->getAllSections();

            $d['selected'] = TRUE;
            $d['my_class_id'] = $class_id;
            $d['section_id'] = $section_id;
            $d['exam_id'] = $exam_id;
            $d['year'] = $this->year;
            $d['marks'] = $mks = $this->exam->getMark($wh);
            $d['exr'] = $exr = $this->exam->getRecord($wh);

            $d['my_class'] = $mc = $this->my_class->find($class_id);
            $d['section']  = $this->my_class->findSection($section_id);
            $d['ex'] = $exam = $this->exam->find($exam_id);
            $d['tex'] = 'tex'.$exam->term;
            //$d['class_type'] = $this->my_class->findTypeByClass($mc->id);
            //$d['ct'] = $ct = $d['class_type']->code;
        }

        return view('pages.support_team.marks.tabulation.index', $d);
    }

    public function print_tabulation($exam_id, $class_id, $section_id)
    {
        $wh = ['my_class_id' => $class_id, 'section_id' => $section_id, 'exam_id' => $exam_id, 'year' => $this->year];

        $sub_ids = $this->mark->getSubjectIDs($wh);
        $st_ids = $this->mark->getStudentIDs($wh);

        if(count($sub_ids) < 1 OR count($st_ids) < 1) {
            return Qs::goWithDanger('marks.tabulation', __('msg.srnf'));
        }

        $d['subjects'] = $this->my_class->getSubjectsByIDs($sub_ids);
        $d['students'] = $this->student->getRecordByUserIDs($st_ids)->get()->sortBy('user.name');

        $d['my_class_id'] = $class_id;
        $d['exam_id'] = $exam_id;
        $d['year'] = $this->year;
        $wh = ['exam_id' => $exam_id, 'my_class_id' => $class_id];
        $d['marks'] = $mks = $this->exam->getMark($wh);
        $d['exr'] = $exr = $this->exam->getRecord($wh);

        $d['my_class'] = $mc = $this->my_class->find($class_id);
        $d['section']  = $this->my_class->findSection($section_id);
        $d['ex'] = $exam = $this->exam->find($exam_id);
        $d['tex'] = 'tex'.$exam->term;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });
        //$d['class_type'] = $this->my_class->findTypeByClass($mc->id);
        //$d['ct'] = $ct = $d['class_type']->code;

        return view('pages.support_team.marks.tabulation.print', $d);
    }

    public function tabulation_select(Request $req)
    {
        return redirect()->route('marks.tabulation', [$req->exam_id, $req->my_class_id, $req->section_id]);
    }

    protected function verifyStudentExamYear($student_id, $year = null)
    {
        $years = $this->exam->getExamYears($student_id);
        $student_exists = $this->student->exists($student_id);

        if(!$year){
            if($student_exists && $years->count() > 0)
            {
                $d =['years' => $years, 'student_id' => Qs::hash($student_id)];

                return view('pages.support_team.marks.select_year', $d);
            }

            return $this->noStudentRecord();
        }

        return ($student_exists && $years->contains('year', $year)) ? true  : false;
    }

    protected function noStudentRecord()
    {
        return redirect()->route('dashboard')->with('flash_danger', __('msg.srnf'));
    }

    protected function checkPinVerified($st_id)
    {
        return Session::has('pin_verified') && Session::get('pin_verified') == $st_id;
    }

    private function getDirectorComment(float $average): string
    {
        if ($average >= 18) {
            return "Félicitations ! Un parcours remarquable.";
        }
        if ($average >= 16) {
            return "Excellente implication, continue ainsi !";
        }
        if ($average >= 14) {
            return "Bon travail, garde cette motivation.";
        }
        if ($average >= 12) {
            return "Des efforts visibles, poursuis dans cette voie.";
        }
        if ($average >= 10) {
            return "Des bases posées, à renforcer pas à pas.";
        }
        if ($average >= 8) {
            return "Continue à progresser, tu en es capable.";
        }
        if ($average >= 5) {
            return "Courage et persévérance mèneront à la réussite.";
        }
        if ($average >= 0) { // Handles 0 to 4.99
            return "Ne lâche rien, chaque effort t'aidera à avancer.";
        }
        return ""; // Default or in case of negative (should not happen)
    }

    public function save_decision(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'student_id' => 'required',
            'year' => 'required',
            'decision' => 'required|in:passant,redoublant',
            'classe_suivante' => 'nullable|string|max:100',
        ]);

        // Récupérer l'enregistrement d'examen du 3ème trimestre pour cette année
        $term3_exam = $this->exam->getExam(['term' => 3, 'year' => $validated['year']])->first();

        if (!$term3_exam) {
            return redirect()->back()->with('flash_error', 'Aucun examen du 3ème trimestre trouvé pour cette année.');
        }

        // Enregistrer la décision dans la base de données
        $examRecord = $this->exam->getRecord([
            'student_id' => $validated['student_id'],
            'exam_id' => $term3_exam->id,
            'year' => $validated['year']
        ])->first();

        if ($examRecord) {
            // Mettre à jour l'enregistrement existant
            $this->exam->updateRecord(
                ['id' => $examRecord->id],
                [
                    'decision' => $validated['decision'],
                    'next_class_id' => null, // Nous stockons le nom de la classe dans observations
                    'observations' => $validated['classe_suivante'] ? 'Classe suivante: ' . $validated['classe_suivante'] : null
                ]
            );

            return redirect()->back()->with('flash_success', 'Décision de fin d\'année enregistrée avec succès');
        } else {
            return redirect()->back()->with('flash_error', 'Enregistrement d\'examen non trouvé pour cet étudiant.');
        }
    }

}
