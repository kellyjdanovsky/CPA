<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mark;
use Illuminate\Support\Facades\DB;

try {
    echo "Updating marks to have decimal values...\n";
    
    // Update specific marks for testing - simulate realistic decimal grades
    $updates = [
        ['student_name' => 'Andry RABE', 'updates' => [
            'tex1' => 28.50,  // Changed from 29.00
            'tex2' => 10.75,  // Changed from 11.00  
            'tex3' => 17.25   // Changed from 18.00
        ]],
    ];
    
    foreach ($updates as $update) {
        // Find marks for this student
        $marks = DB::table('marks')
            ->join('users', 'marks.student_id', '=', 'users.id')
            ->where('users.name', 'like', '%' . $update['student_name'] . '%')
            ->select('marks.*')
            ->get();
            
        if ($marks->count() > 0) {
            echo "Found {$marks->count()} marks for {$update['student_name']}\n";
            
            // Update the first few marks with decimal values
            $count = 0;
            foreach ($marks as $mark) {
                if ($count >= 3) break; // Only update first 3 marks
                
                $updates_to_apply = [];
                if ($count == 0 && isset($update['updates']['tex1'])) {
                    $updates_to_apply['tex1'] = $update['updates']['tex1'];
                } else if ($count == 1 && isset($update['updates']['tex2'])) {
                    $updates_to_apply['tex1'] = $update['updates']['tex2'];
                } else if ($count == 2 && isset($update['updates']['tex3'])) {
                    $updates_to_apply['tex1'] = $update['updates']['tex3'];
                }
                
                if (!empty($updates_to_apply)) {
                    DB::table('marks')
                        ->where('id', $mark->id)
                        ->update($updates_to_apply);
                    echo "Updated mark ID {$mark->id} with decimal value\n";
                }
                $count++;
            }
        } else {
            echo "No marks found for {$update['student_name']}\n";
        }
    }
    
    echo "Update completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}