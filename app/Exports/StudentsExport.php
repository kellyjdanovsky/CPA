<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->students->map(function ($student) {
            return [
                'id' => $student->user_id,
                'name' => $student->user->name ?? 'N/A',
                'adm_no' => $student->adm_no,
                'class' => $student->my_class->name ?? 'N/A',
                'section' => $student->section->name ?? 'N/A',
                'year_admitted' => $student->year_admitted,
                'house' => $student->house,
                'age' => $student->age,
                'session' => $student->session,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom de l\'élève',
            'Numéro d\'admission',
            'Classe',
            'Section',
            'Année d\'admission',
            'Maison',
            'Âge',
            'Session',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}