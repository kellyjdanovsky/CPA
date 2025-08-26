<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Avis de Paiement - {{ $class_name }}</title>
    <style>
        @page { margin: 3mm; size: A4 portrait; }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: Arial;
            font-size: 7px;
            line-height: 1.1;
            color: #000;
            background: #fff;
        }
        
        .page {
            width: 204mm;
            height: 291mm;
            margin: 0;
            padding: 3mm;
            page-break-after: always;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(5, 1fr);
            gap: 4mm;
        }
        
        .page:last-child { page-break-after: avoid; }
        
        .notif {
            width: 100%;
            height: 100%;
            border: 3px solid #000000;
            border-radius: 3mm;
            padding: 3mm;
            margin: 1mm;
            background: #fff;
            font-size: 5px;
            line-height: 1.1;
            color: #000000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            box-sizing: border-box;
        }
        
        .hdr {
            text-align: center;
            border-bottom: 1px solid #000000;
            padding: 1.5mm;
            margin-bottom: 1.5mm;
            height: 8mm;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            flex-shrink: 0;
        }
        
        .logo {
            width: 6px;
            height: 6px;
            position: absolute;
            left: 0.5mm;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .school {
            font-size: 2.5px;
            font-weight: bold;
            padding-left: 8px;
            color: #000000;
            margin-top: 0.1mm;
            text-align: center;
        }
        
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 5px;
            margin: 1.5mm 0;
            color: #000000;
            padding: 2mm;
            height: auto;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-bottom: 1px solid #000000;
        }
        
        .content {
            font-size: 5px;
            color: #000000;
            flex: 1;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.5mm;
            padding: 0;
            background-color: #ffffff;
            min-height: 0;
            line-height: 1.1;
        }
        
        .student-info {
            padding: 1.5mm;
            flex-shrink: 0;
        }
        
        .name {
            font-weight: bold;
            font-size: 5.5px;
            margin-bottom: 0.3mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #000000;
            text-transform: capitalize;
        }
        
        .class {
            font-size: 4.5px;
            margin: 0;
            color: #000000;
        }
        
        .payment-reason-section {
            padding: 1.5mm;
            flex-shrink: 0;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }
        
        .reason-title {
            font-size: 4.5px;
            color: #000000;
            font-weight: bold;
            margin-bottom: 0.3mm;
        }
        
        .reason-content {
            font-size: 4px;
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
            margin-bottom: 0.8mm;
            font-size: 4px;
            line-height: 1.1;
            padding: 0.3mm;
        }
        
        .amount-line-highlight {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1mm;
            padding: 1mm;
            background: #f0f0f0;
            border: 2px solid #000000;
            font-size: 4.5px;
            font-weight: bold;
        }
        
        .amount-label {
            color: #000000;
            font-weight: 600;
        }
        
        .amount-value {
            color: #000000;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .amount-label-highlight {
            color: #000000;
            font-weight: bold;
        }
        
        .amount-value-highlight {
            color: #000000;
            font-weight: bold;
            font-size: 5px;
        }
        
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 4px;
            padding: 1mm;
            border-top: 1px solid #000000;
            height: 8mm;
            background: #ffffff;
            color: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .deadline-info {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        
        .deadline-date {
            background: #ffffff;
            padding: 0.5mm 1mm;
            border: 2px solid #000000;
            color: #000000;
            font-weight: bold;
            display: inline-block;
        }
        
        .f-left { float: left; width: 50%; text-align: left; }
        .f-right { float: right; width: 50%; text-align: right; }
        
        .empty {
            border: 1px dashed #ccc;
            background: #f9f9f9;
            text-align: center;
            color: #999;
            font-size: 4px;
            padding-top: 25mm;
        }
        
        .clear { clear: both; }
    </style>
</head>
<body>
    @php
        $studentsPerPage = 10;
        $totalStudents = count($unpaid_students);
        $totalPages = ceil($totalStudents / $studentsPerPage);
    @endphp

    @if($totalStudents > 0)
        @for ($page = 0; $page < $totalPages; $page++)
            <div class="page">
                @for ($row = 0; $row < 5; $row++)
                    @for ($col = 0; $col < 2; $col++)
                        @php
                            $studentIndex = $page * $studentsPerPage + $row * 2 + $col;
                        @endphp
                        
                        @if ($studentIndex < $totalStudents && isset($unpaid_students[$studentIndex]))
                            @php
                                $studentData = $unpaid_students[$studentIndex];
                                $student = $studentData['student'] ?? null;
                                
                                if (!$student || !isset($student->user)) {
                                    continue;
                                }
                                
                                $status = $studentData['status'] ?? 'Normal';
                                $amountDue = $studentData['amount_due'] ?? 0;
                                $amountPaid = $studentData['amount_paid'] ?? 0;
                                $totalAmount = $studentData['total_amount'] ?? ($amountDue + $amountPaid);
                                $paymentTitles = $studentData['payment_titles'] ?? 'Paiement';
                            @endphp
                            
                            <div class="notif" style="grid-column: {{ $col + 1 }}; grid-row: {{ $row + 1 }};">
                                <div class="hdr">
                                    @php
                                        $logoPath = public_path('images/logo_avar.png');
                                        $logoExists = file_exists($logoPath);
                                    @endphp
                                    
                                    @if($logoExists)
                                        <img src="{{ $logoPath }}" class="logo">
                                    @endif
                                    
                                    <div class="school">
                                        <div style="font-size: 5px; font-weight: bold;">COLLEGE PRIVE ADVENTISTE AVARATETEZANA</div>
                                        <div style="font-size: 3.5px;">AMPITATAFIKA ANTANANARIVO MADAGASCAR</div>
                                        <div style="font-size: 3.5px;">Tél: 038 34 921 09</div>
                                    </div>
                                </div>
                                
                                <div class="title">FAMPAHAFANTARANA FANDOAVAM-BOLA</div>
                                
                                <div class="content">
                                    <div class="student-info">
                                        <div style="font-size: 4px; color: #000000; font-weight: 600; margin-bottom: 0.5mm;">Ho an'ny ray aman-drenin'i</div>
                                        <div class="name">{{ substr($student->user->name ?? 'Nom non disponible', 0, 20) }}</div>
                                        <div class="class">({{ $class_name }}{{ isset($student->section) && $student->section ? ' ' . substr($student->section->name, 0, 3) : '' }})</div>
                                    </div>
                                    
                                    <div class="payment-reason-section">
                                        <div class="reason-title">Antony tsy voaloa:</div>
                                        <div class="reason-content">{{ substr($paymentTitles, 0, 20) }}</div>
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
                                
                                <div class="footer">
                                    <div class="deadline-info">
                                        Daty farany: <span class="deadline-date">{{ date('d/m/Y', strtotime($payment_deadline)) }}</span>
                                    </div>
                                </div>
                                
                                <div class="footer">
                                    <div class="f-left">Statut: {{ $status }}</div>
                                    <div class="f-right">{{ date('d/m/y') }}</div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        @else
                            {{-- Empty grid cell for remaining slots --}}
                            <div class="notif empty" style="grid-column: {{ $col + 1 }}; grid-row: {{ $row + 1 }}; border: 1px dashed #ccc; background: #f9f9f9; text-align: center; padding-top: 20mm; color: #999; font-size: 4px;">
                                Emplacement vide
                            </div>
                        @endif
                    @endfor
                @endfor
            </div>
        @endfor
    @else
        <div class="page">
            <div class="notif empty" style="grid-column: 1 / -1; text-align: center; padding: 20mm;">
                <strong>Aucun élève impayé trouvé</strong><br>
                <small>Vérifiez les critères de sélection</small>
            </div>
        </div>
    @endif
</body>
</html>