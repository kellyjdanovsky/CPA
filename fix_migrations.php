<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Liste des migrations à marquer comme exécutées
$migrations = [
    '2025_04_25_000000_add_methode_to_receipts_table',
    '2025_05_01_000000_add_payment_journal_fields_to_receipts_table',
    '2025_05_02_000000_create_projets_table',
];

// Récupérer le dernier batch
$lastBatch = DB::table('migrations')->max('batch');
$newBatch = $lastBatch + 1;

// Insérer les migrations dans la table
foreach ($migrations as $migration) {
    // Vérifier si la migration existe déjà
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    
    if (!$exists) {
        echo "Ajout de la migration: $migration\n";
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $newBatch
        ]);
    } else {
        echo "La migration $migration existe déjà\n";
    }
}

echo "Terminé!\n";