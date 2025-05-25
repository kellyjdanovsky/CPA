<?php

// Ce script ajoute des projets par défaut à la base de données

// Charger l'environnement Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Projet;
use Illuminate\Support\Facades\DB;

// Vérifier si la table existe
if (!Schema::hasTable('projets')) {
    echo "La table 'projets' n'existe pas. Veuillez exécuter les migrations d'abord.\n";
    exit(1);
}

// Ajouter des projets par défaut
$projets = [
    [
        'nom' => 'Cantine scolaire',
        'description' => 'Gestion de la cantine scolaire',
        'date_debut' => now(),
        'date_fin' => now()->addYear(),
        'budget' => 5000000,
        'statut' => 'actif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nom' => 'Rénovation des salles de classe',
        'description' => 'Projet de rénovation des salles de classe',
        'date_debut' => now(),
        'date_fin' => now()->addMonths(6),
        'budget' => 3000000,
        'statut' => 'actif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nom' => 'Équipement informatique',
        'description' => 'Achat de nouveaux équipements informatiques',
        'date_debut' => now(),
        'date_fin' => now()->addMonths(3),
        'budget' => 2000000,
        'statut' => 'actif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nom' => 'Activités extrascolaires',
        'description' => 'Organisation d\'activités extrascolaires',
        'date_debut' => now(),
        'date_fin' => now()->addYear(),
        'budget' => 1500000,
        'statut' => 'actif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nom' => 'Fournitures scolaires',
        'description' => 'Achat de fournitures scolaires',
        'date_debut' => now(),
        'date_fin' => now()->addYear(),
        'budget' => 1000000,
        'statut' => 'actif',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

// Insérer les projets
foreach ($projets as $projet) {
    Projet::create($projet);
}

echo "Projets par défaut ajoutés avec succès.\n";
