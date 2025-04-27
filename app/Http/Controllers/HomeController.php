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
        $d=[];
        if(Qs::userIsTeamSAT()){
            $d['users'] = $this->user->getAll();

            // Get all classes
            $d['classes'] = $this->my_class->all();

            // Get student count for each class
            $d['class_student_counts'] = [];
            $total_active_students = 0;

            foreach($d['classes'] as $class) {
                $class_count = $this->student->findStudentsByClass($class->id)->count();
                $d['class_student_counts'][$class->id] = $class_count;
                $total_active_students += $class_count;
            }

            // Store the total active students count
            $d['total_active_students'] = $total_active_students;
        }

        return view('pages.support_team.dashboard', $d);
    }
}
