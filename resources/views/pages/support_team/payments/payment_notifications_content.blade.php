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
        font-size: 6.5px;
        margin: 0;
        padding: @if(isset($is_preview)) 3mm @elseif(isset($is_pdf)) 0 @else 0 @endif;
        line-height: 1.15;
        color: #000;
        background: white;
    }
    
    .page {
        width: @if(isset($is_preview)) 210mm @elseif(isset($is_pdf)) 210mm @else 204mm @endif;
        height: @if(isset($is_preview)) 297mm @elseif(isset($is_pdf)) 297mm @else 291mm @endif;
        position: relative;
        margin: 0 @if(isset($is_preview)) auto @elseif(isset($is_pdf)) 0 @else 0 @endif;
        padding: @if(isset($is_preview)) 3mm @elseif(isset($is_pdf)) 2mm @else 2mm @endif;
        @if(isset($is_preview))
        margin-bottom: 20px;
        border: 1px dashed #ccc;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        @endif
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: repeat(5, 1fr);
        gap: 2mm;
    }
    
    .notification {
        width: 100%;
        height: 100%;
        border: 2px solid #000;
        border-radius: 2mm;
        padding: 2mm;
        background: #fff;
        font-size: 6.5px;
        line-height: 1.15;
        color: #000;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    /* State styles (case by case) */
    .notification.is-overdue { border-color: #b00020; border-width: 2px; }
    .notification.is-due { border-color: #ff8f00; border-width: 2px; }
    .notification.is-ok { border-color: #2e7d32; border-width: 2px; }

    .header {
        text-align: center;
        border-bottom: 1px solid #000;
        padding: 0.3mm;
        margin-bottom: 0.3mm;
        background: #fff;
        position: relative;
        height: 10mm;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .school-logo {
        width: 7mm;
        height: 7mm;
        position: absolute;
        left: 0.3mm;
        top: 50%;
        transform: translateY(-50%);
        object-fit: contain;
        border-radius: 0.5mm;
    }
    
    .school-info {
        text-align: center;
        font-weight: 600;
        font-size: 4px;
        line-height: 1.05;
        color: #000;
        margin: 0;
        padding-left: 7.5mm;
        overflow: hidden;
    }
    
    .school-name {
        font-size: 4.5px;
        font-weight: 700;
        color: #000;
        margin-bottom: 0.15mm;
        text-transform: uppercase;
        letter-spacing: 0.05px;
        white-space: nowrap;
    }
    
    .notification-title {
        text-align: center;
        font-weight: 800;
        font-size: 6.5px;
        margin: 0.3mm 0;
        color: #000;
        padding: 0.8mm;
        text-transform: uppercase;
        letter-spacing: 0.1px;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #000;
        background: #f0f0f0;
        border-radius: 0.8mm;
    }
    
    .content {
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        flex: 1;
        gap: 0.6mm;
        padding-bottom: 10.5mm;
    }
    
    .student-info {
        padding: 0.8mm;
        flex-shrink: 0;
        background: #fafafa;
        border-radius: 0.5mm;
        overflow: hidden;
    }
    
    .recipient {
        font-size: 4.5px;
        font-weight: 600;
        margin-bottom: 0.2mm;
    }
    
    .student-name {
        font-weight: 700;
        color: #000;
        font-size: 6.5px;
        margin-bottom: 0.2mm;
        text-transform: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    
    .class-info {
        font-size: 5.5px;
        color: #000;
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .payment-reason-section {
        padding: 0.8mm;
        flex-shrink: 0;
        border: 1px solid #000;
        background: #fff3e0;
        border-radius: 0.8mm;
        overflow: hidden;
        max-height: 9mm;
    }
    
    .reason-title {
        font-size: 5.5px;
        color: #000;
        font-weight: 700;
        margin-bottom: 0.2mm;
    }
    
    .reason-content {
        font-size: 5px;
        color: #000;
        font-weight: 600;
        font-style: italic;
        white-space: normal;
        word-wrap: break-word;
        overflow: hidden;
        line-height: 1.15;
        max-height: 7mm;
    }
    
    .amounts-section {
        background: #fff;
        border: 1px solid #000;
        padding: 0.8mm;
        flex: 1;
        min-height: 0;
        overflow: hidden;
        border-radius: 0.8mm;
    }
    
    .amount-line {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.3mm;
        font-size: 5.5px;
        line-height: 1.15;
        padding: 0.2mm 0;
    }
    
    .amount-line-highlight {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.6mm;
        padding: 0.8mm;
        background: #fff3e0;
        border: 1px solid #000;
        font-size: 6px;
        font-weight: 800;
        border-radius: 0.8mm;
    }
    
    .amount-label {
        color: #000;
        font-weight: 600;
        flex: 1;
        margin-right: 0.8mm;
    }
    
    .amount-value {
        color: #000;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
    }
    
    .amount-label-highlight {
        color: #000;
        font-weight: 800;
        flex: 1;
        margin-right: 0.8mm;
    }
    
    .amount-value-highlight {
        color: #000;
        font-weight: 800;
        font-size: 6.5px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    
    .footer-section {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #000;
        margin: 0 -2mm -2mm -2mm;
        padding: 0.5mm 1.5mm;
        height: 10mm;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .deadline-info {
        text-align: center;
        font-size: 5px;
        color: #000;
        font-weight: 700;
        margin-bottom: 0.2mm;
        line-height: 1.1;
    }

    .deadline-date {
        font-size: 6px;
        font-weight: 800;
        color: #000;
        background: #fff;
        padding: 0.2mm 0.8mm;
        border: 1px solid #000;
        border-radius: 0.4mm;
        display: inline-block;
        margin-top: 0.2mm;
    }

    .status-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 4.5px;
        color: #000;
        font-weight: 600;
        margin-top: 0.2mm;
        width: 100%;
    }

    .status-chip {
        border: 1px solid #000;
        padding: 0.2mm 0.8mm;
        border-radius: 6px;
        font-weight: 700;
        background: #f5f5f5;
        font-size: 4.5px;
    }
    
    .thanks {
        font-style: italic;
        text-align: center;
        font-size: 4px;
        margin: 0.2mm 0;
        line-height: 1.1;
    }
    
    @if(!isset($is_preview))
        .page-break {
            page-break-before: always;
        }
    @else
        .page-break {
            margin-top: 20px;
        }
        
        /* Print optimizations */
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
                font-size: 6.5px !important;
                line-height: 1.15 !important;
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
                padding: 2mm !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                background: white !important;
                overflow: visible !important;
            }
            
            .page:last-child {
                page-break-after: avoid !important;
            }
            
            .notification {
                width: 100% !important;
                height: 100% !important;
                background: white !important;
                color: black !important;
                border: 2px solid black !important;
                padding: 2mm !important;
                position: relative !important;
                page-break-inside: avoid !important;
                font-size: 6.5px !important;
                line-height: 1.15 !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
                break-inside: avoid !important;
                display: flex !important;
                flex-direction: column !important;
            }
            
            .header {
                border-bottom: 1px solid black !important;
                height: 10mm !important;
            }
            
            .notification-title {
                border: 1px solid black !important;
            }
            
            .content {
                padding-bottom: 10.5mm !important;
            }
            
            .footer-section {
                height: 10mm !important;
                border-top: 1px solid black !important;
            }
        }
    @endif
    
    .empty {
        border: 1px dashed #ccc;
        background: #f9f9f9;
        text-align: center;
        color: #999;
        font-size: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
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
                
                @php
                    // Determine card state classes per student
                    $isOverdue = false;
                    try {
                        $deadlineTs = strtotime($payment_deadline);
                        $todayTs = strtotime(date('Y-m-d'));
                        $isOverdue = $todayTs > $deadlineTs && ($amountDue ?? 0) > 0;
                    } catch (\Exception $e) {
                        $isOverdue = false;
                    }
                    $cardClass = $isOverdue ? 'is-overdue' : (($amountDue ?? 0) > 0 ? 'is-due' : 'is-ok');
                @endphp
                <div class="notification {{ $cardClass }}">
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
                            <div class="school-logo" style="background-color: #2c5aa0; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 3.5px; border-radius: 0.5mm;">
                                AVAR
                            </div>
                        @endif
                        
                        <div class="school-info">
                            <div class="school-name">COLLEGE PRIVE ADVENTISTE AVARATETEZANA</div>
                            <div style="font-size: 3.5px;">AMPITATAFIKA ANTANANARIVO MADAGASCAR</div>
                            <div style="font-size: 3.5px;">Tél: 038 34 921 09</div>
                        </div>
                    </div>
                    
                    <div class="notification-title">
                        FAMPAHAFANTARANA FANDOAVAM-BOLA
                    </div>
                    
                    <div class="content">
                        <div class="student-info">
                            <div class="recipient">Ho an'ny ray aman-drenin'i</div>
                            <div class="student-name">{{ $student->user->name }}</div>
                            <div class="class-info">{{ $class_name }}{{ $student->section ? ' ' . $student->section->name : '' }}</div>
                            <div style="font-size: 3.5px; color: #000; font-weight: 500; margin-top: 0.3mm;">
                                ID: {{ $student->user_id }} | Classe: {{ $student->my_class_id }}
                            </div>
                        </div>
                        
                        <div class="payment-reason-section">
                            <div class="reason-title">Antony tsy voaloa:</div>
                            <div class="reason-content">{{ $paymentTitles }}</div>
                        </div>
                        
                        <div class="amounts-section">
                            <div class="amount-line">
                                <span class="amount-label">• Vola rehetra haloa:</span>
                                <span class="amount-value">{{ number_format($totalAmount, 0, ',', ' ') }} Ar</span>
                            </div>
                            @if($amountPaid > 0)
                                <div class="amount-line">
                                    <span class="amount-label">• Vola efa naloa:</span>
                                    <span class="amount-value">{{ number_format($amountPaid, 0, ',', ' ') }} Ar</span>
                                </div>
                            @endif
                            <div class="amount-line-highlight">
                                <span class="amount-label-highlight">• Vola mbola haloa:</span>
                                <span class="amount-value-highlight">{{ number_format($amountDue, 0, ',', ' ') }} Ar</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="footer-section">
                        <div class="deadline-info">
                            Daty farany: <span class="deadline-date">{{ date('d/m/Y', strtotime($payment_deadline)) }}</span>
                        </div>
                        
                        <div class="thanks">
                            misaotra amin'ny fiaraha-miasa
                        </div>
                        
                        <div class="status-info">
                            <span class="status-chip">
                                @if($isOverdue)
                                    Tara
                                @elseif(($amountDue ?? 0) > 0)
                                    Sisa
                                @else
                                    VITA
                                @endif
                            </span>
                            <span>{{ date('d/m/y') }}</span>
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty notification box for consistent grid layout --}}
                <div class="notification empty">
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
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