<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\PaymentRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;

class ReenrollmentController extends Controller
{
    protected $my_class, $student, $payment;

    public function __construct(MyClassRepo $my_class, StudentRepo $student, PaymentRepo $payment)
    {
        $this->middleware('teamSA');

        $this->my_class = $my_class;
        $this->student = $student;
        $this->payment = $payment;
    }

    /**
     * Affiche la page principale de réinscription
     */
    public function index($prev_class_id = NULL, $prev_section_id = NULL, $new_class_id = NULL, $new_section_id = NULL)
    {
        $d['current_year'] = $current_year = Qs::getSetting('current_session');
        
        // Calculer l'année précédente
        $current_year_parts = explode('-', $current_year);
        $d['previous_year'] = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['selected'] = false;

        if($prev_class_id && $prev_section_id && $new_class_id && $new_section_id){
            $d['selected'] = true;
            $d['prev_class_id'] = $prev_class_id;
            $d['prev_section_id'] = $prev_section_id;
            $d['new_class_id'] = $new_class_id;
            $d['new_section_id'] = $new_section_id;
            
            // Récupérer les élèves de l'année précédente avec optimisation de requête
            $d['students'] = $this->student->getRecord([
                'my_class_id' => $prev_class_id, 
                'section_id' => $prev_section_id, 
                'session' => $d['previous_year']
            ])->with(['user', 'my_class', 'section'])->get();

            if($d['students']->count() < 1){
                return redirect()->route('students.reenrollment')->with('flash_warning', 'Aucun élève trouvé dans cette classe pour l\'année précédente.');
            }

            // Vérifier quels élèves sont déjà inscrits pour l'année en cours
            $student_ids = $d['students']->pluck('user_id')->toArray();
            $existing_students = DB::table('student_records')
                ->where('session', $current_year)
                ->whereIn('user_id', $student_ids)
                ->pluck('user_id')
                ->toArray();
            
            $d['existing_students'] = $existing_students;
        }

        return view('pages.support_team.students.reenrollment.index', $d);
    }

    /**
     * Redirection vers la page de sélection des classes
     */
    public function selector(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'prev_class_id' => 'required|exists:my_classes,id',
            'prev_section_id' => 'required|exists:sections,id',
            'new_class_id' => 'required|exists:my_classes,id',
            'new_section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('students.reenrollment')
                ->withErrors($validator)
                ->withInput();
        }

        return redirect()->route('students.reenrollment', [
            $req->prev_class_id, 
            $req->prev_section_id, 
            $req->new_class_id, 
            $req->new_section_id
        ]);
    }

    /**
     * Réinscription des élèves sélectionnés
     */
    public function reenroll(Request $req, $prev_class_id, $prev_section_id, $new_class_id, $new_section_id)
    {
        $current_year = Qs::getSetting('current_session');
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        // Récupérer les élèves de l'année précédente
        $students = $this->student->getRecord([
            'my_class_id' => $prev_class_id, 
            'section_id' => $prev_section_id, 
            'session' => $previous_year
        ])->get();

        if($students->count() < 1){
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Aucun élève trouvé.');
        }

        // Commencer une transaction pour garantir l'intégrité des données
        DB::beginTransaction();
        
        try {
            $count = 0;
            $already_enrolled = 0;
            $errors = [];
            
            foreach($students as $st){
                $checkbox_name = 'student-'.$st->id;
                
                // Vérifier si l'élève a été sélectionné pour la réinscription
                if($req->has($checkbox_name)){
                    // Vérifier si l'élève existe déjà dans l'année courante
                    $exists = $this->student->getRecord([
                        'user_id' => $st->user_id, 
                        'session' => $current_year
                    ])->exists();
                    
                    if(!$exists){
                        // Créer un nouvel enregistrement pour l'élève dans l'année courante
                        $data = [
                            'user_id' => $st->user_id,
                            'my_class_id' => $new_class_id,
                            'section_id' => $new_section_id,
                            'my_parent_id' => $st->my_parent_id,
                            'adm_no' => $st->adm_no,
                            'year_admitted' => $st->year_admitted,
                            'house' => $st->house,
                            'age' => $st->age,
                            'session' => $current_year,
                        ];
                        
                        $this->student->createRecord($data);
                        $count++;
                        
                        // Créer les enregistrements de paiement pour cet élève
                        $this->createPaymentRecords($st->user_id, $new_class_id);
                    } else {
                        $already_enrolled++;
                    }
                }
            }
            
            DB::commit();
            
            $message = '';
            if($count > 0){
                $message .= $count . ' élève(s) réinscrit(s) avec succès. ';
            }
            
            if($already_enrolled > 0){
                $message .= $already_enrolled . ' élève(s) déjà inscrit(s) pour l\'année en cours.';
            }
            
            if($count == 0 && $already_enrolled == 0){
                return redirect()->route('students.reenrollment')->with('flash_info', 'Aucun élève sélectionné pour la réinscription.');
            }
            
            return redirect()->route('students.reenrollment')->with('flash_success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Une erreur est survenue lors de la réinscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Réinscription en masse de tous les élèves d'une classe
     */
    public function reenrollAll(Request $req, $prev_class_id, $prev_section_id, $new_class_id, $new_section_id)
    {
        $current_year = Qs::getSetting('current_session');
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        // Récupérer les élèves de l'année précédente
        $students = $this->student->getRecord([
            'my_class_id' => $prev_class_id, 
            'section_id' => $prev_section_id, 
            'session' => $previous_year
        ])->get();

        if($students->count() < 1){
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Aucun élève trouvé.');
        }

        // Commencer une transaction pour garantir l'intégrité des données
        DB::beginTransaction();
        
        try {
            $count = 0;
            $already_enrolled = 0;
            
            foreach($students as $st){
                // Vérifier si l'élève existe déjà dans l'année courante
                $exists = $this->student->getRecord([
                    'user_id' => $st->user_id, 
                    'session' => $current_year
                ])->exists();
                
                if(!$exists){
                    // Créer un nouvel enregistrement pour l'élève dans l'année courante
                    $data = [
                        'user_id' => $st->user_id,
                        'my_class_id' => $new_class_id,
                        'section_id' => $new_section_id,
                        'my_parent_id' => $st->my_parent_id,
                        'adm_no' => $st->adm_no,
                        'year_admitted' => $st->year_admitted,
                        'house' => $st->house,
                        'age' => $st->age,
                        'session' => $current_year,
                    ];
                    
                    $this->student->createRecord($data);
                    $count++;
                    
                    // Créer les enregistrements de paiement pour cet élève
                    $this->createPaymentRecords($st->user_id, $new_class_id);
                } else {
                    $already_enrolled++;
                }
            }
            
            DB::commit();
            
            $message = '';
            if($count > 0){
                $message .= $count . ' élève(s) réinscrit(s) avec succès. ';
            }
            
            if($already_enrolled > 0){
                $message .= $already_enrolled . ' élève(s) déjà inscrit(s) pour l\'année en cours.';
            }
            
            return redirect()->route('students.reenrollment')->with('flash_success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Une erreur est survenue lors de la réinscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Création des enregistrements de paiement pour un élève
     */
    protected function createPaymentRecords($student_id, $class_id)
    {
        $current_year = Qs::getSetting('current_session');
        
        // Récupérer les paiements pour cette classe et les paiements généraux
        $pay1 = $this->payment->getPayment(['my_class_id' => $class_id, 'year' => $current_year])->get();
        $pay2 = $this->payment->getGeneralPayment(['year' => $current_year])->get();
        $payments = $pay2->count() ? $pay1->merge($pay2) : $pay1;
        
        if($payments->count()){
            $payment_records = [];
            
            foreach($payments as $p){
                $payment_records[] = [
                    'student_id' => $student_id,
                    'payment_id' => $p->id,
                    'year' => $current_year,
                    'ref_no' => mt_rand(100000, 99999999),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Insertion en masse pour optimiser les performances
            if(count($payment_records) > 0){
                DB::table('payment_records')->insert($payment_records);
            }
        }
    }
    
    /**
     * Recherche avancée d'élèves
     */
    public function search(Request $request)
    {
        $current_year = Qs::getSetting('current_session');
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        $search_term = $request->search_term;
        $class_id = $request->class_id;
        
        if(empty($search_term) && empty($class_id)){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Veuillez entrer un terme de recherche ou sélectionner une classe.');
        }
        
        // Rechercher les élèves par nom et/ou classe dans l'année précédente
        $query = DB::table('student_records as sr')
            ->join('users as u', 'sr.user_id', '=', 'u.id')
            ->join('my_classes as mc', 'sr.my_class_id', '=', 'mc.id')
            ->join('sections as s', 'sr.section_id', '=', 's.id')
            ->select('sr.*', 'u.name as student_name', 'u.photo', 'mc.name as class_name', 's.name as section_name')
            ->where('sr.session', $previous_year);
            
        if(!empty($search_term)){
            $query->where('u.name', 'like', '%'.$search_term.'%');
        }
        
        if(!empty($class_id)){
            $query->where('sr.my_class_id', $class_id);
        }
        
        $students = $query->get();
        
        if($students->count() < 1){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Aucun élève trouvé avec ces critères de recherche.');
        }
        
        // Vérifier quels élèves sont déjà inscrits pour l'année en cours
        $student_ids = $students->pluck('user_id')->toArray();
        $existing_students = DB::table('student_records')
            ->where('session', $current_year)
            ->whereIn('user_id', $student_ids)
            ->pluck('user_id')
            ->toArray();
        
        $d['current_year'] = $current_year;
        $d['previous_year'] = $previous_year;
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['students'] = $students;
        $d['search_term'] = $search_term;
        $d['class_id'] = $class_id;
        $d['existing_students'] = $existing_students;
        
        return view('pages.support_team.students.reenrollment.search_results', $d);
    }
    
    /**
     * Réinscription d'un élève individuel
     */
    public function reenrollStudent(Request $request, $student_id)
    {
        $validator = Validator::make($request->all(), [
            'my_class_id' => 'required|exists:my_classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $current_year = Qs::getSetting('current_session');
        
        // Vérifier si l'élève existe déjà dans l'année courante
        $exists = $this->student->getRecord([
            'user_id' => $student_id, 
            'session' => $current_year
        ])->exists();
        
        if($exists){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Cet élève est déjà inscrit pour l\'année en cours.');
        }
        
        // Récupérer les informations de l'élève de l'année précédente
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        $student_record = $this->student->getRecord([
            'user_id' => $student_id, 
            'session' => $previous_year
        ])->first();
        
        if(!$student_record){
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Enregistrement d\'élève non trouvé.');
        }
        
        // Commencer une transaction
        DB::beginTransaction();
        
        try {
            // Créer un nouvel enregistrement pour l'élève dans l'année courante
            $data = [
                'user_id' => $student_id,
                'my_class_id' => $request->my_class_id,
                'section_id' => $request->section_id,
                'my_parent_id' => $student_record->my_parent_id,
                'adm_no' => $student_record->adm_no,
                'year_admitted' => $student_record->year_admitted,
                'house' => $student_record->house,
                'age' => $student_record->age,
                'session' => $current_year,
            ];
            
            $this->student->createRecord($data);
            
            // Créer les enregistrements de paiement pour cet élève
            $this->createPaymentRecords($student_id, $request->my_class_id);
            
            DB::commit();
            
            return redirect()->route('students.reenrollment')->with('flash_success', 'Élève réinscrit avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Une erreur est survenue lors de la réinscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Réinscription en masse d'élèves sélectionnés via la recherche
     */
    public function batchReenroll(Request $request)
    {
        $selected_students = json_decode($request->selected_students, true);
        
        if(empty($selected_students) || !is_array($selected_students)){
            return redirect()->back()->with('flash_warning', 'Aucun élève sélectionné pour la réinscription.');
        }
        
        $d['student_ids'] = $selected_students;
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        
        return view('pages.support_team.students.reenrollment.batch_reenroll', $d);
    }
    
    /**
     * Traitement de la réinscription en masse
     */
    public function batchReenrollSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required',
            'class_id' => 'required|exists:my_classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $student_ids = json_decode($request->student_ids, true);
        
        if(empty($student_ids) || !is_array($student_ids)){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Aucun élève sélectionné pour la réinscription.');
        }
        
        $current_year = Qs::getSetting('current_session');
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        // Commencer une transaction
        DB::beginTransaction();
        
        try {
            $count = 0;
            $already_enrolled = 0;
            
            foreach($student_ids as $student_id){
                // Vérifier si l'élève existe déjà dans l'année courante
                $exists = $this->student->getRecord([
                    'user_id' => $student_id, 
                    'session' => $current_year
                ])->exists();
                
                if(!$exists){
                    // Récupérer les informations de l'élève de l'année précédente
                    $student_record = $this->student->getRecord([
                        'user_id' => $student_id, 
                        'session' => $previous_year
                    ])->first();
                    
                    if($student_record){
                        // Créer un nouvel enregistrement pour l'élève dans l'année courante
                        $data = [
                            'user_id' => $student_id,
                            'my_class_id' => $request->class_id,
                            'section_id' => $request->section_id,
                            'my_parent_id' => $student_record->my_parent_id,
                            'adm_no' => $student_record->adm_no,
                            'year_admitted' => $student_record->year_admitted,
                            'house' => $student_record->house,
                            'age' => $student_record->age,
                            'session' => $current_year,
                        ];
                        
                        $this->student->createRecord($data);
                        $count++;
                        
                        // Créer les enregistrements de paiement pour cet élève
                        $this->createPaymentRecords($student_id, $request->class_id);
                    }
                } else {
                    $already_enrolled++;
                }
            }
            
            DB::commit();
            
            $message = '';
            if($count > 0){
                $message .= $count . ' élève(s) réinscrit(s) avec succès. ';
            }
            
            if($already_enrolled > 0){
                $message .= $already_enrolled . ' élève(s) déjà inscrit(s) pour l\'année en cours.';
            }
            
            return redirect()->route('students.reenrollment')->with('flash_success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Une erreur est survenue lors de la réinscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Exporter la liste des élèves à réinscrire au format Excel
     */
    public function exportStudents(Request $request, $prev_class_id, $prev_section_id)
    {
        $current_year = Qs::getSetting('current_session');
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        // Récupérer les élèves de l'année précédente
        $students = $this->student->getRecord([
            'my_class_id' => $prev_class_id, 
            'section_id' => $prev_section_id, 
            'session' => $previous_year
        ])->with(['user', 'my_class', 'section'])->get();
        
        if($students->count() < 1){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Aucun élève trouvé pour l\'exportation.');
        }
        
        $class_name = $students->first()->my_class->name ?? 'Classe';
        $section_name = $students->first()->section->name ?? 'Section';
        $filename = 'eleves_a_reinscrire_' . $class_name . '_' . $section_name . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new StudentsExport($students), $filename);
    }
    
    /**
     * Afficher le formulaire d'importation d'élèves
     */
    public function importForm()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        
        return view('pages.support_team.students.reenrollment.import', $d);
    }
    
    /**
     * Importer des élèves à partir d'un fichier Excel
     */
    public function importStudents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'my_class_id' => 'required|exists:my_classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            $import = new StudentsImport($request->my_class_id, $request->section_id);
            Excel::import($import, $request->file('file'));
            
            $count = $import->getRowCount();
            
            return redirect()->route('students.reenrollment')->with('flash_success', $count . ' élève(s) importé(s) et réinscrit(s) avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('students.reenrollment')->with('flash_danger', 'Une erreur est survenue lors de l\'importation: ' . $e->getMessage());
        }
    }
}
