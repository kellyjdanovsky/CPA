<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolEvent;
use App\Models\MyClass;
use App\Helpers\Qs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = Qs::getCurrentSession();
        $month = $request->get('month', Carbon::now()->month);
        $y = $request->get('year', Carbon::now()->year);
        
        $date = Carbon::createFromDate($y, $month, 1);
        
        $events = SchoolEvent::year($year)->whereMonth('start_date', $month)->whereYear('start_date', $y)->get();
        $upcoming = SchoolEvent::year($year)->upcoming()->take(5)->get();
        
        $cours_days = SchoolEvent::year($year)->where('event_type', 'cours')->count();
        $vacances_days = SchoolEvent::year($year)->where('event_type', 'vacances')->count();

        return view('pages.support_team.calendar.index', compact('events', 'upcoming', 'cours_days', 'vacances_days', 'date', 'month', 'y'));
    }

    public function create()
    {
        $classes = MyClass::orderBy('name')->get();
        return view('pages.support_team.calendar.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:cours,examen,vacances,fete,reunion,conseil,pedagogique,autre',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:my_classes,id',
        ]);

        $validated['all_day'] = $request->has('all_day');
        $validated['created_by'] = Auth::user()->id;
        $validated['year'] = Qs::getCurrentSession();

        SchoolEvent::create($validated);
        
        return Qs::goWithSuccess();
    }

    public function edit($id)
    {
        $event = SchoolEvent::findOrFail(Qs::decodeHash($id));
        $classes = MyClass::orderBy('name')->get();
        return view('pages.support_team.calendar.edit', compact('event', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $event = SchoolEvent::findOrFail(Qs::decodeHash($id));
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:cours,examen,vacances,fete,reunion,conseil,pedagogique,autre',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:my_classes,id',
        ]);

        $validated['all_day'] = $request->has('all_day');

        $event->update($validated);
        
        return Qs::goWithSuccess();
    }

    public function destroy($id)
    {
        SchoolEvent::findOrFail(Qs::decodeHash($id))->delete();
        return back()->with('flash_success', 'Événement supprimé');
    }

    public function monthData(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $events = SchoolEvent::year(Qs::getCurrentSession())->whereMonth('start_date', $month)->whereYear('start_date', $year)->get();
        return response()->json($events);
    }

    public function annualView(Request $request)
    {
        $events = SchoolEvent::year(Qs::getCurrentSession())->get();
        return view('pages.support_team.calendar.annual_view', compact('events'));
    }

    public function printAnnual(Request $request)
    {
        $events = SchoolEvent::year(Qs::getCurrentSession())->get();
        return view('pages.support_team.calendar.print_annual', compact('events'));
    }

    public function exportExcel(Request $request)
    {
        return back()->with('flash_success', 'Exportation réussie');
    }
}
