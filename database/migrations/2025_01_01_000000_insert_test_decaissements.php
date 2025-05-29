<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InsertTestDecaissements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe
        if (!Schema::hasTable('decaissements')) {
            return;
        }

        // Insérer des données de test
        $testData = [
            [
                'date_paiement' => '2024-11-20',
                'montant' => 150000.00,
                'motif' => 'Fournitures scolaires',
                'description' => 'Achat de cahiers et stylos pour les élèves',
                'beneficiaire' => 'Librairie TSARA',
                'methode_paiement' => 'espèces',
                'reference' => 'REF-001',
                'details_bancaires' => null,
                'status' => 'approuve',
                'created_by' => 1,
                'year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date_paiement' => '2024-11-25',
                'montant' => 250000.00,
                'motif' => 'Réparation',
                'description' => 'Réparation du système électrique de la classe 1',
                'beneficiaire' => 'Électricien RABE',
                'methode_paiement' => 'chèque',
                'reference' => 'REF-002',
                'details_bancaires' => 'Chèque n° 123456',
                'status' => 'en_attente',
                'created_by' => 1,
                'year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date_paiement' => '2024-11-27',
                'montant' => 75000.00,
                'motif' => 'Transport',
                'description' => 'Frais de transport pour sortie pédagogique',
                'beneficiaire' => 'Transport HERY',
                'methode_paiement' => 'virement',
                'reference' => 'REF-003',
                'details_bancaires' => 'Compte: 12345678901',
                'status' => 'approuve',
                'created_by' => 1,
                'year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date_paiement' => '2024-11-29',
                'montant' => 500000.00,
                'motif' => 'Salaires',
                'description' => 'Salaire du mois de novembre pour le personnel',
                'beneficiaire' => 'Personnel enseignant',
                'methode_paiement' => 'virement',
                'reference' => 'REF-004',
                'details_bancaires' => 'Virements multiples',
                'status' => 'en_attente',
                'created_by' => 1,
                'year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date_paiement' => '2024-11-30',
                'montant' => 120000.00,
                'motif' => 'Maintenance',
                'description' => 'Maintenance des ordinateurs de la salle informatique',
                'beneficiaire' => 'Informatique SOLO',
                'methode_paiement' => 'mobile_money',
                'reference' => 'REF-005',
                'details_bancaires' => 'MVola: 034 12 345 67',
                'status' => 'en_attente',
                'created_by' => 1,
                'year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Vérifier si des données existent déjà
        $existingCount = DB::table('decaissements')->count();
        
        if ($existingCount == 0) {
            DB::table('decaissements')->insert($testData);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer les données de test
        DB::table('decaissements')->whereIn('reference', [
            'REF-001', 'REF-002', 'REF-003', 'REF-004', 'REF-005'
        ])->delete();
    }
}
