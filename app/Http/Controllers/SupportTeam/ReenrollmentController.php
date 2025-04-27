<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\PaymentRepo;
use Illuminate\Http\Request;

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
            
            // Récupérer les élèves de l'année précédente
            $d['students'] = $this->student->getRecord([
                'my_class_id' => $prev_class_id, 
                'section_id' => $prev_section_id, 
                'session' => $d['previous_year']
            ])->get();

            if($d['students']->count() < 1){
                return redirect()->route('students.reenrollment')->with('flash_warning', 'Aucun élève trouvé dans cette classe pour l\'année précédente.');
            }
        }

        return view('pages.support_team.students.reenrollment.index', $d);
    }

    public function selector(Request $req)
    {
        return redirect()->route('students.reenrollment', [
            $req->prev_class_id, 
            $req->prev_section_id, 
            $req->new_class_id, 
            $req->new_section_id
        ]);
    }

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

        $count = 0;
        foreach($students as $st){
            $checkbox_name = 'student-'.$st->id;
            
            // Vérifier si l'élève a été sélectionné pour la réinscription
            if($req->has($checkbox_name)){
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
                
                // Vérifier si l'élève existe déjà dans l'année courante
                $exists = $this->student->getRecord([
                    'user_id' => $st->user_id, 
                    'session' => $current_year
                ])->exists();
                
                if(!$exists){
                    $this->student->createRecord($data);
                    $count++;
                    
                    // Créer les enregistrements de paiement pour cet élève
                    $this->createPaymentRecords($st->user_id, $new_class_id);
                }
            }
        }

        if($count > 0){
            return redirect()->route('students.reenrollment')->with('flash_success', $count . ' élève(s) réinscrit(s) avec succès.');
        }
        
        return redirect()->route('students.reenrollment')->with('flash_info', 'Aucun élève sélectionné pour la réinscription.');
    }
    
    protected function createPaymentRecords($student_id, $class_id)
    {
        $current_year = Qs::getSetting('current_session');
        
        // Récupérer les paiements pour cette classe et les paiements généraux
        $pay1 = $this->payment->getPayment(['my_class_id' => $class_id, 'year' => $current_year])->get();
        $pay2 = $this->payment->getGeneralPayment(['year' => $current_year])->get();
        $payments = $pay2->count() ? $pay1->merge($pay2) : $pay1;
        
        if($payments->count()){
            foreach($payments as $p){
                $pr['student_id'] = $student_id;
                $pr['payment_id'] = $p->id;
                $pr['year'] = $current_year;
                $rec = $this->payment->createRecord($pr);
                if(!$rec->ref_no){
                    $rec->update(['ref_no' => mt_rand(100000, 99999999)]);
                }
            }
        }
    }
    
    public function search(Request $request)
    {
        $current_year = Qs::getSetting('current_session');
        $current_year_parts = explode('-', $current_year);
        $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
        
        $search_term = $request->search_term;
        
        if(empty($search_term)){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Veuillez entrer un terme de recherche.');
        }
        
        // Rechercher les élèves par nom dans l'année précédente
        $students = $this->student->searchStudentsByName($search_term, $previous_year);
        
        if($students->count() < 1){
            return redirect()->route('students.reenrollment')->with('flash_warning', 'Aucun élève trouvé avec ce terme de recherche.');
        }
        
        $d['current_year'] = $current_year;
        $d['previous_year'] = $previous_year;
        $d['my_classes'] = $this->my_class->all();
        $d['sections'] = $this->my_class->getAllSections();
        $d['students'] = $students;
        $d['search_term'] = $search_term;
        
        return view('pages.support_team.students.reenrollment.search_results', $d);
    }
    
    public function reenrollStudent(Request $request, $student_id)
    {
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
        
        return redirect()->route('students.reenrollment')->with('flash_success', 'Élève réinscrit avec succès.');
    }
}
