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
                return $s->user && ($s->user->status === 'TEAM3' || $s->user->status === 'Team3');
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
        $religions = ['Adventiste', 'Catholique', 'FJKM', 'FLM', 'Islam', 'Judaïsme', 'Apokalipsy', 'Autres'];
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
            '3-5 ans' => [3, 6],
            '6-8 ans' => [6, 9],
            '9-11 ans' => [9, 12],
            '12-14 ans' => [12, 15],
            '15-17 ans' => [15, 18],
            '18+ ans' => [18, 100]
        ];

        $stats = [];

        foreach ($ageRanges as $range => $limits) {
            $count = $students->filter(function($student) use ($limits) {
                if (!$student->user || !$student->user->dob) return false;
                $age = Qs::calculateAge($student->user->dob);
                return $age >= $limits[0] && $age < $limits[1];
            })->count();

            $stats[$range] = $count;
        }

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

    /**
     * Imprimer le rapport statistique officiel A4 Paysage
     */
    public function printStatisticsReport(Request $request)
    {
        $classes = $this->my_class->all();
        $allStudents = $this->student->getAll()->with(['user', 'my_class', 'section'])->get();

        $total_students = $allStudents->count();
        $total_boys = $allStudents->filter(fn($s) => $s->user && $s->user->gender === 'Male')->count();
        $total_girls = $allStudents->filter(fn($s) => $s->user && $s->user->gender === 'Female')->count();

        $total_normal = $allStudents->filter(fn($s) => $s->user && ($s->user->status === 'Normal' || is_null($s->user->status)))->count();
        $total_adra = $allStudents->filter(fn($s) => $s->user && $s->user->status === 'ADRA')->count();
        $total_team3 = $allStudents->filter(fn($s) => $s->user && ($s->user->status === 'TEAM3' || $s->user->status === 'Team3'))->count();

        $total_nouveaux = $allStudents->filter(fn($s) => $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type)))->count();
        $total_anciens = $allStudents->filter(fn($s) => $s->user && $s->user->student_type === 'Ancien')->count();

        $total_passants = $allStudents->filter(fn($s) => $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status)))->count();
        $total_redoublants = $allStudents->filter(fn($s) => $s->user && $s->user->academic_status === 'Redoublant')->count();

        $total_adventiste = $allStudents->filter(fn($s) => $s->user && $s->user->religion === 'Adventiste')->count();
        $total_catholique = $allStudents->filter(fn($s) => $s->user && $s->user->religion === 'Catholique')->count();
        $total_fjkm = $allStudents->filter(fn($s) => $s->user && $s->user->religion === 'FJKM')->count();
        $total_autres_rel = $total_students - ($total_adventiste + $total_catholique + $total_fjkm);

        $ageSum = 0;
        $ageCount = 0;
        foreach ($allStudents as $s) {
            if ($s->user && $s->user->dob) {
                $ageSum += Qs::calculateAge($s->user->dob);
                $ageCount++;
            }
        }
        $avg_age = $ageCount > 0 ? round($ageSum / $ageCount, 1) : 0;

        // Tableau Croisé par Classe
        $class_matrix = [];
        foreach ($classes as $c) {
            $cStudents = $allStudents->where('my_class_id', $c->id);
            $cTotal = $cStudents->count();
            $cBoys = $cStudents->filter(fn($s) => $s->user && $s->user->gender === 'Male')->count();
            $cGirls = $cStudents->filter(fn($s) => $s->user && $s->user->gender === 'Female')->count();
            
            $cAgeSum = 0;
            $cAgeCount = 0;
            foreach ($cStudents as $cs) {
                if ($cs->user && $cs->user->dob) {
                    $cAgeSum += Qs::calculateAge($cs->user->dob);
                    $cAgeCount++;
                }
            }

            $class_matrix[] = [
                'name' => $c->name,
                'total' => $cTotal,
                'boys' => $cBoys,
                'girls' => $cGirls,
                'avg_age' => $cAgeCount > 0 ? round($cAgeSum / $cAgeCount, 1) : '-',
                'normal' => $cStudents->filter(fn($s) => $s->user && ($s->user->status === 'Normal' || is_null($s->user->status)))->count(),
                'adra' => $cStudents->filter(fn($s) => $s->user && $s->user->status === 'ADRA')->count(),
                'team3' => $cStudents->filter(fn($s) => $s->user && ($s->user->status === 'TEAM3' || $s->user->status === 'Team3'))->count(),
                'nouveau' => $cStudents->filter(fn($s) => $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type)))->count(),
                'ancien' => $cStudents->filter(fn($s) => $s->user && $s->user->student_type === 'Ancien')->count(),
                'passant' => $cStudents->filter(fn($s) => $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status)))->count(),
                'redoublant' => $cStudents->filter(fn($s) => $s->user && $s->user->academic_status === 'Redoublant')->count(),
                'adventiste' => $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'Adventiste')->count(),
                'catholique' => $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'Catholique')->count(),
                'fjkm' => $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'FJKM')->count(),
                'autres_rel' => $cTotal - $cStudents->filter(fn($s) => $s->user && in_array($s->user->religion, ['Adventiste', 'Catholique', 'FJKM']))->count()
            ];
        }

        // Pyramide des Âges par Genre
        $ageRanges = [
            '3-5 ans' => [3, 6],
            '6-8 ans' => [6, 9],
            '9-11 ans' => [9, 12],
            '12-14 ans' => [12, 15],
            '15-17 ans' => [15, 18],
            '18+ ans' => [18, 100]
        ];

        $age_breakdown = [];
        foreach ($ageRanges as $range => $limits) {
            $inRange = $allStudents->filter(function($s) use ($limits) {
                if (!$s->user || !$s->user->dob) return false;
                $a = Qs::calculateAge($s->user->dob);
                return $a >= $limits[0] && $a < $limits[1];
            });

            $age_breakdown[$range] = [
                'total' => $inRange->count(),
                'boys' => $inRange->filter(fn($s) => $s->user->gender === 'Male')->count(),
                'girls' => $inRange->filter(fn($s) => $s->user->gender === 'Female')->count()
            ];
        }

        // Répartition par Religion
        $religionsList = ['Adventiste', 'Catholique', 'FJKM', 'FLM', 'Islam', 'Judaïsme', 'Apokalipsy', 'Autres', 'Non renseigné'];
        $religion_breakdown = [];
        foreach ($religionsList as $rel) {
            $inRel = $allStudents->filter(function($s) use ($rel) {
                if ($rel === 'Non renseigné') return !$s->user || is_null($s->user->religion);
                return $s->user && $s->user->religion === $rel;
            });

            $religion_breakdown[$rel] = [
                'total' => $inRel->count(),
                'boys' => $inRel->filter(fn($s) => $s->user && $s->user->gender === 'Male')->count(),
                'girls' => $inRel->filter(fn($s) => $s->user && $s->user->gender === 'Female')->count()
            ];
        }

        $data = compact(
            'total_students', 'total_boys', 'total_girls', 'avg_age',
            'total_normal', 'total_adra', 'total_team3',
            'total_nouveaux', 'total_anciens', 'total_passants', 'total_redoublants',
            'total_adventiste', 'total_catholique', 'total_fjkm', 'total_autres_rel',
            'class_matrix', 'age_breakdown', 'religion_breakdown'
        );

        return view('pages.support_team.students.print_statistics_report', $data);
    }

    /**
     * Export Excel exhaustif des statistiques multi-dimensionnelles
     */
    public function exportStatistics(Request $request)
    {
        $classes = $this->my_class->all();
        $allStudents = $this->student->getAll()->with(['user', 'my_class', 'section'])->get();
        $totalStudents = $allStudents->count();

        $spreadsheet = new Spreadsheet();
        
        // --- FEUILLE 1 : Synthèse Croisée par Classe ---
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Synthèse par Classe');

        $sheet1->setCellValue('A1', 'COLLÈGE PRIVÉ ADVENTISTE - STATISTIQUES CROISÉES DES ÉLÈVES');
        $sheet1->mergeCells('A1:Q1');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet1->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'Classe', 'Total Élèves', 'Garçons (M)', 'Filles (F)', '% Filles', 'Âge Moyen',
            'Normal', 'ADRA', 'TEAM3', 'Nouveau', 'Ancien', 'Passant', 'Redoublant',
            'Adventiste', 'Catholique', 'FJKM', 'Autres Religions'
        ];

        $sheet1->fromArray([$headers], null, 'A3');
        $sheet1->getStyle('A3:Q3')->getFont()->setBold(true);
        $sheet1->getStyle('A3:Q3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E7FF');

        $rowIdx = 4;
        foreach ($classes as $c) {
            $cStudents = $allStudents->where('my_class_id', $c->id);
            $cTotal = $cStudents->count();
            $cBoys = $cStudents->filter(fn($s) => $s->user && $s->user->gender === 'Male')->count();
            $cGirls = $cStudents->filter(fn($s) => $s->user && $s->user->gender === 'Female')->count();
            
            $cAgeSum = 0; $cAgeCount = 0;
            foreach ($cStudents as $cs) {
                if ($cs->user && $cs->user->dob) {
                    $cAgeSum += Qs::calculateAge($cs->user->dob);
                    $cAgeCount++;
                }
            }

            $sheet1->fromArray([[
                $c->name,
                $cTotal,
                $cBoys,
                $cGirls,
                $cTotal > 0 ? round(($cGirls / $cTotal) * 100, 1) . '%' : '0%',
                $cAgeCount > 0 ? round($cAgeSum / $cAgeCount, 1) : '-',
                $cStudents->filter(fn($s) => $s->user && ($s->user->status === 'Normal' || is_null($s->user->status)))->count(),
                $cStudents->filter(fn($s) => $s->user && $s->user->status === 'ADRA')->count(),
                $cStudents->filter(fn($s) => $s->user && ($s->user->status === 'TEAM3' || $s->user->status === 'Team3'))->count(),
                $cStudents->filter(fn($s) => $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type)))->count(),
                $cStudents->filter(fn($s) => $s->user && $s->user->student_type === 'Ancien')->count(),
                $cStudents->filter(fn($s) => $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status)))->count(),
                $cStudents->filter(fn($s) => $s->user && $s->user->academic_status === 'Redoublant')->count(),
                $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'Adventiste')->count(),
                $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'Catholique')->count(),
                $cStudents->filter(fn($s) => $s->user && $s->user->religion === 'FJKM')->count(),
                $cTotal - $cStudents->filter(fn($s) => $s->user && in_array($s->user->religion, ['Adventiste', 'Catholique', 'FJKM']))->count()
            ]], null, 'A' . $rowIdx);

            $rowIdx++;
        }

        // Ligne de Total
        $totalBoys = $allStudents->filter(fn($s) => $s->user && $s->user->gender === 'Male')->count();
        $totalGirls = $allStudents->filter(fn($s) => $s->user && $s->user->gender === 'Female')->count();
        $sheet1->fromArray([[
            'TOTAL ÉCOLE',
            $totalStudents,
            $totalBoys,
            $totalGirls,
            $totalStudents > 0 ? round(($totalGirls / $totalStudents) * 100, 1) . '%' : '0%',
            '-',
            $allStudents->filter(fn($s) => $s->user && ($s->user->status === 'Normal' || is_null($s->user->status)))->count(),
            $allStudents->filter(fn($s) => $s->user && $s->user->status === 'ADRA')->count(),
            $allStudents->filter(fn($s) => $s->user && ($s->user->status === 'TEAM3' || $s->user->status === 'Team3'))->count(),
            $allStudents->filter(fn($s) => $s->user && ($s->user->student_type === 'Nouveau' || is_null($s->user->student_type)))->count(),
            $allStudents->filter(fn($s) => $s->user && $s->user->student_type === 'Ancien')->count(),
            $allStudents->filter(fn($s) => $s->user && ($s->user->academic_status === 'Passant' || is_null($s->user->academic_status)))->count(),
            $allStudents->filter(fn($s) => $s->user && $s->user->academic_status === 'Redoublant')->count(),
            $allStudents->filter(fn($s) => $s->user && $s->user->religion === 'Adventiste')->count(),
            $allStudents->filter(fn($s) => $s->user && $s->user->religion === 'Catholique')->count(),
            $allStudents->filter(fn($s) => $s->user && $s->user->religion === 'FJKM')->count(),
            $totalStudents - $allStudents->filter(fn($s) => $s->user && in_array($s->user->religion, ['Adventiste', 'Catholique', 'FJKM']))->count()
        ]], null, 'A' . $rowIdx);

        $sheet1->getStyle('A' . $rowIdx . ':Q' . $rowIdx)->getFont()->setBold(true);
        $sheet1->getStyle('A' . $rowIdx . ':Q' . $rowIdx)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');

        foreach (range('A', 'Q') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // --- FEUILLE 2 : Âge & Religions ---
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Âges et Religions');

        $sheet2->setCellValue('A1', 'RÉPARTITION PAR TRANCHE D\'ÂGE');
        $sheet2->mergeCells('A1:E1');
        $sheet2->getStyle('A1')->getFont()->setBold(true);

        $sheet2->fromArray([['Tranche d\'âge', 'Garçons (M)', 'Filles (F)', 'Total', 'Part (%)']], null, 'A2');
        $sheet2->getStyle('A2:E2')->getFont()->setBold(true)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');

        $ageRanges = ['3-5 ans' => [3, 6], '6-8 ans' => [6, 9], '9-11 ans' => [9, 12], '12-14 ans' => [12, 15], '15-17 ans' => [15, 18], '18+ ans' => [18, 100]];
        $rIdx = 3;
        foreach ($ageRanges as $range => $limits) {
            $inRange = $allStudents->filter(function($s) use ($limits) {
                if (!$s->user || !$s->user->dob) return false;
                $a = Qs::calculateAge($s->user->dob);
                return $a >= $limits[0] && $a < $limits[1];
            });
            $sheet2->fromArray([[
                $range,
                $inRange->filter(fn($s) => $s->user->gender === 'Male')->count(),
                $inRange->filter(fn($s) => $s->user->gender === 'Female')->count(),
                $inRange->count(),
                $totalStudents > 0 ? round(($inRange->count() / $totalStudents) * 100, 1) . '%' : '0%'
            ]], null, 'A' . $rIdx);
            $rIdx++;
        }

        // Tableau Religions
        $rIdx += 2;
        $sheet2->setCellValue('A' . $rIdx, 'RÉPARTITION PAR RELIGION');
        $sheet2->mergeCells('A' . $rIdx . ':E' . $rIdx);
        $sheet2->getStyle('A' . $rIdx)->getFont()->setBold(true);

        $rIdx++;
        $sheet2->fromArray([['Religion / Culte', 'Garçons (M)', 'Filles (F)', 'Total', 'Part (%)']], null, 'A' . $rIdx);
        $sheet2->getStyle('A' . $rIdx . ':E' . $rIdx)->getFont()->setBold(true)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');

        $rIdx++;
        $religionsList = ['Adventiste', 'Catholique', 'FJKM', 'FLM', 'Islam', 'Judaïsme', 'Apokalipsy', 'Autres', 'Non renseigné'];
        foreach ($religionsList as $rel) {
            $inRel = $allStudents->filter(function($s) use ($rel) {
                if ($rel === 'Non renseigné') return !$s->user || is_null($s->user->religion);
                return $s->user && $s->user->religion === $rel;
            });
            $sheet2->fromArray([[
                $rel,
                $inRel->filter(fn($s) => $s->user && $s->user->gender === 'Male')->count(),
                $inRel->filter(fn($s) => $s->user && $s->user->gender === 'Female')->count(),
                $inRel->count(),
                $totalStudents > 0 ? round(($inRel->count() / $totalStudents) * 100, 1) . '%' : '0%'
            ]], null, 'A' . $rIdx);
            $rIdx++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Statistiques_Globales_Eleves_' . date('Y-m-d_H-i') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
