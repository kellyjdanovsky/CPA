# OpenSpec Design: Improve Marks Print Functionality for A4 Landscape

## 🎨 Design Overview

This document provides detailed design specifications for improving the marks print functionality to ensure proper A4 landscape orientation and professional print output. The design focuses on creating a clean, readable, and professional report card layout optimized for printing.

## 📐 Layout Specifications

### Page Setup
- **Orientation**: A4 Landscape (297mm × 210mm)
- **Margins**: 1cm on all sides
- **Printable Area**: 277mm × 190mm
- **Color Mode**: Black and white for printing
- **Font Family**: Poppins (print-optimized)

### Grid System
```
┌─────────────────────────────────────────────────────────────┐
│                     HEADER SECTION                          │
│ School Logo | School Name | Address | Contact Info          │
├─────────────────────────────────────────────────────────────┤
│                     TITLE SECTION                           │
│ BULLETIN DE NOTES (CLASS TYPE) - EXAM NAME - YEAR           │
├─────────────────────────────────────────────────────────────┤
│                     STUDENT DETAILS                         │
│ [Photo] | Name | ID | Class | Section | Year | Gender       │
├─────────────────────────────────────────────────────────────┤
│                     MARKS TABLE                             │
│ Subject | DS1 | DS2 | Exam | Avg | Coef | Total | Grade    │
├─────────────────────────────────────────────────────────────┤
│                     SUMMARY SECTION                         │
│ Total Points | Average | Class Rank | Position | Mention    │
├─────────────────────────────────────────────────────────────┤
│                     FOOTER SECTION                          │
│ Director's Comment | Teacher Signatures | Date              │
└─────────────────────────────────────────────────────────────┘
```

## 🎨 Visual Design System

### Color Palette (Print-Optimized)
- **Primary**: #000000 (Black for text and borders)
- **Secondary**: #666666 (Dark gray for secondary text)
- **Accent**: #333333 (Medium gray for emphasis)
- **Background**: #FFFFFF (White for clean printing)
- **Table Headers**: #000000 (Black background, white text)

### Typography Scale
```
Print Sizes:
- H1: 16px (Page Title)
- H2: 14px (Section Headers)
- H3: 12px (Sub-section Headers)
- Body: 11px (Main content)
- Small: 10px (Captions and metadata)

Screen Sizes:
- H1: 18px (Page Title)
- H2: 16px (Section Headers)
- H3: 14px (Sub-section Headers)
- Body: 13px (Main content)
- Small: 12px (Captions and metadata)
```

### Spacing System
```
Print Spacing:
- Section Padding: 15px
- Element Margin: 10px
- Line Height: 1.4
- Column Gap: 8px
- Table Cell Padding: 8px 5px

Screen Spacing:
- Section Padding: 20px
- Element Margin: 15px
- Line Height: 1.5
- Column Gap: 12px
- Table Cell Padding: 12px 8px
```

## 📋 Component Specifications

### 1. Header Component
```html
<div class="print-header">
    <div class="school-logo">
        <img src="{{ $s['logo'] }}" alt="School Logo">
    </div>
    <div class="school-info">
        <h1 class="school-title">{{ strtoupper(Qs::getSetting('system_name')) }}</h1>
        <p class="school-address">{{ ucwords($s['address']) }}</p>
        <p class="school-contact">{{ $s['phone'] }} | {{ $s['email'] }}</p>
    </div>
</div>
```

**CSS**:
```css
.print-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 2px solid #000;
    margin-bottom: 15px;
}

.school-logo img {
    max-height: 60px;
    width: auto;
}

.school-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #000;
}

.school-address {
    font-size: 11px;
    margin: 2px 0;
    color: #666;
}
```

### 2. Title Component
```html
<div class="print-title">
    <h2>BULLETIN DE NOTES ({{ strtoupper($class_type->name) }})</h2>
    <p>{{ strtoupper($exam->name) }} - {{ $year }}</p>
</div>
```

**CSS**:
```css
.print-title {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccc;
}

.print-title h2 {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 5px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.print-title p {
    font-size: 12px;
    margin: 0;
    color: #666;
}
```

### 3. Student Details Component
```html
<div class="student-details">
    <div class="student-photo">
        <img src="{{ $sr->user->photo }}" alt="Student Photo">
    </div>
    <div class="student-info">
        <div class="detail-item">
            <strong>Nom:</strong> {{ $sr->user->name }}
        </div>
        <div class="detail-item">
            <strong>Matricule:</strong> {{ $sr->code }}
        </div>
        <div class="detail-item">
            <strong>Classe:</strong> {{ $my_class->name }}
        </div>
        <div class="detail-item">
            <strong>Section:</strong> {{ $section->name }}
        </div>
        <div class="detail-item">
            <strong>Année:</strong> {{ $year }}
        </div>
        <div class="detail-item">
            <strong>Sexe:</strong> {{ $sr->gender }}
        </div>
    </div>
</div>
```

**CSS**:
```css
.student-details {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.student-photo img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #000;
}

.student-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.detail-item {
    background: white;
    padding: 8px;
    border-radius: 4px;
    border-left: 3px solid #333;
    font-size: 11px;
}

.detail-item strong {
    color: #000;
    font-weight: 600;
}
```

### 4. Marks Table Component
```html
<table class="marks-table">
    <thead>
        <tr>
            <th>Matière</th>
            <th>DS1 (20)</th>
            <th>DS2 (20)</th>
            <th>Examen (20)</th>
            <th>Moyenne (/20)</th>
            <th>Coeff</th>
            <th>Total</th>
            <th>Appréciation</th>
        </tr>
    </thead>
    <tbody>
        @foreach($marks as $mark)
        <tr>
            <td class="subject-name">{{ $mark->subject->name }}</td>
            <td>{{ NumberFormat::formatWithoutRounding($mark->t1, 2) }}</td>
            <td>{{ NumberFormat::formatWithoutRounding($mark->t2, 2) }}</td>
            <td>{{ NumberFormat::formatWithoutRounding($mark->exm, 2) }}</td>
            <td class="grade">{{ NumberFormat::formatWithoutRounding($mark->ave, 2) }}</td>
            <td>{{ $mark->subject->coef }}</td>
            <td class="grade">{{ NumberFormat::formatWithoutRounding($mark->ave * $mark->subject->coef, 2) }}</td>
            <td>{{ $mark->remark }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
```

**CSS**:
```css
.marks-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 11px;
    background: white;
}

.marks-table th {
    background: #000;
    color: white;
    padding: 8px 5px;
    text-align: center;
    font-weight: 600;
    font-size: 12px;
    border: 1px solid #000;
}

.marks-table td {
    padding: 8px 5px;
    text-align: center;
    border: 1px solid #ddd;
    vertical-align: middle;
}

.marks-table tbody tr:nth-child(even) {
    background: #f8f9fa;
}

.marks-table tbody tr:hover {
    background: #e9ecef;
}

.subject-name {
    text-align: left !important;
    font-weight: 600;
    color: #000;
}

.grade {
    font-weight: 600;
    color: #000;
}
```

### 5. Summary Component
```html
<div class="summary-section">
    <div class="summary-item">
        <h4>Total des Points</h4>
        <p>{{ NumberFormat::formatWithoutRounding($sr->total_points, 2) }}</p>
    </div>
    <div class="summary-item">
        <h4>Moyenne Générale</h4>
        <p>{{ NumberFormat::formatWithoutRounding($sr->ave, 2) }}/20</p>
    </div>
    <div class="summary-item">
        <h4>Position</h4>
        <p>{{ $sr->position }}/{{ $total_students }}</p>
    </div>
    <div class="summary-item">
        <h4>Mention</h4>
        <p>{{ $sr->mention }}</p>
    </div>
</div>
```

**CSS**:
```css
.summary-section {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.summary-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    border-top: 3px solid #333;
}

.summary-item h4 {
    font-size: 11px;
    color: #000;
    margin: 0 0 8px 0;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-item p {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    color: #000;
}
```

### 6. Footer Component
```html
<div class="footer-section">
    <div class="comments">
        <h4>Commentaires du Directeur</h4>
        <p>{{ $director_comment }}</p>
    </div>
    <div class="signatures">
        <h4>Signatures</h4>
        <div class="signature-line">Directeur</div>
        <div class="signature-line">Prof Principal</div>
        <div class="signature-line">Parent</div>
    </div>
</div>
```

**CSS**:
```css
.footer-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 2px solid #000;
}

.comments h4, .signatures h4 {
    font-size: 12px;
    color: #000;
    margin-top: 0;
    margin-bottom: 10px;
    font-weight: 600;
    padding-bottom: 5px;
    border-bottom: 1px solid #333;
}

.comments p {
    font-size: 11px;
    line-height: 1.4;
    margin: 0 0 10px 0;
    color: #666;
}

.signature-line {
    height: 40px;
    border-bottom: 1px solid #000;
    margin: 8px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    font-size: 10px;
    font-weight: 500;
}
```

## 🖨️ Print-Specific Styling

### Media Print Rules
```css
@media print {
    @page {
        size: A4 landscape;
        margin: 1cm;
    }
    
    * {
        color: #000 !important;
        background: transparent !important;
    }
    
    .no-print {
        display: none;
    }
    
    .print-container {
        box-shadow: none;
        border-radius: 0;
        padding: 0;
    }
    
    .marks-table {
        font-size: 10px;
    }
    
    .marks-table th, .marks-table td {
        padding: 6px 4px;
        border: 1px solid #000 !important;
    }
    
    .marks-table thead th {
        background: #000 !important;
        color: #fff !important;
    }
    
    .student-details {
        grid-template-columns: 80px 1fr;
    }
    
    .student-info {
        grid-template-columns: 1fr;
    }
    
    .summary-section {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .summary-item {
        padding: 10px;
    }
    
    .footer-section {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}
```

### Screen-Specific Styling
```css
@media screen {
    .print-container {
        max-width: 1100px;
        margin: 0 auto;
        background: white;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .print-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
    }
    
    .print-title {
        background: #f8f9fa;
    }
    
    .student-details {
        background: #f8f9fa;
    }
    
    .marks-table {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .summary-item {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .footer-section {
        background: #f8f9fa;
    }
}
```

## 📱 Responsive Design

### Mobile Breakpoints
```css
@media (max-width: 768px) {
    .student-details {
        grid-template-columns: 1fr;
    }
    
    .student-info {
        grid-template-columns: 1fr;
    }
    
    .summary-section {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .footer-section {
        grid-template-columns: 1fr;
    }
    
    .marks-table {
        font-size: 10px;
    }
    
    .marks-table th, .marks-table td {
        padding: 6px 3px;
    }
}

@media (max-width: 480px) {
    .summary-section {
        grid-template-columns: 1fr;
    }
    
    .marks-table {
        font-size: 9px;
    }
    
    .marks-table th, .marks-table td {
        padding: 4px 2px;
    }
}
```

## 🎯 Design Principles

1. **Readability**: Ensure all text is readable at standard print sizes
2. **Professionalism**: Maintain a clean, professional appearance suitable for official documents
3. **Consistency**: Use consistent spacing, typography, and styling throughout
4. **Accessibility**: Ensure high contrast for readability
5. **Efficiency**: Optimize layout to maximize use of A4 landscape space
6. **Flexibility**: Design to accommodate varying amounts of data

---

**Design Status**: Complete  
**Version**: 1.0  
**Created**: 2025-10-18  
**Review Status**: Ready for Implementation