<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Liste des migrations à marquer comme exécutées
$migrations = [
    '2025_04_24_165345_modify_piece_column_in_decaissements_table',
    '2025_04_25_000000_add_methode_to_receipts_table',
    '2025_04_25_041205_add_details_bancaires_to_decaissements_table',
    '2025_04_25_063500_rename_statut_to_status_in_decaissements_table',
    '2025_04_27_082857_add_missing_fields_to_decaissements_table',
    '2025_04_27_083834_add_year_to_decaissements_table',
    '2025_04_27_084659_add_only_year_to_decaissements_table',
    '2025_04_27_085455_recreate_year_column_in_decaissements_table',
    '2025_04_27_160800_drop_decaissements_table',
    '2025_04_27_160856_create_decaissements_table',
    '2025_04_27_161811_recreate_decaissements_table',
    '2025_04_30_000000_add_new_fields_to_decaissements_table',
    '2025_05_01_000000_add_payment_journal_fields_to_receipts_table',
    '2025_05_02_000000_create_projets_table',
    '2025_05_02_000001_add_projet_id_to_decaissements_table',
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