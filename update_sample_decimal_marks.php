<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Updating marks to have decimal values for testing...\n";
    
    // First, let's see what marks exist for the student "Andry RABE"
    $marks = DB::table('marks')
        ->join('users', 'marks.student_id', '=', 'users.id')
        ->where('users.name', 'like', '%Andry%')
        ->select('marks.*', 'users.name as student_name')
        ->get();
    
    echo "Found " . $marks->count() . " marks for Andry\n";
    
    if ($marks->count() > 0) {
        // Update the first few marks with decimal values
        $sampleDecimalMarks = [
            28.50, 10.75, 17.25, 15.80, 12.30, 19.60, 11.25, 16.90, 14.45
        ];
        
        $index = 0;
        foreach ($marks->take(9) as $mark) {
            if ($index < count($sampleDecimalMarks)) {
                $newValue = $sampleDecimalMarks[$index];
                
                // Update tex1 field with decimal value
                DB::table('marks')
                    ->where('id', $mark->id)
                    ->update(['tex1' => $newValue]);
                
                echo "Updated mark ID {$mark->id} - tex1 = {$newValue}\n";
                $index++;
            }
        }
        
        echo "\nSample marks updated with decimal values!\n";
        echo "You can now test the weighted grades at: http://127.0.0.1:8001/marks/weighted-grades/1/8/8\n";
        
        // Let's also verify one of the updated marks
        $updatedMark = DB::table('marks')->where('id', $marks->first()->id)->first();
        echo "\nVerification - Mark ID {$updatedMark->id}: tex1 = {$updatedMark->tex1}\n";
        
    } else {
        echo "No marks found for Andry RABE to update.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}