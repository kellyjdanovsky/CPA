# Proposal – Improvement of Report Card Design in marks/show and marks/print

## 🎯 Title

Improvement of the bulletin design in marks/show and marks/print for a professional, modern, and printable landscape layout (A4, black and white).

## 💡 Objective

Create a report card that is:

- Visually professional and harmonious
- Compatible with A4 landscape printing
- Readable in black and white
- Fits on a single page
- Maintains consistency of averages and comments

## 📍 Current Context

The current bulletin in marks/show and marks/print:

- Has a basic and inconsistent design
- Sometimes overflows the page
- Is not optimized for clean black and white printing

## ✅ Proposed Changes

Complete graphical redesign of the grades table.

Standardization of typography and spacing.

A4 landscape layout (CSS @page size: A4 landscape).

Automatic conversion to black and white for printing (@media print).

Removal of unnecessary elements (print date, redundant text).

Modernized and readable "Comments" block.

Synchronization of averages with /marks/weighted-grades.

## 📋 Tasks – Technical Task List

| # | Task | Details |
|---|---|---|
| 1.1 | Update marks/show.blade.php | New A4 landscape layout, table structure and clean design |
| 1.2 | Adapt marks/print.blade.php | Printable version without colors, margins and alignment |
| 1.3 | Add print CSS (@media print) | Force black and white, consistent margins, readable typography |
| 1.4 | Adjust column size and spacing | Proportional widths, alternating row colors |
| 1.5 | Remove unnecessary mentions | Print date, class average, etc. |
| 1.6 | Verify data consistency | Final average = /marks/weighted-grades |
| 1.7 | Test A4 landscape printing | Complete single page without overflow |

## 🧠 Spec Delta – Specification Changes

### 🔹 Before:

- Simple design with colors
- Sometimes truncated printing
- Poor readability of averages and comments

### 🔹 After:

#### Modernized Design:
- **Font**: Poppins / Roboto
- **Style**: Black and white, readable, balanced
- **Consistent spacing**

#### Printing:
```css
@page { size: A4 landscape; margin: 1cm; }
@media print { 
  * { 
    color: #000 !important; 
    background: transparent !important; 
  } 
}
```

#### Comments:
- Left-aligned, reduced font size
- Clear and spaced block

#### Data:
- Weighted average from /marks/weighted-grades
- Numbers only for totals, without text labels

## 🔍 Review – Verification & Validation

### Checklist:
- [ ] OpenSpec format validation
- [ ] A4 landscape compliance
- [ ] Verification of average correspondence with /marks/weighted-grades
- [ ] Black and white printed rendering test

### Recommended Reviewers:
- Academic Manager (validation of report card rendering)
- Laravel Front-end Developer
- Printing/Administration Manager