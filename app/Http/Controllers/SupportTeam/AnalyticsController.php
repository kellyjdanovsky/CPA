<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Models\MyClass;
use App\Models\Payment;
use App\Models\PaymentRecord;
use App\Models\StudentRecord;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Cache pour 30 minutes
        $stats = Cache::remember('analytics_stats', 30 * 60, function () {
            $current_year = \App\Helpers\Qs::getCurrentSession();
            
            return [
                'students_stats' => [
                    'total' => StudentRecord::activeStudents()->count(),
                    'by_gender' => User::where('user_type', 'student')
                        ->select('gender', DB::raw('count(*) as total'))
                        ->groupBy('gender')
                        ->pluck('total', 'gender')->toArray(),
                    'by_class' => StudentRecord::activeStudents()
                        ->join('my_classes', 'student_records.my_class_id', '=', 'my_classes.id')
                        ->select('my_classes.name', DB::raw('count(*) as total'))
                        ->groupBy('my_classes.name')
                        ->pluck('total', 'name')->toArray(),
                ],
                'status_stats' => User::where('user_type', 'student')
                    ->select('status', DB::raw('count(*) as total'))
                    ->whereIn('status', ['Normal', 'ADRA', 'TEAM3'])
                    ->groupBy('status')
                    ->pluck('total', 'status')->toArray(),
                'total_collected' => PaymentRecord::where('year', $current_year)->sum('amt_paid'),
            ];
        });

        // Les transactions récentes doivent être en temps réel
        $current_year = \App\Helpers\Qs::getCurrentSession();
        $recent_transactions = PaymentRecord::where('year', $current_year)
            ->with(['student', 'payment'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $students_stats = $stats['students_stats'];
        $status_stats = $stats['status_stats'];
        
        // Compléter les données manquantes
        $status_stats['Normal'] = $status_stats['Normal'] ?? 0;
        $status_stats['ADRA'] = $status_stats['ADRA'] ?? 0;
        $status_stats['TEAM3'] = $status_stats['TEAM3'] ?? 0;

        $payments_stats = [
            'total_expected' => 0,
            'total_collected' => $stats['total_collected'],
            'recent_transactions' => $recent_transactions
        ];

        return view('pages.support_team.analytics.index', compact('students_stats', 'payments_stats', 'status_stats'));
    }
}
