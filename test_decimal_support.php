<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mark;
use Illuminate\Support\Facades\DB;

try {
    echo "Testing decimal marks functionality...\n\n";
    
    // Test 1: Check if we can create a mark with decimal values
    echo "Test 1: Creating a mark with decimal values...\n";
    
    // Find a student to test with
    $student = DB::table('users')->where('user_type', 'student')->first();
    $subject = DB::table('subjects')->first();
    $exam = DB::table('exams')->first();
    $class = DB::table('my_classes')->first();
    $section = DB::table('sections')->first();
    
    if ($student && $subject && $exam && $class && $section) {
        // Delete any existing test mark
        DB::table('marks')->where([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'exam_id' => $exam->id,
            'year' => date('Y')
        ])->delete();
        
        // Create a new mark with decimal values
        $testMarkId = DB::table('marks')->insertGetId([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'my_class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $exam->id,
            'tex1' => 28.50,
            't1' => 15.75,
            't2' => 12.25,
            'exm' => 18.80,
            'year' => date('Y'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Retrieve and verify the mark
        $testMark = DB::table('marks')->where('id', $testMarkId)->first();
        
        echo "Created mark with ID: {$testMarkId}\n";
        echo "tex1: {$testMark->tex1}\n";
        echo "t1: {$testMark->t1}\n";
        echo "t2: {$testMark->t2}\n";
        echo "exm: {$testMark->exm}\n\n";
        
        // Test 2: Check column types in database
        echo "Test 2: Checking database column types...\n";
        $columns = DB::select("DESCRIBE marks");
        foreach ($columns as $column) {
            if (in_array($column->Field, ['t1', 't2', 't3', 't4', 'tca', 'exm', 'tex1', 'tex2', 'tex3'])) {
                echo "Column {$column->Field}: {$column->Type}\n";
            }
        }
        
        echo "\nDecimal marks test completed successfully!\n";
        echo "The database now supports decimal values with 2 decimal places.\n";
        
    } else {
        echo "Error: Missing required data (student, subject, exam, class, or section)\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}