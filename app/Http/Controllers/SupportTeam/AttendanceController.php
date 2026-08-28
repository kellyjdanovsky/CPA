<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use App\User;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\DateHelper;

class AttendanceController extends Controller
{
    public function index()
    {
        $data['my_classes'] = MyClass::orderBy('name', 'asc')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        return view('pages.support_team.attendance.index', $data);
    }

    public function markAttendance(Request $request)
    {
        $class_id = $request->my_class_id;
        $date = $request->date;
        $period = $request->period ?? 'journee';
        $year = Qs::getCurrentSession();

        if (!$class_id || !$date) {
            return redirect()->route('attendance.index')->with('pop_error', 'Veuillez sélectionner une classe et une date.');
        }

        $my_class = MyClass::findOrFail($class_id);
        
        $data['my_class'] = $my_class;
        $data['date'] = $date;
        $data['period'] = $period;
        
        // Get all students for this class in current session
        $students = StudentRecord::with('user')->where('my_class_id', $class_id)
            ->where('session', $year)
            ->get();
            
        // Get existing attendance records
        $existing_records = Attendance::where('my_class_id', $class_id)
            ->where('date', $date)
            ->where('period', $period)
            ->where('year', $year)
            ->get()
            ->keyBy('student_id');
            
        $data['students'] = $students;
        $data['existing_records'] = $existing_records;
        
        return view('pages.support_team.attendance.mark', $data);
    }

    public function store(Request $request)
    {
        $year = Qs::getCurrentSession();
        $date = $request->date;
        $period = $request->period;
        $my_class_id = $request->my_class_id;
        $section_id = $request->section_id ?? Section::where('my_class_id', $my_class_id)->first()->id;
        
        $attendances = $request->attendance;
        $observations = $request->observations;

        if ($attendances) {
            foreach ($attendances as $student_id => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'date' => $date,
                        'period' => $period,
                        'year' => $year
                    ],
                    [
                        'my_class_id' => $my_class_id,
                        'section_id' => $section_id,
                        'status' => $status,
                        'marked_by' => Auth::user()->id,
                        'observations' => $observations[$student_id] ?? null
                    ]
                );
            }
        }

        return redirect()->back()->with('flash_success', 'Présences enregistrées avec succès.');
    }

    public function monthlyReport(Request $request)
    {
        $data['my_classes'] = MyClass::orderBy('name', 'asc')->get();
        
        $class_id = $request->my_class_id;
        $month_year = $request->month_year ?? Carbon::today()->format('Y-m');
        
        if ($class_id) {
            $year = explode('-', $month_year)[0];
            $month = explode('-', $month_year)[1];
            $session = Qs::getCurrentSession();
            
            $data['my_class'] = MyClass::findOrFail($class_id);
            $data['month_year'] = $month_year;
            
            $students = StudentRecord::with('user')->where('my_class_id', $class_id)
                ->where('session', $session)
                ->get();
                
            $attendances = Attendance::where('my_class_id', $class_id)
                ->where('year', $session)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();
                
            $days_in_month = Carbon::parse($month_year . '-01')->daysInMonth;
            
            $data['students'] = $students;
            $data['attendances'] = $attendances;
            $data['days_in_month'] = $days_in_month;
            $data['year'] = $year;
            $data['month'] = $month;
        }

        return view('pages.support_team.attendance.monthly_report', $data);
    }

    public function studentReport($hashed_id)
    {
        $student_id = Qs::decodeHash($hashed_id);
        $year = Qs::getCurrentSession();
        
        $student = User::findOrFail($student_id);
        $attendances = Attendance::where('student_id', $student_id)
            ->where('year', $year)
            ->orderBy('date', 'desc')
            ->get();
            
        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'retard' => $attendances->where('status', 'retard')->count(),
            'excuse' => $attendances->where('status', 'excuse')->count(),
            'total' => $attendances->count()
        ];
        
        $stats['taux'] = $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 2) : 0;
        
        $data['student'] = $student;
        $data['attendances'] = $attendances;
        $data['stats'] = $stats;
        
        return view('pages.support_team.attendance.student_report', $data);
    }

    public function exportMonthly(Request $request)
    {
        // Implementation for Excel export using PhpSpreadsheet can be added here
        return redirect()->back()->with('pop_warning', 'Fonctionnalité d\'export Excel en cours de développement.');
    }

    public function printMonthly(Request $request)
    {
        $class_id = $request->my_class_id;
        $month_year = $request->month_year;
        
        $year = explode('-', $month_year)[0];
        $month = explode('-', $month_year)[1];
        $session = Qs::getCurrentSession();
        
        $data['my_class'] = MyClass::findOrFail($class_id);
        $data['month_year'] = $month_year;
        
        $students = StudentRecord::with('user')->where('my_class_id', $class_id)
            ->where('session', $session)
            ->get();
            
        $attendances = Attendance::where('my_class_id', $class_id)
            ->where('year', $session)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
            
        $days_in_month = Carbon::parse($month_year . '-01')->daysInMonth;
        
        $data['students'] = $students;
        $data['attendances'] = $attendances;
        $data['days_in_month'] = $days_in_month;
        $data['year'] = $year;
        $data['month'] = $month;
        
        return view('pages.support_team.attendance.print_monthly', $data);
    }
}
