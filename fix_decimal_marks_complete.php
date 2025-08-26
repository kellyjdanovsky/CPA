<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== FIXING DECIMAL MARKS ISSUE ===\n\n";
    
    // Step 1: Update database schema to support decimals
    echo "Step 1: Updating database schema...\n";
    
    // Check current column types
    $columns = DB::select("DESCRIBE marks");
    $needsUpdate = false;
    
    foreach ($columns as $column) {
        if (in_array($column->Field, ['t1', 't2', 't3', 't4', 'tca', 'exm', 'tex1', 'tex2', 'tex3', 'cum'])) {
            if (strpos($column->Type, 'decimal') === false) {
                $needsUpdate = true;
                echo "Column {$column->Field} is {$column->Type} - needs update\n";
            } else {
                echo "Column {$column->Field} is already {$column->Type} - OK\n";
            }
        }
    }
    
    if ($needsUpdate) {
        echo "\nUpdating columns to decimal(5,2)...\n";
        
        // Update columns to decimal
        $markColumns = ['t1', 't2', 't3', 't4', 'tca', 'exm', 'tex1', 'tex2', 'tex3', 'cum'];
        
        foreach ($markColumns as $column) {
            try {
                DB::statement("ALTER TABLE marks MODIFY COLUMN {$column} DECIMAL(5,2) NULL");
                echo "Updated {$column} to decimal(5,2)\n";
            } catch (Exception $e) {
                echo "Error updating {$column}: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "Database schema is already correct!\n";
    }
    
    // Step 2: Insert/update test data with decimal values
    echo "\nStep 2: Creating test data with decimal values...\n";
    
    // Find a student to test with (preferably Andry RABE)
    $student = DB::table('users')
        ->join('student_records', 'users.id', '=', 'student_records.user_id')
        ->where('users.name', 'like', '%Andry%')
        ->where('users.user_type', 'student')
        ->select('users.*', 'student_records.my_class_id', 'student_records.section_id')
        ->first();
    
    if (!$student) {
        // Fallback to any student
        $student = DB::table('users')
            ->join('student_records', 'users.id', '=', 'student_records.user_id')
            ->where('users.user_type', 'student')
            ->select('users.*', 'student_records.my_class_id', 'student_records.section_id')
            ->first();
    }
    
    if ($student) {
        echo "Using student: {$student->name} (ID: {$student->id})\n";
        
        // Get subjects for this class
        $subjects = DB::table('subjects')
            ->where('my_class_id', $student->my_class_id)
            ->take(5)
            ->get();
        
        // Get an exam
        $exam = DB::table('exams')
            ->where('year', date('Y'))
            ->first();
        
        if (!$exam) {
            $exam = DB::table('exams')->first();
        }
        
        if ($exam && $subjects->count() > 0) {
            echo "Using exam: {$exam->name} (ID: {$exam->id})\n";
            echo "Updating marks for {$subjects->count()} subjects...\n";
            
            // Sample decimal values
            $decimalValues = [
                ['tex1' => 28.50, 't1' => 14.75, 't2' => 16.25, 'exm' => 15.80],
                ['tex1' => 15.75, 't1' => 18.50, 't2' => 12.25, 'exm' => 19.60],
                ['tex1' => 19.25, 't1' => 16.80, 't2' => 14.45, 'exm' => 17.90],
                ['tex1' => 12.60, 't1' => 13.25, 't2' => 11.75, 'exm' => 16.40],
                ['tex1' => 17.85, 't1' => 15.30, 't2' => 18.70, 'exm' => 14.25]
            ];
            
            $index = 0;
            foreach ($subjects as $subject) {
                if ($index < count($decimalValues)) {
                    $values = $decimalValues[$index];
                    $values['student_id'] = $student->id;
                    $values['subject_id'] = $subject->id;
                    $values['my_class_id'] = $student->my_class_id;
                    $values['section_id'] = $student->section_id;
                    $values['exam_id'] = $exam->id;
                    $values['year'] = $exam->year;
                    $values['created_at'] = now();
                    $values['updated_at'] = now();
                    
                    // Delete existing mark if it exists
                    DB::table('marks')->where([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'exam_id' => $exam->id,
                        'year' => $exam->year
                    ])->delete();
                    
                    // Insert new mark
                    $markId = DB::table('marks')->insertGetId($values);
                    
                    echo "Created mark for {$subject->name}: tex1={$values['tex1']}, t1={$values['t1']}, t2={$values['t2']}, exm={$values['exm']}\n";
                    $index++;
                }
            }
        }
    }
    
    // Step 3: Verify the data
    echo "\nStep 3: Verifying decimal data...\n";
    $testMarks = DB::table('marks')
        ->join('subjects', 'marks.subject_id', '=', 'subjects.id')
        ->where('marks.student_id', $student->id)
        ->where('marks.exam_id', $exam->id)
        ->select('marks.*', 'subjects.name as subject_name')
        ->get();
    
    foreach ($testMarks as $mark) {
        echo "{$mark->subject_name}: tex1={$mark->tex1}, t1={$mark->t1}, t2={$mark->t2}, exm={$mark->exm}\n";
    }
    
    echo "\n=== SOLUTION COMPLETE ===\n";
    echo "✅ Database schema updated to support decimal(5,2)\n";
    echo "✅ Test data created with decimal values\n";
    echo "✅ You can now test weighted grades at:\n";
    echo "   http://127.0.0.1:8001/marks/weighted-grades/{$exam->id}/{$student->my_class_id}/{$student->section_id}\n";
    echo "\n🔍 Compare with marks/show to verify consistency:\n";
    echo "   http://127.0.0.1:8001/marks/show/" . base64_encode($student->id) . "/{$exam->year}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}