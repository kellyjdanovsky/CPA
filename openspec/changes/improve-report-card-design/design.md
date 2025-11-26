# Design Specifications – Report Card Improvement

## Design Philosophy

The new report card design follows a clean, professional approach optimized for both digital display and A4 landscape printing. The design prioritizes:

- **Readability**: Clear typography and adequate spacing
- **Professionalism**: Modern, academic aesthetic
- **Functionality**: Optimized for printing and data presentation
- **Consistency**: Unified design language across all views

## Visual Design System

### Color Palette
**Digital Display:**
- Primary: #2c3e50 (dark blue-gray for headers)
- Secondary: #34495e (slightly lighter for accents)
- Background: #ffffff (white)
- Text: #2c3e50 (dark gray for body text)
- Alternating rows: #f8f9fa (light gray)

**Print Mode:**
- All colors converted to black (#000000)
- Backgrounds removed (transparent)
- Text remains black for maximum readability

### Typography
**Font Stack:**
```css
font-family: 'Poppins', 'Roboto', sans-serif;
```

**Font Sizes:**
- Headers: 16px (bold)
- Student Information: 14px (normal)
- Table Headers: 13px (bold)
- Table Content: 12px (normal)
- Comments: 11px (italic)

**Line Heights:**
- Headers: 1.4
- Body Text: 1.6
- Table Content: 1.5

### Spacing System
**Margins:**
- Page: 1cm (print), 20px (digital)
- Section: 16px
- Element: 8px

**Padding:**
- Headers: 12px 16px
- Table Cells: 8px 12px
- Comments: 12px

## Layout Structure

### A4 Landscape Layout
```css
@page {
  size: A4 landscape;
  margin: 1cm;
}

@media print {
  * {
    color: #000 !important;
    background: transparent !important;
  }
}
```

### Grid Structure
```
┌─────────────────────────────────────────────────────────────┐
│                     STUDENT INFORMATION                     │
├─────────────────────────────────────────────────────────────┤
│ SUBJECTS │ TERM1 │ TERM2 │ TERM3 │ WEIGHTED │ COMMENTS    │
├─────────────────────────────────────────────────────────────┤
│ Math    │ 15.5  │ 16.0  │ 17.2  │   16.2   │ Excellent   │
│ Science │ 14.0  │ 15.5  │ 16.8  │   15.4   │ Good        │
│ ...     │ ...   │ ...   │ ...   │   ...    │ ...        │
├─────────────────────────────────────────────────────────────┤
│                    FINAL STATISTICS                         │
├─────────────────────────────────────────────────────────────┤
│                    DIRECTOR'S COMMENTS                     │
└─────────────────────────────────────────────────────────────┘
```

## Component Specifications

### 1. Student Information Section
**Structure:**
```
STUDENT NAME: [Full Name]
CLASS: [Class Name] - [Section]
ACADEMIC YEAR: [Year]
STUDENT ID: [ID Number]
```

**Styling:**
- Background: #f8f9fa (digital), transparent (print)
- Border: 1px solid #e9ecef
- Padding: 16px
- Font: 14px, normal weight

### 2. Grades Table
**Table Structure:**
```html
<table class="grades-table">
  <thead>
    <tr>
      <th>Subject</th>
      <th>Term 1</th>
      <th>Term 2</th>
      <th>Term 3</th>
      <th>Weighted</th>
      <th>Comments</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Mathematics</td>
      <td>15.5</td>
      <td>16.0</td>
      <td>17.2</td>
      <td>16.2</td>
      <td>Excellent progress</td>
    </tr>
    <!-- More rows... -->
  </tbody>
</table>
```

**Table Styling:**
- Border: 1px solid #dee2e6
- Cell Padding: 8px 12px
- Alternating Rows: #f8f9fa (digital), transparent (print)
- Header Background: #2c3e50 (digital), transparent (print)
- Header Text: #ffffff (digital), #000000 (print)

### 3. Final Statistics Section
**Content:**
```
FINAL AVERAGE: [X.X/20]
CLASS RANK: [X/X]
PROMOTION STATUS: [Promoted/Not Promoted]
```

**Styling:**
- Background: #e3f2fd (digital), transparent (print)
- Border: 1px solid #bbdefb
- Padding: 12px
- Font: 13px, bold

### 4. Director's Comments Section
**Structure:**
```
DIRECTOR'S COMMENTS:
[Comment text goes here. Multiple lines supported with proper spacing.]
```

**Styling:**
- Background: #fff3e0 (digital), transparent (print)
- Border: 1px solid #ffcc02
- Padding: 12px
- Font: 11px, italic
- Line Height: 1.6

## Responsive Design

### Digital Display Adaptations
- **Desktop**: Full A4 landscape layout
- **Tablet**: Scaled to fit screen while maintaining proportions
- **Mobile**: Stack vertically with reduced font sizes

### Breakpoints
```css
@media (max-width: 1200px) {
  .grades-table {
    font-size: 11px;
  }
}

@media (max-width: 768px) {
  .grades-table {
    font-size: 10px;
  }
  
  .student-info {
    font-size: 12px;
  }
}
```

## Print-Specific Considerations

### Page Setup
```css
@page {
  size: A4 landscape;
  margin: 1cm;
}

@media print {
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  .no-print {
    display: none;
  }
  
  .page-break {
    page-break-before: always;
  }
}
```

### Print Optimization
- Remove all background images
- Ensure text is black on white
- Remove interactive elements
- Optimize for 300 DPI printing
- Ensure proper page breaks

## Data Integration Specifications

### Weighted Average Calculation
```javascript
// Formula for weighted average
function calculateWeightedAverage(terms, coefficients) {
  let total = 0;
  let totalCoeff = 0;
  
  terms.forEach((term, index) => {
    total += term * coefficients[index];
    totalCoeff += coefficients[index];
  });
  
  return total / totalCoeff;
}
```

### Data Synchronization
- Connect to `/marks/weighted-grades` endpoint
- Ensure real-time data updates
- Handle loading states gracefully
- Implement error handling for data inconsistencies

## Accessibility Considerations

### Color Contrast
- Text color: #2c3e50
- Background: #ffffff
- Contrast ratio: 7.5:1 (meets WCAG AAA)

### Screen Reader Support
- Proper table headers with scope attributes
- Alt text for decorative elements
- Semantic HTML structure
- Keyboard navigation support

### Font Legibility
- Minimum font size: 11px
- High contrast for print mode
- Clear distinction between different text types

## Implementation Checklist

### Design Implementation
- [ ] Create CSS variables for consistent theming
- [ ] Implement responsive grid system
- [ ] Add print-specific media queries
- [ ] Create typography scale
- [ ] Implement color system

### Functionality Implementation
- [ ] Connect to weighted-grades API
- [ ] Implement data synchronization
- [ ] Add loading states
- [ ] Implement error handling
- [ ] Add print functionality

### Testing Requirements
- [ ] Cross-browser testing
- [ ] Print testing on different printers
- [ ] Responsive testing on various devices
- [ ] Accessibility testing
- [ ] Performance testing

## Quality Assurance

### Design Review
- Visual consistency check
- Typography validation
- Color scheme approval
- Layout optimization

### Technical Review
- Code quality standards
- Performance optimization
- Cross-browser compatibility
- Print functionality testing

### User Acceptance Testing
- Academic staff review
- Parent feedback collection
- Student usability testing
- Final approval process