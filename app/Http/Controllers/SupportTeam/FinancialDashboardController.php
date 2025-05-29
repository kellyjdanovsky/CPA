<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentRecord;
use App\Models\Receipt;
use App\Repositories\MyClassRepo;
use App\Repositories\PaymentRepo;
use App\Repositories\StudentRepo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Barryvdh\DomPDF\Facade as PDF;

class FinancialDashboardController extends Controller
{
    protected $payment, $my_class, $student, $year;

    public function __construct(PaymentRepo $payment, MyClassRepo $my_class, StudentRepo $student)
    {
        $this->payment = $payment;
        $this->my_class = $my_class;
        $this->student = $student;
        $this->year = Qs::getCurrentSession();

        $this->middleware('teamSA');
    }

    /**
     * Affiche le tableau de bord financier
     */
    public function index(Request $request)
    {
        // Période par défaut (mois en cours)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $period = $request->input('period', 'month');
        $classId = $request->input('class_id');
        $paymentType = $request->input('payment_type');

        // Convertir les dates en objets Carbon pour faciliter les manipulations
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        // Ajuster les dates en fonction de la période sélectionnée
        if ($period == 'day') {
            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        } elseif ($period == 'week') {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        } elseif ($period == 'month') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        } elseif ($period == 'year') {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        }

        // Récupérer les recettes (paiements reçus)
        $receiptsQuery = Receipt::with(['pr.payment', 'pr.student'])
            ->whereHas('pr', function ($query) use ($classId, $paymentType) {
                $query->where('year', $this->year);

                if ($classId) {
                    $query->whereHas('student.studentRecord', function ($q) use ($classId) {
                        $q->where('my_class_id', $classId);
                    });
                }

                if ($paymentType) {
                    $query->whereHas('payment', function ($q) use ($paymentType) {
                        $q->where('id', $paymentType);
                    });
                }
            })
            ->whereBetween('created_at', [$startDateCarbon->startOfDay(), $endDateCarbon->endOfDay()]);

        $receipts = $receiptsQuery->get();

        // Calculer le montant total des recettes
        $totalRevenue = $receipts->sum('amt_paid');

        // Récupérer les dépenses (décaissements) avec gestion d'erreur améliorée
        $totalExpenses = 0;
        $expenses = collect();

        try {
            if (Schema::hasTable('decaissements')) {
                $expensesQuery = DB::table('decaissements')
                    ->whereBetween('date_paiement', [$startDate, $endDate])
                    ->where('status', 'approuve') // Seulement les dépenses approuvées
                    ->where('year', $this->year); // Filtrer par année scolaire

                if ($classId) {
                    // Filtrer les dépenses par classe si applicable
                    // Ajouter une jointure si nécessaire pour lier aux classes
                    $expensesQuery->where('motif', 'LIKE', '%classe%');
                }

                $expenses = $expensesQuery->orderBy('date_paiement', 'desc')->get();
                $totalExpenses = $expenses->sum('montant');

                // Log pour debug
                \Log::info('Décaissements récupérés', [
                    'count' => $expenses->count(),
                    'total' => $totalExpenses,
                    'period' => [$startDate, $endDate]
                ]);
            } else {
                \Log::warning('Table decaissements non trouvée');
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des décaissements: ' . $e->getMessage());
            $totalExpenses = 0;
            $expenses = collect();
        }

        // Calculer le solde de trésorerie
        $cashBalance = $totalRevenue - $totalExpenses;

        // Calculer les moyennes mensuelles
        $daysInPeriod = $startDateCarbon->diffInDays($endDateCarbon) + 1;
        $monthsInPeriod = max(1, round($daysInPeriod / 30, 1));

        $avgMonthlyRevenue = $totalRevenue / $monthsInPeriod;
        $avgMonthlyExpenses = $totalExpenses / $monthsInPeriod;

        // Calculer le taux de recouvrement
        $expectedPayments = PaymentRecord::where('payment_records.year', $this->year)
            ->when($classId, function ($query) use ($classId) {
                return $query->whereHas('student.student_record', function ($q) use ($classId) {
                    $q->where('my_class_id', $classId);
                });
            })
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_id', $paymentType);
            })
            ->join('payments', 'payment_records.payment_id', '=', 'payments.id')
            ->sum('payments.amount');

        $recoveryRate = $expectedPayments > 0 ? ($totalRevenue / $expectedPayments) * 100 : 0;

        // Récupérer les données pour les graphiques

        // 1. Recettes et dépenses par mois
        $monthlyData = $this->getMonthlyData($startDateCarbon, $endDateCarbon);

        // 2. Répartition des recettes par catégorie
        $revenueByCategory = $this->getRevenueByCategory();

        // 3. Répartition des dépenses par catégorie
        $expensesByCategory = $this->getExpensesByCategory();

        // 4. Recettes par classe
        $revenueByClass = $this->getRevenueByClass();

        // 5. Retards de paiement par classe
        $latePaymentsByClass = $this->getLatePaymentsByClass();

        // 6. Tendances annuelles
        $yearlyTrends = $this->getYearlyTrends();

        // 7. Prévisions de trésorerie
        $cashFlowForecast = $this->getCashFlowForecast();

        // Récupérer les classes pour le filtre
        $classes = $this->my_class->all();

        // Récupérer les types de paiement pour le filtre
        $paymentTypes = Payment::where('year', $this->year)->get();

        // Générer les alertes
        $alerts = $this->generateAlerts($monthlyData, $latePaymentsByClass);

        return view('pages.support_team.financial_dashboard.index', compact(
            'startDate',
            'endDate',
            'period',
            'classId',
            'paymentType',
            'totalRevenue',
            'totalExpenses',
            'cashBalance',
            'avgMonthlyRevenue',
            'avgMonthlyExpenses',
            'recoveryRate',
            'monthlyData',
            'revenueByCategory',
            'expensesByCategory',
            'revenueByClass',
            'latePaymentsByClass',
            'yearlyTrends',
            'cashFlowForecast',
            'classes',
            'paymentTypes',
            'alerts'
        ));
    }

    /**
     * Récupère les données mensuelles pour les graphiques
     */
    private function getMonthlyData($startDate, $endDate)
    {
        $months = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $months[$monthKey] = [
                'label' => $currentDate->format('M Y'),
                'revenue' => 0,
                'expenses' => 0
            ];

            $currentDate->addMonth();
        }

        // Récupérer les recettes par mois
        $monthlyRevenues = Receipt::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amt_paid) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->get();

        foreach ($monthlyRevenues as $revenue) {
            if (isset($months[$revenue->month])) {
                $months[$revenue->month]['revenue'] = $revenue->total;
            }
        }

        // Récupérer les dépenses par mois avec gestion d'erreur améliorée
        try {
            if (Schema::hasTable('decaissements')) {
                $monthlyExpenses = DB::table('decaissements')
                    ->selectRaw('DATE_FORMAT(date_paiement, "%Y-%m") as month, SUM(montant) as total')
                    ->whereBetween('date_paiement', [$startDate, $endDate])
                    ->where('status', 'approuve') // Seulement les dépenses approuvées
                    ->where('year', $this->year) // Filtrer par année scolaire
                    ->groupBy('month')
                    ->get();

                foreach ($monthlyExpenses as $expense) {
                    if (isset($months[$expense->month])) {
                        $months[$expense->month]['expenses'] = (float) $expense->total;
                    }
                }

                // Log pour debug
                \Log::info('Dépenses mensuelles récupérées', [
                    'count' => $monthlyExpenses->count(),
                    'data' => $monthlyExpenses->toArray()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des dépenses mensuelles: ' . $e->getMessage());
        }

        return array_values($months);
    }

    /**
     * Récupère la répartition des recettes par catégorie
     */
    private function getRevenueByCategory()
    {
        $revenueByCategory = Receipt::with('pr.payment')
            ->whereHas('pr', function ($query) {
                $query->where('payment_records.year', $this->year);
            })
            ->get()
            ->groupBy(function ($receipt) {
                return $receipt->pr->payment->title ?? 'Autre';
            })
            ->map(function ($receipts) {
                return $receipts->sum('amt_paid');
            });

        return $revenueByCategory;
    }

    /**
     * Récupère la répartition des dépenses par catégorie
     */
    private function getExpensesByCategory()
    {
        $expensesByCategory = collect();

        if (Schema::hasTable('decaissements')) {
            $expensesByCategory = DB::table('decaissements')
                ->where('decaissements.year', $this->year)
                ->get()
                ->groupBy('motif')
                ->map(function ($expenses) {
                    return $expenses->sum('montant');
                });
        }

        return $expensesByCategory;
    }

    /**
     * Récupère les recettes par classe
     */
    private function getRevenueByClass()
    {
        $classes = $this->my_class->all();
        $revenueByClass = [];

        foreach ($classes as $class) {
            $revenueByClass[$class->name] = Receipt::with(['pr.student.student_record'])
                ->whereHas('pr.student.student_record', function ($query) use ($class) {
                    $query->where('my_class_id', $class->id);
                })
                ->whereHas('pr', function ($query) {
                    $query->where('payment_records.year', $this->year);
                })
                ->sum('amt_paid');
        }

        return $revenueByClass;
    }

    /**
     * Récupère les retards de paiement par classe
     */
    private function getLatePaymentsByClass()
    {
        $classes = $this->my_class->all();
        $latePaymentsByClass = [];

        foreach ($classes as $class) {
            $latePaymentsByClass[$class->name] = PaymentRecord::with(['student.student_record'])
                ->whereHas('student.student_record', function ($query) use ($class) {
                    $query->where('my_class_id', $class->id);
                })
                ->where('payment_records.year', $this->year)
                ->where('paid', 0)
                ->count();
        }

        return $latePaymentsByClass;
    }

    /**
     * Récupère les données de tendance annuelle
     */
    private function getYearlyTrends()
    {
        // Récupérer les 3 dernières années
        $years = DB::table('payment_records')
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->limit(3)
            ->pluck('year')
            ->toArray();

        // Si moins de 3 années sont disponibles, utiliser les années disponibles
        if (count($years) < 3) {
            $years = DB::table('payment_records')
                ->select('year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        }

        $yearlyTrends = [];

        foreach ($years as $year) {
            // Récupérer les recettes par mois pour cette année
            $monthlyRevenues = Receipt::join('payment_records', 'receipts.pr_id', '=', 'payment_records.id')
                ->where('payment_records.year', $year)
                ->selectRaw('MONTH(receipts.created_at) as month, SUM(receipts.amt_paid) as total')
                ->groupBy('month')
                ->get()
                ->keyBy('month')
                ->map(function ($item) {
                    return $item->total;
                })
                ->toArray();

            // Récupérer les dépenses par mois pour cette année si la table existe
            $monthlyExpenses = [];

            if (Schema::hasTable('decaissements')) {
                $monthlyExpenses = DB::table('decaissements')
                    ->where('decaissements.year', $year)
                    ->selectRaw('MONTH(date_paiement) as month, SUM(montant) as total')
                    ->groupBy('month')
                    ->get()
                    ->keyBy('month')
                    ->map(function ($item) {
                        return $item->total;
                    })
                    ->toArray();
            }

            // Préparer les données pour tous les mois (1-12)
            $yearData = [
                'year' => $year,
                'revenues' => [],
                'expenses' => []
            ];

            for ($month = 1; $month <= 12; $month++) {
                $yearData['revenues'][$month] = $monthlyRevenues[$month] ?? 0;
                $yearData['expenses'][$month] = $monthlyExpenses[$month] ?? 0;
            }

            $yearlyTrends[] = $yearData;
        }

        return $yearlyTrends;
    }

    /**
     * Récupère les données de prévision de trésorerie
     */
    private function getCashFlowForecast()
    {
        // Récupérer les données des 3 derniers mois
        $lastThreeMonths = Carbon::now()->subMonths(3);

        $historicalData = Receipt::whereBetween('created_at', [$lastThreeMonths, Carbon::now()])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amt_paid) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return $item->revenue;
            })
            ->toArray();

        // Calculer la moyenne des 3 derniers mois
        $avgRevenue = count($historicalData) > 0 ? array_sum($historicalData) / count($historicalData) : 0;

        // Préparer les prévisions pour les 3 prochains mois
        $forecast = [];
        $currentDate = Carbon::now();

        for ($i = 1; $i <= 3; $i++) {
            $forecastDate = $currentDate->copy()->addMonths($i);
            $month = $forecastDate->format('Y-m');

            // Appliquer une croissance de 5% par mois (exemple simple)
            $forecastRevenue = $avgRevenue * (1 + (0.05 * $i));

            $forecast[$month] = $forecastRevenue;
        }

        return [
            'historical' => $historicalData,
            'forecast' => $forecast
        ];
    }

    /**
     * Génère les alertes pour le tableau de bord
     */
    private function generateAlerts($monthlyData, $latePaymentsByClass)
    {
        $alerts = [];

        // Alerte 1: Dépassement budgétaire
        $currentMonthData = end($monthlyData);
        if ($currentMonthData && $currentMonthData['expenses'] > $currentMonthData['revenue']) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'icon-alert',
                'message' => 'Dépassement budgétaire: Les dépenses du mois en cours (' . number_format($currentMonthData['expenses'], 0, ',', ' ') . ' Ar) sont supérieures aux recettes (' . number_format($currentMonthData['revenue'], 0, ',', ' ') . ' Ar).'
            ];
        }

        // Alerte 2: Baisse inhabituelle de recettes
        if (count($monthlyData) >= 3) {
            $lastThreeMonths = array_slice($monthlyData, -3);
            $currentMonth = end($lastThreeMonths);
            $previousMonths = array_slice($lastThreeMonths, 0, 2);

            $avgPreviousRevenue = array_sum(array_column($previousMonths, 'revenue')) / count($previousMonths);

            if ($currentMonth['revenue'] < $avgPreviousRevenue * 0.7) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'icon-warning22',
                    'message' => 'Baisse inhabituelle de recettes: Les recettes du mois en cours (' . number_format($currentMonth['revenue'], 0, ',', ' ') . ' Ar) sont inférieures à 70% de la moyenne des 2 derniers mois (' . number_format($avgPreviousRevenue, 0, ',', ' ') . ' Ar).'
                ];
            }
        }

        // Alerte 3: Inactivité
        $lastActivity = Receipt::max('created_at');
        if ($lastActivity) {
            $daysSinceLastActivity = Carbon::parse($lastActivity)->diffInDays(Carbon::now());

            if ($daysSinceLastActivity > 7) {
                $alerts[] = [
                    'type' => 'info',
                    'icon' => 'icon-info3',
                    'message' => 'Inactivité: Aucune recette enregistrée depuis ' . $daysSinceLastActivity . ' jours.'
                ];
            }
        }

        // Alerte 4: Retard de paiement élèves
        $totalLatePayments = array_sum($latePaymentsByClass);
        if ($totalLatePayments > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'icon-users4',
                'message' => 'Retard de paiement: ' . $totalLatePayments . ' élèves n\'ont pas encore effectué leur paiement.'
            ];
        }

        return $alerts;
    }

    /**
     * Exporte les données du tableau de bord en Excel
     */
    public function exportExcel(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $classId = $request->input('class_id');
        $paymentType = $request->input('payment_type');

        // Convertir les dates en objets Carbon
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        // Récupérer les données nécessaires
        $monthlyData = $this->getMonthlyData($startDateCarbon, $endDateCarbon);
        $revenueByCategory = $this->getRevenueByCategory();
        $expensesByCategory = $this->getExpensesByCategory();
        $revenueByClass = $this->getRevenueByClass();
        $latePaymentsByClass = $this->getLatePaymentsByClass();

        // Calculer les totaux
        $totalRevenue = array_sum(array_column($monthlyData, 'revenue'));
        $totalExpenses = array_sum(array_column($monthlyData, 'expenses'));
        $cashBalance = $totalRevenue - $totalExpenses;

        // Calculer les moyennes mensuelles
        $daysInPeriod = $startDateCarbon->diffInDays($endDateCarbon) + 1;
        $monthsInPeriod = max(1, round($daysInPeriod / 30, 1));
        $avgMonthlyRevenue = $totalRevenue / $monthsInPeriod;
        $avgMonthlyExpenses = $totalExpenses / $monthsInPeriod;

        // Calculer le taux de recouvrement
        $expectedPayments = PaymentRecord::where('payment_records.year', $this->year)
            ->when($classId, function ($query) use ($classId) {
                return $query->whereHas('student.student_record', function ($q) use ($classId) {
                    $q->where('my_class_id', $classId);
                });
            })
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_id', $paymentType);
            })
            ->join('payments', 'payment_records.payment_id', '=', 'payments.id')
            ->sum('payments.amount');

        $recoveryRate = $expectedPayments > 0 ? ($totalRevenue / $expectedPayments) * 100 : 0;

        // Créer un nouveau classeur Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tableau de Bord Financier');

        // Définir les styles
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '333333'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $highlightStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Titre du rapport
        $sheet->setCellValue('A1', 'TABLEAU DE BORD FINANCIER');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Période du rapport
        $sheet->setCellValue('A2', 'Période: ' . $startDateCarbon->format('d/m/Y') . ' - ' . $endDateCarbon->format('d/m/Y'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Indicateurs clés
        $sheet->setCellValue('A4', 'INDICATEURS CLÉS');
        $sheet->mergeCells('A4:G4');
        $sheet->getStyle('A4')->applyFromArray($titleStyle);
        $sheet->getStyle('A4')->getFont()->setSize(14);

        $sheet->setCellValue('A5', 'Indicateur');
        $sheet->setCellValue('B5', 'Valeur');
        $sheet->getStyle('A5:B5')->applyFromArray($headerStyle);

        $sheet->setCellValue('A6', 'Solde de trésorerie');
        $sheet->setCellValue('B6', number_format($cashBalance, 0, ',', ' ') . ' Ar');

        $sheet->setCellValue('A7', 'Moyenne mensuelle recettes');
        $sheet->setCellValue('B7', number_format($avgMonthlyRevenue, 0, ',', ' ') . ' Ar');

        $sheet->setCellValue('A8', 'Moyenne mensuelle dépenses');
        $sheet->setCellValue('B8', number_format($avgMonthlyExpenses, 0, ',', ' ') . ' Ar');

        $sheet->setCellValue('A9', 'Taux de recouvrement');
        $sheet->setCellValue('B9', number_format($recoveryRate, 1) . '%');

        $sheet->getStyle('A6:B9')->applyFromArray($dataStyle);
        $sheet->getStyle('A6:A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Évolution mensuelle des recettes et dépenses
        $sheet->setCellValue('A11', 'ÉVOLUTION MENSUELLE DES RECETTES ET DÉPENSES');
        $sheet->mergeCells('A11:G11');
        $sheet->getStyle('A11')->applyFromArray($titleStyle);
        $sheet->getStyle('A11')->getFont()->setSize(14);

        $sheet->setCellValue('A12', 'Mois');
        $sheet->setCellValue('B12', 'Recettes');
        $sheet->setCellValue('C12', 'Dépenses');
        $sheet->setCellValue('D12', 'Solde');
        $sheet->getStyle('A12:D12')->applyFromArray($headerStyle);

        $row = 13;
        foreach ($monthlyData as $data) {
            $sheet->setCellValue('A' . $row, $data['label']);
            $sheet->setCellValue('B' . $row, $data['revenue']);
            $sheet->setCellValue('C' . $row, $data['expenses']);
            $sheet->setCellValue('D' . $row, $data['revenue'] - $data['expenses']);
            $row++;
        }

        // Total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $totalRevenue);
        $sheet->setCellValue('C' . $row, $totalExpenses);
        $sheet->setCellValue('D' . $row, $cashBalance);
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($highlightStyle);

        $sheet->getStyle('A13:D' . $row)->applyFromArray($dataStyle);
        $sheet->getStyle('A13:A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Formater les nombres
        for ($i = 13; $i <= $row; $i++) {
            $sheet->getStyle('B' . $i)->getNumberFormat()->setFormatCode('#,##0 "Ar"');
            $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('#,##0 "Ar"');
            $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('#,##0 "Ar"');
        }

        // Répartition des recettes par catégorie
        $row += 2;
        $sheet->setCellValue('A' . $row, 'RÉPARTITION DES RECETTES PAR CATÉGORIE');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        $sheet->getStyle('A' . $row)->getFont()->setSize(14);

        $row++;
        $sheet->setCellValue('A' . $row, 'Catégorie');
        $sheet->setCellValue('B' . $row, 'Montant');
        $sheet->setCellValue('C' . $row, 'Pourcentage');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);

        $startRow = $row + 1;
        foreach ($revenueByCategory as $category => $amount) {
            $row++;
            $percentage = $totalRevenue > 0 ? ($amount / $totalRevenue) * 100 : 0;
            $sheet->setCellValue('A' . $row, $category);
            $sheet->setCellValue('B' . $row, $amount);
            $sheet->setCellValue('C' . $row, $percentage);
        }

        // Total
        $row++;
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $totalRevenue);
        $sheet->setCellValue('C' . $row, '100%');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($highlightStyle);

        $sheet->getStyle('A' . $startRow . ':C' . $row)->applyFromArray($dataStyle);
        $sheet->getStyle('A' . $startRow . ':A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Formater les nombres
        for ($i = $startRow; $i <= $row; $i++) {
            $sheet->getStyle('B' . $i)->getNumberFormat()->setFormatCode('#,##0 "Ar"');
            $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('0.0"%"');
        }

        // Recettes par classe
        $row += 2;
        $sheet->setCellValue('A' . $row, 'RECETTES PAR CLASSE');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        $sheet->getStyle('A' . $row)->getFont()->setSize(14);

        $row++;
        $sheet->setCellValue('A' . $row, 'Classe');
        $sheet->setCellValue('B' . $row, 'Montant');
        $sheet->setCellValue('C' . $row, 'Pourcentage');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);

        $startRow = $row + 1;
        foreach ($revenueByClass as $class => $amount) {
            $row++;
            $percentage = $totalRevenue > 0 ? ($amount / $totalRevenue) * 100 : 0;
            $sheet->setCellValue('A' . $row, $class);
            $sheet->setCellValue('B' . $row, $amount);
            $sheet->setCellValue('C' . $row, $percentage);
        }

        // Total
        $row++;
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $totalRevenue);
        $sheet->setCellValue('C' . $row, '100%');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($highlightStyle);

        $sheet->getStyle('A' . $startRow . ':C' . $row)->applyFromArray($dataStyle);
        $sheet->getStyle('A' . $startRow . ':A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Formater les nombres
        for ($i = $startRow; $i <= $row; $i++) {
            $sheet->getStyle('B' . $i)->getNumberFormat()->setFormatCode('#,##0 "Ar"');
            $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('0.0"%"');
        }

        // Retards de paiement par classe
        $row += 2;
        $sheet->setCellValue('A' . $row, 'RETARDS DE PAIEMENT PAR CLASSE');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        $sheet->getStyle('A' . $row)->getFont()->setSize(14);

        $row++;
        $sheet->setCellValue('A' . $row, 'Classe');
        $sheet->setCellValue('B' . $row, 'Nombre d\'élèves');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($headerStyle);

        $startRow = $row + 1;
        $totalLatePayments = 0;
        foreach ($latePaymentsByClass as $class => $count) {
            $row++;
            $sheet->setCellValue('A' . $row, $class);
            $sheet->setCellValue('B' . $row, $count);
            $totalLatePayments += $count;
        }

        // Total
        $row++;
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $totalLatePayments);
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($highlightStyle);

        $sheet->getStyle('A' . $startRow . ':B' . $row)->applyFromArray($dataStyle);
        $sheet->getStyle('A' . $startRow . ':A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Ajuster la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Créer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'tableau_de_bord_financier_' . date('Y-m-d') . '.xlsx';

        // Enregistrer le fichier et le télécharger
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Affiche l'onglet des dépenses
     */
    public function expenses(Request $request)
    {
        // Période par défaut (mois en cours)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $period = $request->input('period', 'month');
        $categoryFilter = $request->input('category');

        // Convertir les dates en objets Carbon pour faciliter les manipulations
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        // Ajuster les dates en fonction de la période sélectionnée
        if ($period == 'day') {
            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        } elseif ($period == 'week') {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        } elseif ($period == 'month') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        } elseif ($period == 'year') {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
            $startDateCarbon = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
        }

        // Récupérer les dépenses
        $expenses = collect();
        $totalExpenses = 0;
        $expensesByCategory = collect();
        $monthlyExpenses = [];
        $categories = collect();

        if (Schema::hasTable('decaissements')) {
            // Requête de base pour les dépenses
            $expensesQuery = DB::table('decaissements')
                ->whereBetween('date_paiement', [$startDateCarbon->startOfDay(), $endDateCarbon->endOfDay()]);

            // Filtrer par catégorie si spécifié
            if ($categoryFilter) {
                $expensesQuery->where('motif', $categoryFilter);
            }

            // Récupérer les dépenses
            $expenses = $expensesQuery->orderBy('date_paiement', 'desc')->get();
            $totalExpenses = $expenses->sum('montant');

            // Récupérer les catégories de dépenses
            $categories = DB::table('decaissements')
                ->select('motif')
                ->distinct()
                ->orderBy('motif')
                ->pluck('motif');

            // Répartition des dépenses par catégorie
            $expensesByCategory = $expenses->groupBy('motif')
                ->map(function ($items) {
                    return $items->sum('montant');
                });

            // Dépenses mensuelles
            $monthlyExpenses = DB::table('decaissements')
                ->whereBetween('date_paiement', [$startDateCarbon->startOfDay(), $endDateCarbon->endOfDay()])
                ->selectRaw('DATE_FORMAT(date_paiement, "%Y-%m") as month, SUM(montant) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month')
                ->map(function ($item) {
                    return $item->total;
                })
                ->toArray();
        }

        // Préparer les données pour le graphique mensuel
        $months = [];
        $currentDate = clone $startDateCarbon;

        while ($currentDate <= $endDateCarbon) {
            $monthKey = $currentDate->format('Y-m');
            $months[$monthKey] = [
                'label' => $currentDate->format('M Y'),
                'expenses' => $monthlyExpenses[$monthKey] ?? 0
            ];

            $currentDate->addMonth();
        }

        $monthlyData = array_values($months);

        // Récupérer les classes pour le filtre
        $classes = $this->my_class->all();

        return view('pages.support_team.financial_dashboard.expenses', compact(
            'startDate',
            'endDate',
            'period',
            'categoryFilter',
            'expenses',
            'totalExpenses',
            'expensesByCategory',
            'monthlyData',
            'categories',
            'classes'
        ));
    }

    /**
     * Exporte les données du tableau de bord en PDF
     */
    public function exportPdf(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $classId = $request->input('class_id');
        $paymentType = $request->input('payment_type');

        // Convertir les dates en objets Carbon
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        // Récupérer les données nécessaires
        $monthlyData = $this->getMonthlyData($startDateCarbon, $endDateCarbon);
        $revenueByCategory = $this->getRevenueByCategory();
        $expensesByCategory = $this->getExpensesByCategory();
        $revenueByClass = $this->getRevenueByClass();
        $latePaymentsByClass = $this->getLatePaymentsByClass();

        // Calculer les totaux
        $totalRevenue = array_sum(array_column($monthlyData, 'revenue'));
        $totalExpenses = array_sum(array_column($monthlyData, 'expenses'));
        $cashBalance = $totalRevenue - $totalExpenses;

        // Calculer les moyennes mensuelles
        $daysInPeriod = $startDateCarbon->diffInDays($endDateCarbon) + 1;
        $monthsInPeriod = max(1, round($daysInPeriod / 30, 1));
        $avgMonthlyRevenue = $totalRevenue / $monthsInPeriod;
        $avgMonthlyExpenses = $totalExpenses / $monthsInPeriod;

        // Calculer le taux de recouvrement
        $expectedPayments = PaymentRecord::where('payment_records.year', $this->year)
            ->when($classId, function ($query) use ($classId) {
                return $query->whereHas('student.student_record', function ($q) use ($classId) {
                    $q->where('my_class_id', $classId);
                });
            })
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query->where('payment_id', $paymentType);
            })
            ->join('payments', 'payment_records.payment_id', '=', 'payments.id')
            ->sum('payments.amount');

        $recoveryRate = $expectedPayments > 0 ? ($totalRevenue / $expectedPayments) * 100 : 0;

        // Récupérer les classes et les types de paiement pour les filtres
        $classes = $this->my_class->all();
        $paymentTypes = Payment::where('year', $this->year)->get();

        // Récupérer la classe et le type de paiement sélectionnés
        $selectedClass = null;
        $selectedPaymentType = null;

        if ($classId) {
            $selectedClass = $classes->where('id', $classId)->first();
        }

        if ($paymentType) {
            $selectedPaymentType = $paymentTypes->where('id', $paymentType)->first();
        }

        // Générer les alertes
        $alerts = $this->generateAlerts($monthlyData, $latePaymentsByClass);

        // Préparer les données pour la vue PDF
        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateCarbon' => $startDateCarbon,
            'endDateCarbon' => $endDateCarbon,
            'selectedClass' => $selectedClass,
            'selectedPaymentType' => $selectedPaymentType,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'cashBalance' => $cashBalance,
            'avgMonthlyRevenue' => $avgMonthlyRevenue,
            'avgMonthlyExpenses' => $avgMonthlyExpenses,
            'recoveryRate' => $recoveryRate,
            'monthlyData' => $monthlyData,
            'revenueByCategory' => $revenueByCategory,
            'expensesByCategory' => $expensesByCategory,
            'revenueByClass' => $revenueByClass,
            'latePaymentsByClass' => $latePaymentsByClass,
            'alerts' => $alerts,
            'year' => $this->year
        ];

        // Générer le PDF
        $pdf = PDF::loadView('pages.support_team.financial_dashboard.pdf_export', $data);
        $pdf->setPaper('a4', 'landscape');

        // Télécharger le PDF
        return $pdf->download('tableau_de_bord_financier_' . date('Y-m-d') . '.pdf');
    }
}
