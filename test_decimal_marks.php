<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\StudentRecord;

try {
    echo "Testing decimal marks insertion...\n";
    
    // Find a student and exam to test with
    $student = StudentRecord::first();
    $exam = Exam::first();
    $subject = Subject::first();
    
    if (!$student || !$exam || !$subject) {
        echo "Missing required data (student, exam, or subject)\n";
        exit;
    }
    
    echo "Student: {$student->user->name}\n";
    echo "Exam: {$exam->name}\n";
    echo "Subject: {$subject->name}\n";
    
    // Try to create a mark with decimal values
    $markData = [
        'student_id' => $student->user_id,
        'subject_id' => $subject->id,
        'my_class_id' => $student->my_class_id,
        'section_id' => $student->section_id,
        'exam_id' => $exam->id,
        'tex1' => 28.50,  // Decimal value
        'tex2' => 15.75,  // Decimal value
        'tex3' => 18.25,  // Decimal value
        'year' => date('Y')
    ];
    
    // Delete existing mark if it exists
    Mark::where('student_id', $markData['student_id'])
        ->where('subject_id', $markData['subject_id'])
        ->where('exam_id', $markData['exam_id'])
        ->where('year', $markData['year'])
        ->delete();
    
    // Create new mark
    $mark = Mark::create($markData);
    
    echo "Mark created successfully with ID: {$mark->id}\n";
    echo "tex1: {$mark->tex1}\n";
    echo "tex2: {$mark->tex2}\n";
    echo "tex3: {$mark->tex3}\n";
    
    // Verify the values are stored correctly
    $retrievedMark = Mark::find($mark->id);
    echo "Retrieved values:\n";
    echo "tex1: {$retrievedMark->tex1}\n";
    echo "tex2: {$retrievedMark->tex2}\n";
    echo "tex3: {$retrievedMark->tex3}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}