<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;

class PascomaController extends Controller
{
    protected $student;

    public function __construct(StudentRepo $student)
    {
        $this->middleware('teamSA', ['except' => []]);
        $this->student = $student;
    }

    /**
     * Display the PASCOMA table
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $d = [];
        
        // Récupérer tous les élèves inscrits sur l'année scolaire en cours
        $current_session = Qs::getSetting('current_session');
        
        // Ordre des classes pour le tri (avec et sans accents pour être sûr)
        $class_order = [
            'MS' => 1, 
            'GS' => 2, 
            'CP1' => 3, 
            'CP2' => 4, 
            'CE' => 5,
            'CM1' => 6, 
            'CM2' => 7, 
            '6ème' => 8, '6eme' => 8, '6EME' => 8,
            '5ème' => 9, '5eme' => 9, '5EME' => 9,
            '4ème' => 10, '4eme' => 10, '4EME' => 10,
            '3ème' => 11, '3eme' => 11, '3EME' => 11
        ];
        
        // Récupérer tous les élèves avec leur classe
        $students = $this->student->getAll()
            ->join('users', 'student_records.user_id', '=', 'users.id')
            ->join('my_classes', 'student_records.my_class_id', '=', 'my_classes.id')
            ->select('student_records.*', 'my_classes.name as class_name')
            ->get();

        // Trier par ordre de classe, puis par nom alphabétique
        $students = $students->sort(function($a, $b) use ($class_order) {
            // Extraire le nom de la classe (sans section)
            $class_a = trim(explode(' ', $a->class_name)[0]);
            $class_b = trim(explode(' ', $b->class_name)[0]);
            
            // Récupérer l'ordre de tri pour chaque classe
            $order_a = $class_order[$class_a] ?? 999;
            $order_b = $class_order[$class_b] ?? 999;
            
            // Si classes différentes, trier par ordre de classe
            if ($order_a != $order_b) {
                return $order_a - $order_b;
            }
            
            // Même classe : trier par nom alphabétiquement (insensible à la casse)
            return strcasecmp($a->user->name, $b->user->name);
        })->values();

        // Compteurs pour la numérotation des attestations
        // On numérote APRES le tri, donc les numéros suivront l'ordre alphabétique
        $female_count = 0;
        $male_count = 0;

        // Ajouter le numéro d'attestation pour chaque élève
        $students = $students->map(function($student) use (&$female_count, &$male_count) {
            if ($student->user->gender === 'Female') {
                $female_count++;
                $student->attestation_no = $female_count . 'F';
            } else {
                $male_count++;
                $student->attestation_no = $male_count . 'G';
            }
            
            $student->somme_payee = 200; // 200 Ar pour tous
            
            return $student;
        });

        $d['students'] = $students;
        $d['current_session'] = $current_session;

        return view('pages.support_team.pascoma.index', $d);
    }

    /**
     * Export PASCOMA data to Excel
     *
     * @return Excel download
     */
    public function export()
    {
        $current_session = Qs::getSetting('current_session');
        
        // Ordre des classes pour le tri
        $class_order = [
            'MS' => 1, 
            'GS' => 2, 
            'CP1' => 3, 
            'CP2' => 4, 
            'CE' => 5,
            'CM1' => 6, 
            'CM2' => 7, 
            '6ème' => 8, '6eme' => 8, '6EME' => 8,
            '5ème' => 9, '5eme' => 9, '5EME' => 9,
            '4ème' => 10, '4eme' => 10, '4EME' => 10,
            '3ème' => 11, '3eme' => 11, '3EME' => 11
        ];
        
        $students = $this->student->getAll()
            ->join('users', 'student_records.user_id', '=', 'users.id')
            ->join('my_classes', 'student_records.my_class_id', '=', 'my_classes.id')
            ->select('student_records.*', 'my_classes.name as class_name')
            ->get();

        // Trier par ordre de classe, puis par nom alphabétique
        $students = $students->sort(function($a, $b) use ($class_order) {
            $class_a = trim(explode(' ', $a->class_name)[0]);
            $class_b = trim(explode(' ', $b->class_name)[0]);
            
            $order_a = $class_order[$class_a] ?? 999;
            $order_b = $class_order[$class_b] ?? 999;
            
            if ($order_a != $order_b) {
                return $order_a - $order_b;
            }
            
            // Même classe : trier par nom alphabétiquement (insensible à la casse)
            return strcasecmp($a->user->name, $b->user->name);
        })->values();

        $female_count = 0;
        $male_count = 0;

        $students = $students->map(function($student) use (&$female_count, &$male_count) {
            if ($student->user->gender === 'Female') {
                $female_count++;
                $student->attestation_no = $female_count . 'F';
            } else {
                $male_count++;
                $student->attestation_no = $male_count . 'G';
            }
            
            $student->somme_payee = 200;
            
            return $student;
        });

        return \Excel::download(new \App\Exports\PascomaExport($students), 'PASCOMA_' . $current_session . '.xlsx');
    }

}
