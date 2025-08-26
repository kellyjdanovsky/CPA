<?php
/**
 * Script to resolve migration conflicts for the new payment management tables
 * Usage: php resolve_migration_conflict.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Résolution des conflits de migration...\n\n";

// Tables to check
$tables = [
    'encaissements' => '2025_08_25_100000_create_encaissements_table',
    'recettes' => '2025_08_25_100001_create_recettes_table', 
    'decaissements' => '2025_08_25_100002_create_decaissements_table'
];

foreach ($tables as $tableName => $migrationFile) {
    echo "📋 Vérification de la table '$tableName'...\n";
    
    if (Schema::hasTable($tableName)) {
        echo "   ✅ Table '$tableName' existe déjà\n";
        
        // Check if migration is recorded
        $migrationExists = DB::table('migrations')
            ->where('migration', $migrationFile)
            ->exists();
            
        if ($migrationExists) {
            echo "   ℹ️  Migration déjà enregistrée\n";
        } else {
            echo "   🔄 Enregistrement de la migration...\n";
            
            // Mark migration as run
            DB::table('migrations')->insert([
                'migration' => $migrationFile,
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            
            echo "   ✅ Migration enregistrée avec succès\n";
        }
    } else {
        echo "   ❌ Table '$tableName' n'existe pas - migration nécessaire\n";
    }
    
    echo "\n";
}

echo "🎉 Résolution terminée!\n";
echo "\nVous pouvez maintenant exécuter :\n";
echo "php artisan migrate\n";

?>