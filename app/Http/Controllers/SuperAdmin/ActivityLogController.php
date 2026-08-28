<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\User;
use Illuminate\Http\Request;
use App\Helpers\Qs;
use Carbon\Carbon;
use App\Services\UniversalExportService;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->user($request->user_id);
        }
        if ($request->filled('module')) {
            $query->module($request->module);
        }
        if ($request->filled('action')) {
            $query->action($request->action);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->period($start, $end);
        }

        $logs = $query->get();
        $users = User::orderBy('name', 'asc')->get();
        
        $modules = ActivityLog::select('module')->distinct()->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('pages.super_admin.activity_logs.index', compact('logs', 'users', 'modules', 'actions'));
    }

    public function show($id)
    {
        $id = Qs::decodeHash($id);
        $log = ActivityLog::with('user')->findOrFail($id);

        return view('pages.super_admin.activity_logs.show', compact('log'));
    }

    public function exportExcel(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->user($request->user_id);
        }
        if ($request->filled('module')) {
            $query->module($request->module);
        }
        if ($request->filled('action')) {
            $query->action($request->action);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->period($start, $end);
        }

        $logs = $query->get();

        $data = $logs->map(function($log) {
            return [
                'Date' => $log->created_at->format('d/m/Y H:i:s'),
                'Utilisateur' => $log->user ? $log->user->name : 'Système',
                'Module' => $log->module,
                'Action' => $log->action,
                'Description' => $log->description,
                'Adresse IP' => $log->ip_address,
            ];
        })->toArray();

        $headers = ['Date', 'Utilisateur', 'Module', 'Action', 'Description', 'Adresse IP'];

        return UniversalExportService::exportCsv($data, $headers, 'journal_activites.csv');
    }

    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        $date = Carbon::now()->subDays($days);
        
        ActivityLog::where('created_at', '<', $date)->delete();
        
        return Qs::goWithSuccess();
    }
}
