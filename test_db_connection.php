<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Encaissement;

try {
    echo "Testing database connection...\n";
    
    // Test database connection
    DB::connection()->getPdo();
    echo "✅ Database connected successfully\n";
    
    // Check if encaissements table exists
    $tables = DB::select("SHOW TABLES LIKE 'encaissements'");
    if (!empty($tables)) {
        echo "✅ Encaissements table exists\n";
        
        // Count records
        $count = DB::table('encaissements')->count();
        echo "📊 Total encaissements: {$count}\n";
        
        // Show recent records
        $recent = DB::table('encaissements')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'reference_encaissement', 'type_encaissement', 'montant_encaisse', 'date_encaissement']);
        
        echo "📝 Recent encaissements:\n";
        foreach ($recent as $enc) {
            echo "   - ID: {$enc->id}, Ref: {$enc->reference_encaissement}, Type: {$enc->type_encaissement}, Amount: {$enc->montant_encaisse}\n";
        }
        
    } else {
        echo "❌ Encaissements table does not exist\n";
        echo "Creating table...\n";
        
        // Run migration
        \Artisan::call('migrate', ['--path' => 'database/migrations/2025_08_25_100000_create_encaissements_table.php']);
        echo "✅ Migration executed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}