<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        return $this->students;
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
            'Classe précédente',
            'Section précédente',
            'Statut',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($student): array
    {
        // Vérifier si l'élève est déjà inscrit pour l'année en cours
        $current_year = \App\Helpers\Qs::getSetting('current_session');
        $exists = \App\Models\StudentRecord::where('user_id', $student->user_id)
            ->where('session', $current_year)
            ->exists();

        return [
            $student->user_id,
            $student->user->name,
            $student->adm_no,
            $student->my_class->name,
            $student->section->name,
            $exists ? 'Déjà inscrit' : 'Non inscrit',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        
        // Ajuster la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
    }
}