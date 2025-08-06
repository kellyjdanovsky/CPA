<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Repositories\StudentRepo;
use App\Repositories\MyClassRepo;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentStatisticsController extends Controller
{
    protected $student, $my_class;

    public function __construct(StudentRepo $student, MyClassRepo $my_class)
    {
        $this->student = $student;
        $this->my_class = $my_class;
    }

    public function getDetailedStatistics(Request $request)
    {
        try {
            $classId = $request->get('class_id');

            // Récupérer les étudiants
            if ($classId && $classId !== 'all') {
                $students = $this->student->findStudentsByClass($classId);
                $className = $this->my_class->find($classId)->name ?? 'Classe inconnue';
            } else {
                $students = $this->student->getAll()->with(['user', 'my_class', 'section'])->get();
                $className = 'Toutes les classes';
            }

            $totalStudents = $students->count();

            if ($totalStudents === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_students' => 0,
                        'class_name' => $className,
                        'statistics' => []
                    ]
                ]);
            }

            // Calculer les statistiques
            $statistics = [
                'genre' => $this->calculateGenderStats($students, $totalStudents),
                'statut' => $this->calculateStatusStats($students, $totalStudents),
                'type_etudiant' => $this->calculateStudentTypeStats($students, $totalStudents),
                'statut_academique' => $this->calculateAcademicStatusStats($students, $totalStudents),
                'religion' => $this->calculateReligionStats($students, $totalStudents),
                'tranche_age' => $this->calculateAgeRangeStats($students, $totalStudents)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'total_students' => $totalStudents,
                    'class_name' => $className,
                    'statistics' => $statistics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateGenderStats($students, $total)
    {
        $stats = [
            'Masculin' => $students->filter(function($s) {
                return $s->user && $s->user->gender === 'Male';
            })->count(),
            'Féminin' => $students->filter(function($s) {
                return $s->user && $s->user->gender === 'Female';
            })->count()
        ];

        return $this->formatStats($stats, $total);
    }

    private function calculateStatusStats($students, $total)
    {
        $stats = [
            'Normal' => $students->filter(function($s) {
                return $s->user && ($s->user->status === 'Normal' || is_null($s->user->status));
            })->count(),
            'ADRA' => $students->filter(function($s) {
                return $s->user && $s->user->status === 'ADRA';
            })->count(),
            'TEAM3' => $students->filter(function($s) {
                return $s->user && $s->user->status === 'TEAM3';
            })->count()
        ];

        return $this->formatStats($stats, $total);
    }

    private function calculateStudentTypeStats($students, $total)
    {
        $stats = [
            'Nouveau' => $students->filter(function($s) {
                return $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type));
            })->count(),
            'Ancien' => $students->filter(function($s) {
                return $s->user && $s->user->student_type === 'Ancien';
            })->count()
        ];

        return $this->formatStats($stats, $total);
    }

    private function calculateAcademicStatusStats($students, $total)
    {
        $stats = [
            'Passant' => $students->filter(function($s) {
                return $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status));
            })->count(),
            'Redoublant' => $students->filter(function($s) {
                return $s->user && $s->user->academic_status === 'Redoublant';
            })->count()
        ];

        return $this->formatStats($stats, $total);
    }

    private function calculateReligionStats($students, $total)
    {
        $religions = ['FLM', 'FJKM', 'Catholique', 'Adventiste', 'Islam', 'Judaïsme', 'Apokalipsy', 'Autres'];
        $stats = [];

        foreach ($religions as $religion) {
            $stats[$religion] = $students->filter(function($s) use ($religion) {
                return $s->user && $s->user->religion === $religion;
            })->count();
        }

        // Ajouter les non renseignés
        $stats['Non renseigné'] = $students->filter(function($s) {
            return $s->user && is_null($s->user->religion);
        })->count();

        return $this->formatStats($stats, $total);
    }

    private function calculateAgeRangeStats($students, $total)
    {
        $ageRanges = [
            '6-8 ans' => [6, 8],
            '8-10 ans' => [8, 10],
            '10-12 ans' => [10, 12],
            '12-14 ans' => [12, 14],
            '14-16 ans' => [14, 16],
            '16-18 ans' => [16, 18],
            '18-20 ans' => [18, 20],
            '20+ ans' => [20, 100]
        ];

        $stats = [];

        foreach ($ageRanges as $range => $limits) {
            $count = $students->filter(function($student) use ($limits) {
                if (!$student->user->dob) return false;
                
                $age = Qs::calculateAge($student->user->dob);
                return $age >= $limits[0] && $age < $limits[1];
            })->count();

            $stats[$range] = $count;
        }

        // Ajouter les âges non calculables
        $stats['Non calculable'] = $students->filter(function($student) {
            return !$student->user->dob;
        })->count();

        return $this->formatStats($stats, $total);
    }

    private function formatStats($stats, $total)
    {
        $formatted = [];
        
        foreach ($stats as $category => $count) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            $formatted[] = [
                'category' => $category,
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        return $formatted;
    }

    public function exportStatistics(Request $request)
    {
        $classId = $request->get('class_id');
        
        // Récupérer les données
        $response = $this->getDetailedStatistics($request);
        $data = $response->getData(true)['data'];

        // Créer le fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configuration de base
        $sheet->setTitle('Statistiques Étudiants');
        
        // En-tête principal
        $sheet->setCellValue('A1', 'STATISTIQUES DÉTAILLÉES DES ÉTUDIANTS');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Informations générales
        $sheet->setCellValue('A3', 'Classe:');
        $sheet->setCellValue('B3', $data['class_name']);
        $sheet->setCellValue('A4', 'Total étudiants:');
        $sheet->setCellValue('B4', $data['total_students']);
        $sheet->setCellValue('A5', 'Date de génération:');
        $sheet->setCellValue('B5', date('d/m/Y H:i:s'));

        $currentRow = 7;

        // Générer les tableaux pour chaque statistique
        $statisticTitles = [
            'genre' => 'Répartition par Genre',
            'statut' => 'Répartition par Statut',
            'type_etudiant' => 'Répartition par Type d\'Étudiant',
            'statut_academique' => 'Répartition par Statut Académique',
            'religion' => 'Répartition par Religion',
            'tranche_age' => 'Répartition par Tranche d\'Âge'
        ];

        foreach ($statisticTitles as $key => $title) {
            // Titre de la section
            $sheet->setCellValue("A{$currentRow}", $title);
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("A{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E3F2FD');
            
            $currentRow++;

            // En-têtes du tableau
            $sheet->setCellValue("A{$currentRow}", 'Catégorie');
            $sheet->setCellValue("B{$currentRow}", 'Nombre');
            $sheet->setCellValue("C{$currentRow}", 'Pourcentage');
            $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F5F5F5');

            $currentRow++;

            // Données
            foreach ($data['statistics'][$key] as $stat) {
                $sheet->setCellValue("A{$currentRow}", $stat['category']);
                $sheet->setCellValue("B{$currentRow}", $stat['count']);
                $sheet->setCellValue("C{$currentRow}", $stat['percentage'] . '%');
                $currentRow++;
            }

            $currentRow += 2; // Espacement entre les sections
        }

        // Ajuster la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        // Bordures pour tous les tableaux
        $lastRow = $currentRow - 2;
        $sheet->getStyle("A1:D{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Générer le fichier
        $filename = 'statistiques_etudiants_' . ($classId === 'all' ? 'toutes_classes' : 'classe_' . $classId) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        // Headers pour le téléchargement
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
