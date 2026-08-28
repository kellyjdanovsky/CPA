<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;

class StudentRecordController extends Controller
{
    protected $loc, $my_class, $user, $student;

   public function __construct(LocationRepo $loc, MyClassRepo $my_class, UserRepo $user, StudentRepo $student)
   {
       $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
       $this->middleware('super_admin', ['only' => ['destroy',] ]);

       $this->loc = $loc;
       $this->my_class = $my_class;
       $this->user = $user;
       $this->student = $student;
   }

    public function index()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = $this->student->getAll();

        return view('pages.support_team.students.index', $data);
    }

    public function reset_pass($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        $data['password'] = Hash::make('student');
        $this->user->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function create()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        return view('pages.support_team.students.add', $data);
    }

    public function store(StudentRecordCreate $req)
    {
       $data =  $req->only(Qs::getUserRecord());
       $sr =  $req->only(Qs::getStudentData());

       $ct = $this->my_class->findTypeByClass($req->my_class_id)->code;
      /* $ct = ($ct == 'J') ? 'JSS' : $ct;
       $ct = ($ct == 'S') ? 'SS' : $ct;*/

       $data['user_type'] = 'student';
       $data['name'] = ucwords($req->name);
       $data['code'] = strtoupper(Str::random(10));
       $data['password'] = Hash::make('student');
       $data['photo'] = Qs::getDefaultUserImage();
       $data['status'] = $req->status ?? 'Normal';
       $data['student_type'] = $req->student_type ?? 'Nouveau';
       $data['academic_status'] = $req->academic_status ?? 'Passant';
       $data['religion'] = $req->religion;
       $data['nom_p'] = $req->nom_p;
       $data['prof_p'] = $req->prof_p;
       $data['nom_m'] = $req->nom_m;
       $data['prof_m'] = $req->prof_m;
       $adm_no = $req->adm_no;
       $data['username'] = strtoupper(Qs::getAppCode().'/'.$ct.'/'.$sr['year_admitted'].'/'.($adm_no ?: mt_rand(1000, 99999)));

       if($req->hasFile('photo')) {
           $photo = $req->file('photo');
           $f = Qs::getFileMetaData($photo);
           $f['name'] = 'photo.' . $f['ext'];
           $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$data['code'], $f['name']);
           $data['photo'] = asset('storage/' . $f['path']);
       }

       $user = $this->user->create($data); // Create User

       $sr['adm_no'] = $data['username'];
       $sr['user_id'] = $user->id;
       $sr['session'] = Qs::getSetting('current_session');
       $sr['age'] = $req->age ?: Qs::calculateAge($req->dob);

       $this->student->createRecord($sr); // Create Student
       
       // Auto-Assign Payments
       $paymentService = new \App\Services\PaymentAutoAssignService();
       $paymentService->assignClassPaymentsToStudent($user->id, $req->my_class_id, $sr['session'], $data['student_type'], $data['status']);
       
       return Qs::jsonStoreOk();
    }

    public function listByClass($class_id)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);
        $data['sections'] = $this->my_class->getClassSections($class_id);
        $data['all_students'] = $this->student->getAllSorted()->get();
        $data['my_classes'] = $this->my_class->all();

        return is_null($mc) ? Qs::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    /**
     * Imprimer la feuille d'appel / registre de présence mensuel en A4 paysage
     */
    public function printAttendanceSheet($class_id, $section_id = null)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        if (!$mc) {
            return Qs::goWithDanger();
        }

        if ($section_id) {
            $data['section'] = $this->my_class->findSection($section_id);
            $data['students'] = $this->student->getRecord(['my_class_id' => $class_id, 'section_id' => $section_id])->get();
        } else {
            $data['students'] = $this->student->findStudentsByClass($class_id);
        }

        return view('pages.support_team.students.print_attendance_sheet', $data);
    }

    /**
     * Imprimer les cartes scolaires par classe (8 cartes / page A4)
     */
    public function printIdCards($class_id, $section_id = null)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        if (!$mc) {
            return Qs::goWithDanger();
        }

        if ($section_id) {
            $data['section'] = $this->my_class->findSection($section_id);
            $data['students'] = $this->student->getRecord(['my_class_id' => $class_id, 'section_id' => $section_id])->get();
        } else {
            $data['students'] = $this->student->findStudentsByClass($class_id);
        }

        return view('pages.support_team.students.id_cards', $data);
    }

    public function listAll()
    {
        $data['all_students'] = $this->student->getAllSorted()->get();
        $data['my_classes'] = $this->my_class->all();

        // Récupérer des statistiques pour le nouvel onglet
        $data['total_students'] = $data['all_students']->count();
        $data['students_by_class'] = [];
        $data['students_by_status'] = [
            'Normal' => $data['all_students']->where('user.status', 'Normal')->count() + $data['all_students']->whereNull('user.status')->count(),
            'ADRA' => $data['all_students']->where('user.status', 'ADRA')->count(),
            'TEAM3' => $data['all_students']->where('user.status', 'TEAM3')->count(),
        ];

        // S'assurer que les valeurs ne sont jamais nulles
        $data['total_students'] = max(0, $data['total_students']);
        foreach ($data['students_by_status'] as $key => $value) {
            $data['students_by_status'][$key] = max(0, $value);
        }

        // Compter les élèves par classe et construire la matrice par classe
        $data['class_matrix'] = [];
        if ($data['my_classes'] && $data['my_classes']->count() > 0) {
            foreach($data['my_classes'] as $class) {
                $cStudents = $data['all_students']->where('my_class_id', $class->id);
                $count = $cStudents->count();
                $cBoys = $cStudents->where('user.gender', 'Male')->count();
                $cGirls = $cStudents->where('user.gender', 'Female')->count();
                
                $cAgeSum = 0; $cAgeCount = 0;
                foreach ($cStudents as $cs) {
                    if ($cs->user && $cs->user->dob) {
                        $cAgeSum += Qs::calculateAge($cs->user->dob);
                        $cAgeCount++;
                    }
                }

                $data['students_by_class'][] = [
                    'name' => $class->name ?? 'Classe sans nom',
                    'count' => max(0, $count)
                ];

                $data['class_matrix'][] = [
                    'id' => $class->id,
                    'name' => $class->name ?? 'Classe sans nom',
                    'total' => $count,
                    'boys' => $cBoys,
                    'girls' => $cGirls,
                    'avg_age' => $cAgeCount > 0 ? round($cAgeSum / $cAgeCount, 1) : '-',
                    'normal' => $cStudents->filter(fn($s) => $s->user && ($s->user->status === 'Normal' || is_null($s->user->status)))->count(),
                    'adra' => $cStudents->filter(fn($s) => $s->user && $s->user->status === 'ADRA')->count(),
                    'team3' => $cStudents->filter(fn($s) => $s->user && ($s->user->status === 'TEAM3' || $s->user->status === 'Team3'))->count(),
                    'nouveau' => $cStudents->filter(fn($s) => $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type)))->count(),
                    'ancien' => $cStudents->filter(fn($s) => $s->user && $s->user->student_type === 'Ancien')->count(),
                    'passant' => $cStudents->filter(fn($s) => $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status)))->count(),
                    'redoublant' => $cStudents->filter(fn($s) => $s->user && $s->user->academic_status === 'Redoublant')->count(),
                    'adventiste' => $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'Adventiste')->count(),
                    'catholique' => $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'Catholique')->count(),
                    'fjkm' => $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'FJKM')->count(),
                    'autres_rel' => $count - $cStudents->filter(fn($s) => $s->user && in_array($s->user->religion, ['Adventiste', 'Catholique', 'FJKM']))->count()
                ];
            }
        }

        // Statistiques par religion
        $religions = ['Adventiste', 'Catholique', 'FJKM', 'FLM', 'Islam', 'Judaïsme', 'Apokalipsy', 'Autres', 'Non renseigné'];
        $data['students_by_religion'] = [];
        foreach ($religions as $rel) {
            if ($rel === 'Non renseigné') {
                $rCount = $data['all_students']->filter(fn($s) => !$s->user || is_null($s->user->religion))->count();
            } else {
                $rCount = $data['all_students']->filter(fn($s) => $s->user && $s->user->religion === $rel)->count();
            }
            $data['students_by_religion'][$rel] = max(0, $rCount);
        }

        // Totaux globaux détaillés
        $data['total_boys'] = $data['all_students']->where('user.gender', 'Male')->count();
        $data['total_girls'] = $data['all_students']->where('user.gender', 'Female')->count();
        $data['total_nouveaux'] = $data['all_students']->filter(fn($s) => $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type)))->count();
        $data['total_anciens'] = $data['all_students']->filter(fn($s) => $s->user && $s->user->student_type === 'Ancien')->count();
        $data['total_passants'] = $data['all_students']->filter(fn($s) => $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status)))->count();
        $data['total_redoublants'] = $data['all_students']->filter(fn($s) => $s->user && $s->user->academic_status === 'Redoublant')->count();
        
        $totAge = 0; $totAgeCount = 0;
        foreach($data['all_students'] as $st) {
            if ($st->user && $st->user->dob) {
                $totAge += Qs::calculateAge($st->user->dob);
                $totAgeCount++;
            }
        }
        $data['avg_school_age'] = $totAgeCount > 0 ? round($totAge / $totAgeCount, 1) : '-';

        return view('pages.support_team.students.list_all', $data);
    }

    public function graduated()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = $this->student->allGradStudents();

        return view('pages.support_team.students.graduated', $data);
    }

    public function not_graduated($sr_id)
    {
        $d['grad'] = 0;
        $d['grad_date'] = NULL;
        $d['session'] = Qs::getSetting('current_session');
        $this->student->updateRecord($sr_id, $d);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data['sr']->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data['sr']->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.students.show', $data);
    }

    public function edit($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $req, $sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $d =  $req->only(Qs::getUserRecord());
        $d['name'] = ucwords($req->name);
        $d['status'] = $req->status;
        $d['religion'] = $req->religion;
        $d['nom_p'] = $req->nom_p;
        $d['prof_p'] = $req->prof_p;
        $d['nom_m'] = $req->nom_m;
        $d['prof_m'] = $req->prof_m;

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$sr->user->code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d); // Update User Details

        $srec = $req->only(Qs::getStudentData());
        $srec['age'] = $req->age ?: Qs::calculateAge($req->dob);

        $this->student->updateRecord($sr_id, $srec); // Update St Rec

        /*** If Class/Section is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Section ****/
        Mk::deleteOldRecord($sr->user->id, $srec['my_class_id']);

        return Qs::jsonUpdateOk();
    }

    public function destroy($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        if(!$st_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        $path = Qs::getUploadPath('student').$sr->user->code;
        Storage::exists($path) ? Storage::deleteDirectory($path) : false;
        $this->user->delete($sr->user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /**
     * Exporter les étudiants en Excel avec colonnes visibles
     */
    public function export(Request $request)
    {
        // Récupérer les colonnes visibles depuis la requête
        $columns = $request->input('columns', []);
        
        // Si aucune colonne n'est spécifiée, exporter toutes les colonnes par défaut
        if (empty($columns)) {
            $columns = ['name', 'adm_no', 'my_class_name', 'section_name', 'dob', 'age', 'address', 'religion', 'status', 'student_type', 'academic_status', 'gender', 'nom_p', 'prof_p', 'nom_m', 'prof_m', 'phone'];
        } else {
            // Si les colonnes sont envoyées en tant que chaîne JSON, la décoder
            if (is_string($columns)) {
                $columns = json_decode($columns, true);
            }
        }
        
        // Récupérer tous les étudiants
        $students = $this->student->getAll()->get();
        
        // Préparer les données pour l'export
        $exportData = [];
        
        // En-tête avec les colonnes visibles
        $headers = [];
        foreach ($columns as $column) {
            switch ($column) {
                case 'name':
                    $headers[] = 'Nom';
                    break;
                case 'adm_no':
                    $headers[] = 'N° d\'admission';
                    break;
                case 'my_class_name':
                    $headers[] = 'Classe';
                    break;
                case 'section_name':
                    $headers[] = 'Section';
                    break;
                case 'dob':
                    $headers[] = 'Date de naissance';
                    break;
                case 'age':
                    $headers[] = 'Âge';
                    break;
                case 'address':
                    $headers[] = 'Adresse';
                    break;
                case 'religion':
                    $headers[] = 'Religion';
                    break;
                case 'status':
                    $headers[] = 'Statut';
                    break;
                case 'student_type':
                    $headers[] = 'Type';
                    break;
                case 'academic_status':
                    $headers[] = 'Statut académique';
                    break;
                case 'gender':
                    $headers[] = 'Sexe';
                    break;
                case 'nom_p':
                    $headers[] = 'Père/Tuteur';
                    break;
                case 'prof_p':
                    $headers[] = 'Profession père';
                    break;
                case 'nom_m':
                    $headers[] = 'Mère/Tutrice';
                    break;
                case 'prof_m':
                    $headers[] = 'Profession mère';
                    break;
                case 'phone':
                    $headers[] = 'Téléphone';
                    break;
            }
        }
        
        $exportData[] = $headers;
        
        // Données des étudiants
        foreach ($students as $student) {
            $row = [];
            foreach ($columns as $column) {
                switch ($column) {
                    case 'name':
                        $row[] = $student->user->name;
                        break;
                    case 'adm_no':
                        $row[] = $student->adm_no;
                        break;
                    case 'my_class_name':
                        $row[] = $student->my_class->name;
                        break;
                    case 'section_name':
                        $row[] = $student->section->name;
                        break;
                    case 'dob':
                        $row[] = $student->user->dob;
                        break;
                    case 'age':
                        $row[] = $student->user->dob ? \App\Helpers\Qs::calculateAge($student->user->dob) : '-';
                        break;
                    case 'address':
                        $row[] = $student->user->address;
                        break;
                    case 'religion':
                        $row[] = $student->user->religion;
                        break;
                    case 'status':
                        $row[] = $student->user->status ?? 'Normal';
                        break;
                    case 'student_type':
                        $row[] = $student->user->student_type ?? 'Nouveau';
                        break;
                    case 'academic_status':
                        $row[] = $student->user->academic_status ?? 'Passant';
                        break;
                    case 'gender':
                        $row[] = $student->user->gender == 'Male' ? 'Masculin' : ($student->user->gender == 'Female' ? 'Féminin' : '-');
                        break;
                    case 'nom_p':
                        $row[] = $student->user->nom_p;
                        break;
                    case 'prof_p':
                        $row[] = $student->user->prof_p;
                        break;
                    case 'nom_m':
                        $row[] = $student->user->nom_m;
                        break;
                    case 'prof_m':
                        $row[] = $student->user->prof_m;
                        break;
                    case 'phone':
                        $row[] = $student->user->phone;
                        break;
                }
            }
            $exportData[] = $row;
        }
        
        // Créer le contenu CSV
        $csvContent = '';
        foreach ($exportData as $row) {
            $csvContent .= '"' . implode('","', $row) . '"' . "\n";
        }
        
        // Nom du fichier
        $filename = 'export_eleves_' . date('Y-m-d_His') . '.csv';
        
        // Retourner le fichier CSV
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;
        $decodedIds = array_map(function($id) { return Qs::decodeHash($id); }, $ids);

        switch ($action) {
            case 'delete':
                if(!Qs::userIsSuperAdmin()){
                    return response()->json(['message' => 'Non autorisé.'], 403);
                }
                foreach ($decodedIds as $id) {
                    $this->student->delete($id);
                }
                break;
            case 'change_status':
                $status = $request->status;
                foreach ($decodedIds as $id) {
                    $st = $this->student->find($id);
                    if($st && $st->user) {
                        $st->user->update(['status' => $status]);
                    }
                }
                break;
            case 'promote':
                return response()->json(['message' => 'La promotion en masse nécessite une interface dédiée.'], 400);
                break;
        }

        return response()->json(['success' => true]);
    }
}
