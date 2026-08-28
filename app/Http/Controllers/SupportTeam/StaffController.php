<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Models\StaffRecord;
use App\User;
use App\Helpers\Qs;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = StaffRecord::with('user')->get();
        
        $totalStaff = $staffs->count();
        $teachers = $staffs->filter(function($s) { return $s->user && $s->user->user_type == 'teacher'; })->count();
        $admins = $staffs->filter(function($s) { return $s->user && in_array($s->user->user_type, ['admin', 'super_admin', 'accountant']); })->count();
        $cdi = $staffs->where('type_contrat', 'CDI')->count();
        $cdd = $staffs->where('type_contrat', 'CDD')->count();

        return view('pages.support_team.staff.index', compact('staffs', 'totalStaff', 'teachers', 'admins', 'cdi', 'cdd'));
    }

    public function show($id)
    {
        $staff = StaffRecord::with('user')->findOrFail(Qs::decodeHash($id));
        return view('pages.support_team.staff.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = StaffRecord::with('user')->findOrFail(Qs::decodeHash($id));
        return view('pages.support_team.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = StaffRecord::findOrFail(Qs::decodeHash($id));

        $data = $request->validate([
            'poste' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'diplome' => 'nullable|string|max:255',
            'specialite' => 'nullable|string|max:255',
            'type_contrat' => 'required|string|max:100',
            'salaire' => 'nullable|numeric',
            'date_fin_contrat' => 'nullable|date',
            'heures_semaine' => 'nullable|integer',
            'observations' => 'nullable|string',
        ]);

        $staff->update($data);

        return Qs::goWithSuccess();
    }

    public function exportExcel()
    {
        $staffs = StaffRecord::with('user')->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Poste');
        $sheet->setCellValue('D1', 'Département');
        $sheet->setCellValue('E1', 'Type Contrat');
        $sheet->setCellValue('F1', 'Salaire');
        
        $row = 2;
        foreach ($staffs as $staff) {
            $sheet->setCellValue('A'.$row, $staff->user->name ?? '');
            $sheet->setCellValue('B'.$row, $staff->user->email ?? '');
            $sheet->setCellValue('C'.$row, $staff->poste);
            $sheet->setCellValue('D'.$row, $staff->departement);
            $sheet->setCellValue('E'.$row, $staff->type_contrat);
            $sheet->setCellValue('F'.$row, $staff->salaire);
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Personnel.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function printList()
    {
        $staffs = StaffRecord::with('user')->get();
        return view('pages.support_team.staff.print_list', compact('staffs'));
    }
}
