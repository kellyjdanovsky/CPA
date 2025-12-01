<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PascomaExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $students;
    protected $rowNumber = 0;

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
            $this->rowNumber++;
            
            // Extraire uniquement le nom de la classe (sans section)
            $class_name = $student->my_class->name ?? 'N/A';
            $class_only = trim(explode(' ', $class_name)[0]);
            
            return [
                'numero' => $this->rowNumber,
                'name' => $student->user->name ?? 'N/A',
                'dob' => $student->user->dob ?? 'N/A',
                'class' => $class_only, // Classe uniquement sans section
                'attestation_no' => $student->attestation_no ?? 'N/A',
                'gender' => $student->user->gender === 'Female' ? 'F' : 'G',
                'somme_payee' => $student->somme_payee . ' Ar',
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N°',
            'Nom et Prénoms de l\'élève',
            'Date de naissance',
            'Classe',
            'N° Attestation d\'Assurance',
            'Sexe',
            'Somme payée',
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
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F4F8']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Center align columns
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'G' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]],
        ];
    }
}
