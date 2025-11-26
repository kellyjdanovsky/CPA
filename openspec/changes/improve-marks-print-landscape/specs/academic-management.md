# OpenSpec Specification: Academic Management - A4 Landscape Print Functionality

## 📚 Academic Management Overview

This specification document outlines the academic management requirements for implementing A4 landscape print functionality for student marks and report cards. The solution must integrate seamlessly with the existing academic management system while providing professional-grade print output.

## 🎯 Academic Requirements

### 1. Report Card Generation
**Objective**: Generate professional report cards in A4 landscape format

**Functional Requirements**:
- [ ] Generate individual student report cards
- [ ] Generate class-wide report cards
- [ ] Support multiple exam periods (DS1, DS2, Exam)
- [ ] Include weighted grade calculations
- [ ] Display student rankings and positions

**Data Requirements**:
```php
// Required Student Data Fields
- student_id (Primary Key)
- user_id (Foreign Key to Users)
- code (Student Registration Code)
- name (Student Full Name)
- gender (Student Gender)
- photo (Student Photo URL)
- my_class_id (Class Assignment)
- section_id (Section Assignment)
- year (Academic Year)
- total_points (Calculated Total)
- ave (Calculated Average)
- position (Class Position)
- mention (Performance Mention)

// Required Marks Data Fields
- mark_id (Primary Key)
- student_id (Foreign Key)
- subject_id (Foreign Key)
- exam_id (Foreign Key)
- t1 (First Term Mark)
- t2 (Second Term Mark)
- exm (Final Exam Mark)
- ave (Calculated Average)
- remark (Teacher Comments)
- coefficient (Subject Weight)

// Required Class Data Fields
- class_id (Primary Key)
- name (Class Name)
- class_type_id (Class Type)
- section_id (Section Assignment)
```

### 2. Grade Calculation System
**Objective**: Ensure accurate grade calculations for print output

**Calculation Requirements**:
```php
// Individual Subject Average Calculation
function calculateSubjectAverage($t1, $t2, $exm) {
    $values = [$t1, $t2, $exm];
    $sum = array_sum($values);
    $count = count(array_filter($values, function($value) { return $value > 0; }));
    return $count > 0 ? $sum / $count : 0;
}

// Weighted Grade Calculation
function calculateWeightedGrade($average, $coefficient) {
    return $average * $coefficient;
}

// Overall Average Calculation
function calculateOverallAverage($weighted_grades, $total_coefficients) {
    return $total_coefficients > 0 ? array_sum($weighted_grades) / $total_coefficients : 0;
}

// Performance Mention Calculation
function calculateMention($average) {
    if ($average >= 16) return 'Très Bien';
    if ($average >= 14) return 'Bien';
    if ($average >= 12) return 'Assez Bien';
    if ($average >= 10) return 'Passable';
    return 'Insuffisant';
}
```

### 3. Academic Year Management
**Objective**: Support multiple academic years and sessions

**Year Management Requirements**:
- [ ] Current session tracking
- [ ] Historical data preservation
- [ ] Year-based data filtering
- [ ] Session transition handling
- [ ] Academic calendar integration

**Data Structure**:
```php
// Academic Year Model
class Session extends Model {
    protected $fillable = [
        'name',           // "2024-2025"
        'start_date',     // Academic year start
        'end_date',       // Academic year end
        'is_current',     // Current session flag
        'status'          // Active, Archived, etc.
    ];
}
```

## 📊 Data Management Specifications

### 1. Database Schema Requirements
**Objective**: Ensure proper database structure for print functionality

**Required Tables**:
```sql
-- Students Table
CREATE TABLE student_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    my_class_id INT NOT NULL,
    section_id INT NOT NULL,
    year VARCHAR(20) NOT NULL,
    total_points DECIMAL(10,2) DEFAULT 0,
    ave DECIMAL(10,2) DEFAULT 0,
    position INT DEFAULT 0,
    mention VARCHAR(50) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (my_class_id) REFERENCES my_classes(id),
    FOREIGN KEY (section_id) REFERENCES sections(id)
);

-- Marks Table
CREATE TABLE marks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    exam_id INT NOT NULL,
    t1 DECIMAL(5,2) DEFAULT 0,
    t2 DECIMAL(5,2) DEFAULT 0,
    exm DECIMAL(5,2) DEFAULT 0,
    ave DECIMAL(5,2) DEFAULT 0,
    remark TEXT,
    year VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student_records(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (exam_id) REFERENCES exams(id),
    UNIQUE KEY unique_mark (student_id, subject_id, exam_id, year)
);

-- Exams Table
CREATE TABLE exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    term VARCHAR(20) NOT NULL,  // "DS1", "DS2", "Exam"
    year VARCHAR(20) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. Data Validation Rules
**Objective**: Ensure data integrity for print output

**Validation Requirements**:
```php
// Mark Validation
$rules = [
    't1' => 'required|numeric|between:0,20',
    't2' => 'required|numeric|between:0,20',
    'exm' => 'required|numeric|between:0,20',
    'remark' => 'nullable|string|max:500',
    'year' => 'required|string|size:9'  // "2024-2025"
];

// Student Record Validation
$studentRules = [
    'code' => 'required|string|max:50|unique:student_records,code',
    'my_class_id' => 'required|exists:my_classes,id',
    'section_id' => 'required|exists:sections,id',
    'year' => 'required|string|size:9'
];
```

### 3. Data Security and Privacy
**Objective**: Protect sensitive student data in print output

**Security Requirements**:
- [ ] Role-based access control for print functionality
- [ ] Student data encryption at rest
- [ ] Audit logging for print operations
- [ ] Data retention policies
- [ ] GDPR compliance for student information

**Access Control**:
```php
// Middleware for Print Access
class CanPrintMarks {
    public function handle($request, Closure $next) {
        if (!auth()->check() || !auth()->user()->can('print_marks')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }
        return $next($request);
    }
}
```

## 🖨️ Print Output Specifications

### 1. Print Format Requirements
**Objective**: Standardize print output format

**Format Specifications**:
- **Paper Size**: A4 (210mm × 297mm) Landscape
- **Orientation**: Landscape (297mm × 210mm)
- **Margins**: 1cm on all sides
- **Font**: Poppins (print-optimized)
- **Language**: French (as per existing system)
- **Color Mode**: Black and white for printing

### 2. Print Content Structure
**Objective**: Define standardized content for report cards

**Content Sections**:
```php
// Report Card Structure
$reportCard = [
    'header' => [
        'school_logo' => $school->logo,
        'school_name' => $school->name,
        'school_address' => $school->address,
        'school_contact' => $school->phone . ' | ' . $school->email
    ],
    'title' => [
        'document_type' => 'BULLETIN DE NOTES',
        'class_type' => $classType->name,
        'exam_name' => $exam->name,
        'academic_year' => $year
    ],
    'student_info' => [
        'photo' => $student->photo,
        'name' => $student->name,
        'code' => $student->code,
        'class' => $class->name,
        'section' => $section->name,
        'gender' => $student->gender
    ],
    'academic_performance' => [
        'marks' => $marks,
        'summary' => [
            'total_points' => $student->total_points,
            'average' => $student->ave,
            'position' => $student->position,
            'mention' => $student->mention
        ]
    ],
    'footer' => [
        'director_comment' => $directorComment,
        'signatures' => [
            'director' => 'Directeur',
            'teacher' => 'Prof Principal',
            'parent' => 'Parent'
        ],
        'date' => now()->format('d/m/Y')
    ]
];
```

### 3. Print Quality Standards
**Objective**: Ensure professional print quality

**Quality Requirements**:
- **Resolution**: 300 DPI for print output
- **Image Quality**: Optimized for black and white printing
- **Text Readability**: Minimum 10pt font size
- **Line Quality**: Crisp borders and lines
- **Layout Consistency**: Uniform spacing and alignment

## 🔧 Technical Implementation

### 1. Controller Implementation
**Objective**: Implement print functionality in controllers

**Controller Requirements**:
```php
// MarksController Print Method
public function printMarks($studentId, $examId)
{
    // Validate permissions
    if (!auth()->user()->can('print_marks')) {
        return redirect()->route('dashboard')->with('error', 'Access denied');
    }
    
    // Get student data
    $student = StudentRecord::with(['user', 'myClass', 'section'])
        ->findOrFail($studentId);
    
    // Get exam data
    $exam = Exam::findOrFail($examId);
    
    // Get marks data
    $marks = Mark::with(['subject', 'grade'])
        ->where('student_id', $studentId)
        ->where('exam_id', $examId)
        ->where('year', $this->currentYear)
        ->get();
    
    // Calculate summary
    $summary = $this->calculateSummary($marks);
    
    // Get school settings
    $settings = Setting::all()->pluck('description', 'type');
    
    // Return print view
    return view('pages.support_team.marks.print.sheet', compact(
        'student', 'exam', 'marks', 'summary', 'settings'
    ));
}

// Summary Calculation Method
private function calculateSummary($marks)
{
    $totalPoints = 0;
    $totalCoefficients = 0;
    $subjectCount = 0;
    
    foreach ($marks as $mark) {
        if ($mark->ave > 0) {
            $totalPoints += ($mark->ave * $mark->subject->coef);
            $totalCoefficients += $mark->subject->coef;
            $subjectCount++;
        }
    }
    
    $average = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : 0;
    $mention = $this->calculateMention($average);
    
    return [
        'total_points' => $totalPoints,
        'average' => $average,
        'subject_count' => $subjectCount,
        'mention' => $mention
    ];
}
```

### 2. View Implementation
**Objective**: Create optimized print views

**View Requirements**:
```php
// Print View Structure
@extends('layouts.print')

@section('content')
<div class="print-container">
    @include('pages.support_team.marks.print.header')
    @include('pages.support_team.marks.print.title')
    @include('pages.support_team.marks.print.student-info')
    @include('pages.support_team.marks.print.marks-table')
    @include('pages.support_team.marks.print.summary')
    @include('pages.support_team.marks.print.footer')
</div>

@include('pages.support_team.marks.print.print-css')
@endsection
```

### 3. CSS Implementation
**Objective**: Implement print-specific styling

**CSS Requirements**:
```css
/* Print-specific CSS */
@media print {
    @page {
        size: A4 landscape;
        margin: 1cm;
    }
    
    .print-container {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        box-shadow: none;
    }
    
    .marks-table {
        font-size: 10px;
        width: 100%;
    }
    
    .marks-table th {
        background: #000 !important;
        color: #fff !important;
        padding: 6px 4px;
    }
    
    .marks-table td {
        padding: 6px 4px;
        border: 1px solid #000 !important;
    }
}
```

## 📈 Performance Requirements

### 1. Response Time Requirements
**Objective**: Ensure fast print generation

**Performance Targets**:
- **Page Load Time**: < 3 seconds
- **Print Preview Generation**: < 2 seconds
- **Large Class Processing**: < 5 seconds (50+ students)
- **Memory Usage**: < 50MB per print operation

### 2. Database Optimization
**Objective**: Optimize database queries for print functionality

**Optimization Requirements**:
```php
// Optimized Query for Marks
$marks = Mark::with(['subject:id,name,coef', 'grade:id,name,remark'])
    ->select('id', 'student_id', 'subject_id', 't1', 't2', 'exm', 'ave', 'remark')
    ->where('student_id', $studentId)
    ->where('exam_id', $examId)
    ->where('year', $this->currentYear)
    ->orderBy('subject_id')
    ->get();

// Index Requirements
CREATE INDEX idx_marks_student_exam_year ON marks(student_id, exam_id, year);
CREATE INDEX idx_marks_year ON marks(year);
CREATE INDEX idx_student_records_year ON student_records(year);
```

### 3. Caching Strategy
**Objective**: Implement caching for improved performance

**Caching Requirements**:
```php
// Cache Print Data
public function getPrintData($studentId, $examId)
{
    $cacheKey = "print_marks_{$studentId}_{$examId}_{$this->currentYear}";
    
    return Cache::remember($cacheKey, now()->addHours(1), function() use ($studentId, $examId) {
        return $this->generatePrintData($studentId, $examId);
    });
}

// Cache Tags for Invalidation
Cache::tags(['print_data', 'marks', $this->currentYear])
    ->put($cacheKey, $data, now()->addHours(1));
```

## 🧪 Testing Requirements

### 1. Unit Testing
**Objective**: Test individual components

**Test Requirements**:
```php
// Grade Calculation Test
public function testGradeCalculation()
{
    $t1 = 15.5;
    $t2 = 16.0;
    $exm = 14.5;
    $coefficient = 3;
    
    $average = $this->marksService->calculateSubjectAverage($t1, $t2, $exm);
    $weighted = $this->marksService->calculateWeightedGrade($average, $coefficient);
    
    $this->assertEquals(15.3, $average);
    $this->assertEquals(45.9, $weighted);
}

// Print Data Generation Test
public function testPrintDataGeneration()
{
    $studentId = 1;
    $examId = 1;
    
    $printData = $this->printService->generatePrintData($studentId, $examId);
    
    $this->assertArrayHasKey('student_info', $printData);
    $this->assertArrayHasKey('academic_performance', $printData);
    $this->assertArrayHasKey('marks', $printData['academic_performance']);
}
```

### 2. Integration Testing
**Objective**: Test complete print workflow

**Test Scenarios**:
- [ ] Individual student report card generation
- [ ] Class-wide report card generation
- [ ] Print preview functionality
- [ ] Actual print output validation
- [ ] Error handling for missing data

### 3. Performance Testing
**Objective**: Validate performance requirements

**Performance Tests**:
- [ ] Load testing with multiple concurrent users
- [ ] Memory usage profiling
- [ ] Database query optimization
- [ ] Cache effectiveness testing

---

**Specification Status**: Complete  
**Version**: 1.0  
**Created**: 2025-10-18  
**Review Status**: Ready for Implementation