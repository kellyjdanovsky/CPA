{{-- This file contains the actual payment notifications content --}}
{{-- It can be included in both PDF generation and preview --}}

@php
    $studentsPerPage = 10;
    $totalStudents = count($unpaid_students);
    $totalPages = ceil($totalStudents / $studentsPerPage);
    $currentPage = 1;
@endphp

@if(!isset($is_preview) && !isset($is_pdf))
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Avis de Paiement - {{ $class_name }}</title>
@endif

<style>
    @if(!isset($is_preview) && !isset($is_pdf))
        @page {
            margin: 3mm;
            size: A4 portrait;
        }
    @endif
    
    * {
        box-sizing: border-box;
    }
    
    .notifications-body {
        font-family: Arial, sans-serif;
        font-size: 8px;
        margin: 0;
        padding: @if(isset($is_preview)) 3mm @elseif(isset($is_pdf)) 0 @else 0 @endif;
        line-height: 1.2;
        color: #000; /* Black text */
        background: white;
    }
    
    .page {
        width: @if(isset($is_preview)) 210mm @elseif(isset($is_pdf)) 210mm @else 204mm @endif; /* A4 width */
        height: @if(isset($is_preview)) 297mm @elseif(isset($is_pdf)) 297mm @else 291mm @endif; /* A4 height */
        display: grid;
        grid-template-columns: 1fr 1fr; /* 2 colonnes verticales */
        grid-template-rows: repeat(5, 1fr); /* 5 rangées horizontales = 10 élèves */
        gap: 4mm; /* Espacement plus visible entre les cases */
        margin: 0 @if(isset($is_preview)) auto @elseif(isset($is_pdf)) 0 @else 0 @endif;
        padding: @if(isset($is_preview)) 5mm @elseif(isset($is_pdf)) 3mm @else 2mm @endif;
        @if(isset($is_preview))
        margin-bottom: 20px;
        border: 1px dashed #ccc; /* Bordure pour visualiser la page */
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        @endif
    }
    
    .notification {
        width: 100%;
        height: 100%;
        border: 3px solid #000000;
        border-radius: 8px;
        padding: 3mm;
        margin: 1mm;
        position: relative;
        background: #ffffff;
        overflow: hidden;
        font-family: 'Arial', sans-serif;
        font-size: 6px;
        line-height: 1.2;
        color: #000000;
        display: flex;
        flex-direction: column;
        min-height: 55mm;
        max-height: 57mm;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        box-sizing: border-box;
    }
    
    /* Grid positioning for proper 2x5 layout */
    .notification:nth-child(1) { grid-column: 1; grid-row: 1; }
    .notification:nth-child(2) { grid-column: 2; grid-row: 1; }
    .notification:nth-child(3) { grid-column: 1; grid-row: 2; }
    .notification:nth-child(4) { grid-column: 2; grid-row: 2; }
    .notification:nth-child(5) { grid-column: 1; grid-row: 3; }
    .notification:nth-child(6) { grid-column: 2; grid-row: 3; }
    .notification:nth-child(7) { grid-column: 1; grid-row: 4; }
    .notification:nth-child(8) { grid-column: 2; grid-row: 4; }
    .notification:nth-child(9) { grid-column: 1; grid-row: 5; }
    .notification:nth-child(10) { grid-column: 2; grid-row: 5; }
    
    .header {
        text-align: center;
        border-bottom: 1px solid #000000;
        padding: 1.5mm;
        margin-bottom: 1.5mm;
        background: #ffffff;
        position: relative;
        height: 10mm;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .school-logo {
        width: 8px;
        height: 8px;
        position: absolute;
        left: 1mm;
        top: 50%;
        transform: translateY(-50%);
        object-fit: contain;
        border-radius: 1px;
    }
    
    .school-info {
        text-align: center;
        font-weight: 600;
        font-size: 3px;
        line-height: 1.0;
        color: #000000;
        margin: 0;
        padding-left: 10px;
    }
    
    .school-name {
        font-size: 3.5px;
        font-weight: 700;
        color: #000000;
        margin-bottom: 0.2mm;
        text-transform: uppercase;
        letter-spacing: 0.1px;
    }
    
    .notification-title {
        text-align: center;
        font-weight: 700;
        font-size: 6px;
        margin: 1.5mm 0;
        color: #000000;
        padding: 2mm;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-bottom: 1px solid #000000;
    }
    
    .content {
        flex: 1;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 1.5mm;
        overflow: hidden;
        min-height: 0;
    }
    
    .student-info {
        padding: 1.5mm;
        flex-shrink: 0;
    }
    
    .student-name {
        font-weight: 700;
        color: #000000;
        font-size: 7px;
        margin-bottom: 0.5mm;
        text-transform: capitalize;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .class-info {
        font-size: 5px;
        color: #000000;
        font-weight: 500;
        margin: 0;
    }
    
    .payment-reason-section {
        padding: 1.5mm;
        flex-shrink: 0;
        border-top: 1px solid #000000;
        border-bottom: 1px solid #000000;
    }
    
    .reason-title {
        font-size: 5.5px;
        color: #000000;
        font-weight: 700;
        margin-bottom: 0.5mm;
    }
    
    .reason-content {
        font-size: 5px;
        color: #000000;
        font-weight: 600;
        font-style: italic;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .amounts-section {
        background: #ffffff;
        border: 2px solid #000000;
        padding: 1.5mm;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    
    .amount-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1mm;
        font-size: 5px;
        line-height: 1.2;
        padding: 0.5mm 0;
    }
    
    .amount-line-highlight {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1mm;
        padding: 1.5mm;
        background: #f0f0f0;
        border: 2px solid #000000;
        font-size: 5.5px;
        font-weight: 700;
    }
    
    .amount-label {
        color: #000000;
        font-weight: 600;
    }
    
    .amount-value {
        color: #000000;
        font-weight: 700;
        white-space: nowrap;
    }
    
    .amount-label-highlight {
        color: #000000;
        font-weight: 700;
    }
    
    .amount-value-highlight {
        color: #000000;
        font-weight: 700;
        font-size: 6px;
    }
    
    .footer-section {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid #000000;
        margin: 0 -3mm -3mm -3mm;
        padding: 1mm 3mm;
        height: 8mm;
        flex-shrink: 0;
    }
    
    .deadline-info {
        text-align: center;
        font-size: 5px;
        color: #000000;
        font-weight: 700;
        margin-bottom: 1mm;
    }
    
    .deadline-date {
        font-size: 6px;
        font-weight: 700;
        color: #000000;
        background: #ffffff;
        padding: 1mm 2mm;
        border: 2px solid #000000;
        display: inline-block;
    }
    
    .status-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 4px;
        color: #000000;
        font-weight: 500;
        margin-top: 1mm;
    }
    

    

    
    @if(!isset($is_preview))
        .page-break {
            page-break-before: always;
        }
    @else
        .page-break {
            margin-top: 20px;
        }
        
        /* Print optimizations - Simplified for better compatibility */
        @media print {
            @page {
                size: A4 portrait;
                margin: 3mm;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body, html {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                color: black !important;
                font-family: Arial, sans-serif !important;
                font-size: 8px !important;
                line-height: 1.2 !important;
            }
            
            .notifications-body { 
                display: block !important;
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                color: black !important;
                overflow: visible !important;
            }
            
            .page {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                grid-template-rows: repeat(5, 1fr) !important;
                gap: 2mm !important;
                width: 204mm !important;
                height: 287mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                background: white !important;
                overflow: visible !important;
            }
            
            .page:last-child {
                page-break-after: avoid !important;
            }
            
            .notification {
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                height: 100% !important;
                background: white !important;
                color: black !important;
                border: 2px solid black !important;
                padding: 2mm !important;
                position: relative !important;
                page-break-inside: avoid !important;
                font-size: 8px !important;
                line-height: 1.2 !important;
                overflow: visible !important;
                box-sizing: border-box !important;
                justify-content: space-between !important;
            }
            
            .notification * {
                color: black !important;
                background: transparent !important;
            }
            
            .header {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-bottom: 2px solid black !important;
                height: 15mm !important;
                padding: 1mm !important;
            }
            
            .notification-title {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border: 2px solid black !important;
                padding: 1mm !important;
                margin: 1mm 0 !important;
                text-align: center !important;
                font-weight: bold !important;
                height: 8mm !important;
            }
            
            .content {
                flex: 1 !important;
                display: block !important;
            }
            
            .deadline {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                border: 2px solid black !important;
                padding: 1.5mm !important;
                margin: 1mm 0 !important;
                font-weight: bold !important;
                text-align: center !important;
                min-height: 6mm !important;
            }
            
            .deadline-date {
                font-size: 7px !important;
                font-weight: bold !important;
                border: 1px solid black !important;
                padding: 0.5mm 1mm !important;
                margin-top: 0.5mm !important;
                background-color: white !important;
            }
            
            .payment-details {
                border: 2px solid black !important;
                padding: 1.5mm !important;
                margin: 1mm 0 !important;
                background-color: white !important;
            }
            
            .payment-line {
                margin-bottom: 1mm !important;
                color: black !important;
            }
            
            .payment-line.highlight {
                background-color: #f0f0f0 !important;
                border: 1px solid black !important;
                padding: 0.5mm !important;
            }
            
            .label-text {
                font-weight: bold !important;
                color: black !important;
                font-size: 5.5px !important;
            }
            
            .amount-value {
                font-weight: bold !important;
                color: black !important;
                display: block !important;
            }
            
            .amount-value.due {
                font-size: 7px !important;
            }
            
            .malagasy-thanks {
                border-top: 1px solid black !important;
                border-bottom: 1px solid black !important;
                padding: 1mm !important;
                font-style: italic !important;
                text-align: center !important;
            }
            
            .footer {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                border-top: 1px solid black !important;
                padding: 0.5mm !important;
                position: absolute !important;
                bottom: 1mm !important;
                left: 2mm !important;
                right: 2mm !important;
            }
            
            .school-logo {
                width: 20px !important;
                height: 20px !important;
            }
        }
    @endif
</style>

@if(!isset($is_preview) && !isset($is_pdf))
    </head>
    <body class="notifications-body">
@else
    <div class="notifications-body">
@endif

@for ($page = 0; $page < $totalPages; $page++)
    @if ($page > 0)
        <div class="page-break"></div>
    @endif
    
    <div class="page">
        @for ($i = 0; $i < $studentsPerPage; $i++)
            @php
                $studentIndex = $page * $studentsPerPage + $i;
            @endphp
            
            @if ($studentIndex < $totalStudents)
                @php
                    $studentData = $unpaid_students[$studentIndex];
                    $student = $studentData['student'];
                    $status = $studentData['status'];
                    $amountDue = $studentData['amount_due'];
                    $amountPaid = $studentData['amount_paid'] ?? 0;
                    $totalAmount = $studentData['total_amount'] ?? $amountDue + $amountPaid;
                    $paymentTitles = $studentData['payment_titles'];
                @endphp
                
                <div class="notification">
                    <div class="header">
                        @php
                            $logoPath = public_path('images/logo_avar.png');
                            $logoExists = file_exists($logoPath);
                            // For PDF generation, use absolute path; for preview, use asset URL
                            if (isset($is_preview)) {
                                $logoSrc = asset('images/logo_avar.png');
                            } else {
                                $logoSrc = $logoPath;
                            }
                        @endphp
                        
                        @if($logoExists)
                            <img src="{{ $logoSrc }}" alt="Logo AVAR" class="school-logo">
                        @else
                            <div class="school-logo" style="background-color: #2c5aa0; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 5px; border-radius: 1px;">
                                AVAR
                            </div>
                        @endif
                        
                        <div class="school-info">
                            <div class="school-name">COLLEGE PRIVE ADVENTISTE AVARATETEZANA</div>
                            <div style="font-size: 5px;">AMPITATAFIKA ANTANANARIVO MADAGASCAR</div>
                            <div style="font-size: 5px;">Tél: 038 34 921 09</div>
                        </div>
                    </div>
                    
                    <div class="notification-title">
                        FAMPAHAFANTARANA FANDOAVAM-BOLA
                    </div>
                    
                    <div class="content">
                        <div class="student-info">
                            <div style="font-size: 5px; color: #000000; font-weight: 600; margin-bottom: 0.5mm;">Ho an'ny ray aman-drenin'i</div>
                            <div class="student-name">{{ substr($student->user->name, 0, 25) }}</div>
                            <div class="class-info">({{ $class_name }}{{ $student->section ? ' ' . substr($student->section->name, 0, 4) : '' }})</div>
                        </div>
                        
                        <div class="payment-reason-section">
                            <div class="reason-title">Antony tsy voaloa:</div>
                            <div class="reason-content">{{ substr($paymentTitles, 0, 25) }}</div>
                        </div>
                        
                        <div class="amounts-section">
                            <div class="amount-line">
                                <span class="amount-label">• Vola rehetra tokony haloa:</span>
                                <span class="amount-value">{{ number_format($totalAmount, 0, ',', ' ') }} Ariary</span>
                            </div>
                            @if($amountPaid > 0)
                                <div class="amount-line">
                                    <span class="amount-label">• Vola efa naloa:</span>
                                    <span class="amount-value">{{ number_format($amountPaid, 0, ',', ' ') }} Ariary</span>
                                </div>
                            @endif
                            <div class="amount-line-highlight">
                                <span class="amount-label-highlight">• Vola mbola tokony haloa:</span>
                                <span class="amount-value-highlight">{{ number_format($amountDue, 0, ',', ' ') }} Ariary</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="footer-section">
                        <div class="deadline-info">
                            Daty farany hanaovana fandoavam-bola:
                            <div class="deadline-date">{{ date('d/m/Y', strtotime($payment_deadline)) }}</div>
                        </div>
                        
                        <div class="status-info">
                            <span>{{ $status }}</span>
                            <span>{{ date('d/m/y') }}</span>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <span>{{ $status }}</span>
                        <span>{{ date('d/m/y') }}</span>
                    </div>
                </div>
            @else
                {{-- Empty notification box for consistent grid layout --}}
                <div class="notification" style="border: 1px dashed #ccc; background-color: #f9f9f9;">
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999; font-size: 5px;">
                        Aucun élève
                    </div>
                </div>
            @endif
        @endfor
    </div>
@endfor

@if(!isset($is_preview) && !isset($is_pdf))
    </body>
    </html>
@else
    </div>
@endif