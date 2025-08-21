<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestStatisticsController extends Controller
{
    public function getDetailedStatistics(Request $request)
    {
        try {
            // Test simple avec des données factices
            return response()->json([
                'success' => true,
                'data' => [
                    'total_students' => 10,
                    'class_name' => 'Test - Toutes les classes',
                    'statistics' => [
                        'genre' => [
                            ['category' => 'Masculin', 'count' => 6, 'percentage' => 60],
                            ['category' => 'Féminin', 'count' => 4, 'percentage' => 40]
                        ],
                        'statut' => [
                            ['category' => 'Normal', 'count' => 8, 'percentage' => 80],
                            ['category' => 'ADRA', 'count' => 2, 'percentage' => 20],
                            ['category' => 'TEAM3', 'count' => 0, 'percentage' => 0]
                        ],
                        'type_etudiant' => [
                            ['category' => 'Nouveau', 'count' => 7, 'percentage' => 70],
                            ['category' => 'Ancien', 'count' => 3, 'percentage' => 30]
                        ],
                        'statut_academique' => [
                            ['category' => 'Passant', 'count' => 9, 'percentage' => 90],
                            ['category' => 'Redoublant', 'count' => 1, 'percentage' => 10]
                        ],
                        'religion' => [
                            ['category' => 'FLM', 'count' => 3, 'percentage' => 30],
                            ['category' => 'FJKM', 'count' => 2, 'percentage' => 20],
                            ['category' => 'Catholique', 'count' => 2, 'percentage' => 20],
                            ['category' => 'Adventiste', 'count' => 1, 'percentage' => 10],
                            ['category' => 'Islam', 'count' => 1, 'percentage' => 10],
                            ['category' => 'Judaïsme', 'count' => 0, 'percentage' => 0],
                            ['category' => 'Apokalipsy', 'count' => 0, 'percentage' => 0],
                            ['category' => 'Autres', 'count' => 1, 'percentage' => 10],
                            ['category' => 'Non renseigné', 'count' => 0, 'percentage' => 0]
                        ],
                        'tranche_age' => [
                            ['category' => '6-8 ans', 'count' => 1, 'percentage' => 10],
                            ['category' => '8-10 ans', 'count' => 2, 'percentage' => 20],
                            ['category' => '10-12 ans', 'count' => 3, 'percentage' => 30],
                            ['category' => '12-14 ans', 'count' => 2, 'percentage' => 20],
                            ['category' => '14-16 ans', 'count' => 1, 'percentage' => 10],
                            ['category' => '16-18 ans', 'count' => 1, 'percentage' => 10],
                            ['category' => '18-20 ans', 'count' => 0, 'percentage' => 0],
                            ['category' => '20+ ans', 'count' => 0, 'percentage' => 0],
                            ['category' => 'Non calculable', 'count' => 0, 'percentage' => 0]
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
