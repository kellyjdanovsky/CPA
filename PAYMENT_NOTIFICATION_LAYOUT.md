# Payment Notification Layout Visualization

## A4 Page Layout (210mm × 297mm)

```mermaid
graph TD
    A[A4 Page<br/>210mm × 297mm] --> B[Grid Layout<br/>2 Columns × 5 Rows]
    B --> C1[Notification 1]
    B --> C2[Notification 2]
    B --> C3[Notification 3]
    B --> C4[Notification 4]
    B --> C5[Notification 5]
    B --> C6[Notification 6]
    B --> C7[Notification 7]
    B --> C8[Notification 8]
    B --> C9[Notification 9]
    B --> C10[Notification 10]
    
    style A fill:#e1f5fe
    style B fill:#f3e5f5
    style C1 fill:#e8f5e8
    style C2 fill:#e8f5e8
    style C3 fill:#e8f5e8
    style C4 fill:#e8f5e8
    style C5 fill:#e8f5e8
    style C6 fill:#e8f5e8
    style C7 fill:#e8f5e8
    style C8 fill:#e8f5e8
    style C9 fill:#e8f5e8
    style C10 fill:#e8f5e8
```

## Individual Notification Structure

```mermaid
graph TD
    A[Notification<br/>95mm × 54mm] --> B[Header Section<br/>School Info]
    A --> C[Title<br/>FAMPAHAFANTARANA FANDOAVAM-BOLA]
    A --> D[Content Area]
    A --> E[Footer Section<br/>Deadline & Thanks]
    
    D --> D1[Student Info<br/>Name & Class]
    D --> D2[Payment Reason<br/>Antony tsy voaloa]
    D --> D3[Amounts Section<br/>Vola rehetra, efa naloa, mbola tokony haloa]
    
    style A fill:#fff3e0
    style B fill:#fce4ec
    style C fill:#f3e5f5
    style D fill:#e1f5fe
    style E fill:#e8f5e8
    style D1 fill:#bbdefb
    style D2 fill:#c8e6c9
    style D3 fill:#f8bbd0
```

## Content Details

### Header Section
- School logo (8mm × 8mm)
- School name: COLLEGE PRIVE ADVENTISTE AVARATETEZANA
- Address: AMPITATAFIKA ANTANANARIVO MADAGASCAR
- Phone: Tél: 038 34 921 09

### Title Section
- FAMPAHAFANTARANA FANDOAVAM-BOLA (centered, bold)

### Content Area
1. **Student Information**
   - "Ho an'ny ray aman-drenin'i [Student Name]"
   - Class name

2. **Payment Reason**
   - "Antony tsy voaloa:"
   - Payment titles

3. **Amounts Section**
   - "• Vola rehetra tokony haloa: [amount] Ariary"
   - "• Vola efa naloa: [amount] Ariary"
   - "• Vola mbola tokony haloa: [amount] Ariary" (highlighted)

### Footer Section
- "Daty farany hanaovana fandoavam-bola: [deadline date]"
- "misaotra amin'ny fiaraha-miasa sy ny fandraisana andrakitra"
- Status and date information

## Layout Specifications

- **Page Size**: A4 (210mm × 297mm)
- **Margins**: 3mm
- **Grid**: 2 columns × 5 rows
- **Notification Size**: 95mm × 54mm
- **Gap Between Notifications**: 3mm
- **Font Size**: 7-8px for body text, larger for headings
- **Border**: 2px solid black
- **Background**: White
- **Page Breaks**: After each filled page