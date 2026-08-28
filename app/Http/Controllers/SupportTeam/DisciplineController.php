<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DisciplineRecord;
use App\User;
use App\Models\MyClass;
use App\Helpers\Qs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $year = Qs::getCurrentSession();
        $records = DisciplineRecord::with(['student.studentRecord.my_class', 'recorder'])
            ->where('year', $year)
            ->orderBy('date_incident', 'desc')
            ->get();
        
        $total_incidents = $records->where('type', 'incident')->where('date_incident', '>=', Carbon::now()->startOfMonth())->count();
        $total_sanctions = $records->where('type', 'sanction')->count();
        $total_recompenses = $records->where('type', 'recompense')->count();
        $eleves_concernes = $records->unique('student_id')->count();
        
        $classes = MyClass::orderBy('name')->get();

        return view('pages.support_team.discipline.index', compact(
            'records', 'total_incidents', 'total_sanctions', 'total_recompenses', 'eleves_concernes', 'classes'
        ));
    }

    public function create()
    {
        $students = User::where('user_type', 'student')->orderBy('name')->get();
        $categories = DisciplineRecord::getCategories();
        return view('pages.support_team.discipline.create', compact('students', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'type' => 'required|in:incident,sanction,recompense',
            'category' => 'required|string',
            'description' => 'required|string',
            'date_incident' => 'required|date',
            'severity' => 'nullable|in:mineur,moyen,grave,tres_grave',
            'action_taken' => 'nullable|string',
            'parent_notified' => 'nullable|boolean',
        ]);

        $validated['recorded_by'] = Auth::user()->id;
        $validated['year'] = Qs::getCurrentSession();
        $validated['parent_notified'] = $request->has('parent_notified');

        DisciplineRecord::create($validated);
        
        return Qs::goWithSuccess();
    }

    public function show($id)
    {
        $record = DisciplineRecord::with(['student', 'recorder'])->findOrFail(Qs::decodeHash($id));
        return view('pages.support_team.discipline.show', compact('record'));
    }

    public function studentHistory($student_id)
    {
        $student_id = Qs::decodeHash($student_id);
        $student = User::findOrFail($student_id);
        $records = DisciplineRecord::where('student_id', $student_id)
            ->orderBy('date_incident', 'desc')
            ->get();
            
        $total_incidents = $records->where('type', 'incident')->count();
        $total_sanctions = $records->where('type', 'sanction')->count();
        $total_recompenses = $records->where('type', 'recompense')->count();
        
        return view('pages.support_team.discipline.student_history', compact(
            'student', 'records', 'total_incidents', 'total_sanctions', 'total_recompenses'
        ));
    }

    public function classReport(Request $request)
    {
        $classes = MyClass::orderBy('name')->get();
        $records = collect();
        $stats = [];
        $top_students = collect();
        $class_id = $request->class_id;

        if ($class_id) {
            $records = DisciplineRecord::class($class_id)->where('year', Qs::getCurrentSession())->get();
            $stats = $records->where('type', 'incident')->groupBy('category')->map->count();
            $top_students = $records->where('type', 'incident')->groupBy('student_id')
                ->map(function($items) {
                    return ['student' => $items->first()->student, 'count' => $items->count()];
                })->sortByDesc('count')->take(5);
        }

        return view('pages.support_team.discipline.class_report', compact('classes', 'records', 'stats', 'top_students', 'class_id'));
    }

    public function exportExcel(Request $request)
    {
        // Dummy export for now
        return back()->with('flash_success', 'Exportation réussie');
    }

    public function printReport(Request $request)
    {
        $records = DisciplineRecord::with(['student', 'recorder'])->where('year', Qs::getCurrentSession())->get();
        return view('pages.support_team.discipline.print_report', compact('records'));
    }

    public function destroy($id)
    {
        DisciplineRecord::findOrFail(Qs::decodeHash($id))->delete();
        return back()->with('flash_success', 'Enregistrement supprimé avec succès');
    }
}
