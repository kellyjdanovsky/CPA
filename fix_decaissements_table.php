<?php

// Charger l'environnement Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Vérifier si la colonne 'year' existe
$hasYearColumn = Schema::hasColumn('decaissements', 'year');
echo "La colonne 'year' existe-t-elle ? " . ($hasYearColumn ? 'Oui' : 'Non') . "\n";

// Afficher la structure de la table
$columns = DB::select('SHOW COLUMNS FROM decaissements');
echo "Structure de la table 'decaissements' :\n";
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

// Si la colonne 'year' n'existe pas, l'ajouter
if (!$hasYearColumn) {
    echo "Ajout de la colonne 'year'...\n";
    DB::statement('ALTER TABLE decaissements ADD COLUMN year VARCHAR(255) NULL');
    echo "Colonne 'year' ajoutée avec succès.\n";
} else {
    // Si la colonne existe mais pose problème, la recréer
    echo "Recréation de la colonne 'year'...\n";
    DB::statement('ALTER TABLE decaissements MODIFY COLUMN year VARCHAR(255) NULL');
    echo "Colonne 'year' recréée avec succès.\n";
}

echo "Opération terminée.\n";
