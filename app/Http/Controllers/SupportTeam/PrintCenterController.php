<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyClass;
use App\Models\Exam;
use App\User;

class PrintCenterController extends Controller
{
    public function index()
    {
        // Fetch necessary data for quick selectors
        $my_classes = MyClass::orderBy('name', 'asc')->get();
        $exams = Exam::orderBy('name', 'asc')->get();
        $years = [];
        
        $current_year = date('Y');
        for ($i = $current_year - 2; $i <= $current_year + 1; $i++) {
            $years[] = $i . '-' . ($i + 1);
        }

        return view('pages.support_team.print_center.index', compact('my_classes', 'exams', 'years'));
    }
}
