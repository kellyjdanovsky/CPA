<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "Checking marks table schema...\n";
    
    // Get table columns info
    $columns = DB::select("DESCRIBE marks");
    
    foreach ($columns as $column) {
        if (in_array($column->Field, ['t1', 't2', 't3', 't4', 'tca', 'exm', 'tex1', 'tex2', 'tex3', 'cum'])) {
            echo "Column: {$column->Field} - Type: {$column->Type} - Null: {$column->Null}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}