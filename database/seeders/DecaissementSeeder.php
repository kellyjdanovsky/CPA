<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Decaissement;
use App\User;
use App\Helpers\Qs;

class DecaissementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer un utilisateur admin pour créer les décaissements
        $admin = User::where('user_type', 'super_admin')->first();
        if (!$admin) {
            $admin = User::first();
        }

        if (!$admin) {
            $this->command->info('Aucun utilisateur trouvé. Veuillez créer un utilisateur d\'abord.');
            return;
        }

        $currentYear = Qs::getCurrentSession();

        // Créer des décaissements de test
        $decaissements = [
            [
                'date_paiement' => now()->subDays(10),
                'montant' => 150000,
                'motif' => 'Fournitures scolaires',
                'description' => 'Achat de cahiers et stylos pour les élèves',
                'beneficiaire' => 'Librairie TSARA',
                'methode_paiement' => 'espèces',
                'reference' => 'REF-001',
                'details_bancaires' => null,
                'status' => 'approuve',
                'created_by' => $admin->id,
                'year' => $currentYear,
            ],
            [
                'date_paiement' => now()->subDays(5),
                'montant' => 250000,
                'motif' => 'Réparation',
                'description' => 'Réparation du système électrique de la classe 1',
                'beneficiaire' => 'Électricien RABE',
                'methode_paiement' => 'chèque',
                'reference' => 'REF-002',
                'details_bancaires' => 'Chèque n° 123456',
                'status' => 'en_attente',
                'created_by' => $admin->id,
                'year' => $currentYear,
            ],
            [
                'date_paiement' => now()->subDays(3),
                'montant' => 75000,
                'motif' => 'Transport',
                'description' => 'Frais de transport pour sortie pédagogique',
                'beneficiaire' => 'Transport HERY',
                'methode_paiement' => 'virement',
                'reference' => 'REF-003',
                'details_bancaires' => 'Compte: 12345678901',
                'status' => 'approuve',
                'created_by' => $admin->id,
                'year' => $currentYear,
            ],
            [
                'date_paiement' => now()->subDays(1),
                'montant' => 500000,
                'motif' => 'Salaires',
                'description' => 'Salaire du mois de novembre pour le personnel',
                'beneficiaire' => 'Personnel enseignant',
                'methode_paiement' => 'virement',
                'reference' => 'REF-004',
                'details_bancaires' => 'Virements multiples',
                'status' => 'en_attente',
                'created_by' => $admin->id,
                'year' => $currentYear,
            ],
            [
                'date_paiement' => now(),
                'montant' => 120000,
                'motif' => 'Maintenance',
                'description' => 'Maintenance des ordinateurs de la salle informatique',
                'beneficiaire' => 'Informatique SOLO',
                'methode_paiement' => 'mobile_money',
                'reference' => 'REF-005',
                'details_bancaires' => 'MVola: 034 12 345 67',
                'status' => 'en_attente',
                'created_by' => $admin->id,
                'year' => $currentYear,
            ],
        ];

        foreach ($decaissements as $decaissement) {
            Decaissement::create($decaissement);
        }

        $this->command->info('5 décaissements de test créés avec succès.');
    }
}
