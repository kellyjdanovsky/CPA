<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Avis de Paiement - {{ $class_name }}</title>
    <style>
        @page { 
            margin: 3mm; 
            size: A4 portrait; 
        }
        
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #000;
            background: #fff;
        }
        
        .page {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 3mm;
            page-break-after: always;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(5, 1fr);
            gap: 3mm;
        }
        
        .page:last-child { 
            page-break-after: avoid; 
        }
        
        .notif {
            width: 100%;
            height: 100%;
            border: 2px solid #000;
            border-radius: 3mm;
            padding: 2mm;
            background: #fff;
            font-size: 7px;
            line-height: 1.2;
            color: #000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .hdr {
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 1mm;
            margin-bottom: 1mm;
            height: 12mm;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            flex-shrink: 0;
        }
        
        .logo {
            width: 8mm;
            height: 8mm;
            position: absolute;
            left: 1mm;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .school {
            font-size: 4px;
            font-weight: bold;
            padding-left: 10mm;
            color: #000;
            text-align: center;
        }
        
        .school-name {
            font-size: 5px;
            font-weight: bold;
            margin-bottom: 0.5mm;
        }
        
        .school-address {
            font-size: 3.5px;
            margin-bottom: 0.3mm;
        }
        
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 7px;
            margin: 1mm 0;
            color: #000;
            padding: 1mm;
            height: auto;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #000;
            background: #f8f8f8;
        }
        
        .content {
            font-size: 7px;
            color: #000;
            flex: 1;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1mm;
            padding: 0;
            background-color: #fff;
            min-height: 0;
            line-height: 1.2;
        }
        
        .student-info {
            padding: 1mm;
            flex-shrink: 0;
        }
        
        .recipient {
            font-size: 5px;
            font-weight: 600;
            margin-bottom: 0.5mm;
        }
        
        .name {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 0.5mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #000;
            text-transform: capitalize;
        }
        
        .class {
            font-size: 6px;
            margin: 0;
            color: #000;
        }
        
        .payment-reason-section {
            padding: 1mm;
            flex-shrink: 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            background: #f9f9f9;
        }
        
        .reason-title {
            font-size: 6px;
            color: #000;
            font-weight: bold;
            margin-bottom: 0.5mm;
        }
        
        .reason-content {
            font-size: 5.5px;
            color: #000;
            font-weight: 600;
            font-style: italic;
            white-space: normal;
            word-wrap: break-word;
        }
        
        .amounts-section {
            background: #fff;
            border: 1px solid #000;
            padding: 1mm;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }
        
        .amount-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1mm;
            font-size: 6px;
            line-height: 1.2;
            padding: 0.5mm 0;
        }
        
        .amount-line-highlight {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1mm;
            padding: 1mm;
            background: #fffbea;
            border: 1px solid #000;
            font-size: 7px;
            font-weight: bold;
        }
        
        .amount-label {
            color: #000;
            font-weight: 600;
        }
        
        .amount-value {
            color: #000;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .amount-label-highlight {
            color: #000;
            font-weight: bold;
        }
        
        .amount-value-highlight {
            color: #000;
            font-weight: bold;
            font-size: 8px;
        }
        
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 5px;
            padding: 1mm;
            border-top: 1px solid #000;
            height: 10mm;
            background: #fff;
            color: #000;
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
            background: #fff;
            padding: 0.5mm 1mm;
            border: 1px solid #000;
            color: #000;
            font-weight: bold;
            display: inline-block;
        }
        
        .thanks {
            font-style: italic;
            text-align: center;
            margin-top: 1mm;
        }
        
        .f-left { 
            float: left; 
            width: 50%; 
            text-align: left; 
            font-size: 5px;
        }
        
        .f-right { 
            float: right; 
            width: 50%; 
            text-align: right; 
            font-size: 5px;
        }
        
        .empty {
            border: 1px dashed #ccc;
            background: #f9f9f9;
            text-align: center;
            color: #999;
            font-size: 6px;
            padding-top: 20mm;
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
                            
                            <div class="notif">
                                <div class="hdr">
                                    @php
                                        $logoPath = public_path('images/logo_avar.png');
                                        $logoExists = file_exists($logoPath);
                                    @endphp
                                    
                                    @if($logoExists)
                                        <img src="{{ $logoPath }}" class="logo">
                                    @endif
                                    
                                    <div class="school">
                                        <div class="school-name">COLLEGE PRIVE ADVENTISTE AVARATETEZANA</div>
                                        <div class="school-address">AMPITATAFIKA ANTANANARIVO MADAGASCAR</div>
                                        <div class="school-address">Tél: 038 34 921 09</div>
                                    </div>
                                </div>
                                
                                <div class="title">FAMPAHAFANTARANA FANDOAVAM-BOLA</div>
                                
                                <div class="content">
                                    <div class="student-info">
                                        <div class="recipient">Ho an'ny ray aman-drenin'i</div>
                                        <div class="name">{{ $student->user->name ?? 'Nom non disponible' }}</div>
                                        <div class="class">{{ $class_name }}{{ isset($student->section) && $student->section ? ' ' . $student->section->name : '' }}</div>
                                    </div>
                                    
                                    <div class="payment-reason-section">
                                        <div class="reason-title">Antony tsy voaloa:</div>
                                        <div class="reason-content">{{ $paymentTitles }}</div>
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
                                        Daty farany hanaovana fandoavam-bola:
                                        <div class="deadline-date">{{ date('d/m/Y', strtotime($payment_deadline)) }}</div>
                                    </div>
                                    <div class="thanks">
                                        misaotra amin'ny fiaraha-miasa sy ny fandraisana andrakitra
                                    </div>
                                    <div>
                                        <div class="f-left">Statut: {{ $status }}</div>
                                        <div class="f-right">{{ date('d/m/y') }}</div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Empty grid cell for remaining slots --}}
                            <div class="notif empty">
                                Emplacement vide
                            </div>
                        @endif
                    @endfor
                @endfor
            </div>
        @endfor
    @else
        <div class="page">
            <div class="notif empty" style="grid-column: 1 / -1;">
                <strong>Aucun élève impayé trouvé</strong><br>
                <small>Vérifiez les critères de sélection</small>
            </div>
        </div>
    @endif
</body>
</html>                                            <span class="amount-value-highlight">{{ number_format($amountDue, 0, ',', ' ') }} Ariary</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="footer">
                                    <div class="deadline-info">
                                        Daty farany hanaovana fandoavam-bola:
                                        <div class="deadline-date">{{ date('d/m/Y', strtotime($payment_deadline)) }}</div>
                                    </div>
                                    <div class="thanks">
                                        misaotra amin'ny fiaraha-miasa sy ny fandraisana andrakitra
                                    </div>
                                    <div>
                                        <div class="f-left">Statut: {{ $status }}</div>
                                        <div class="f-right">{{ date('d/m/y') }}</div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Empty grid cell for remaining slots --}}
                            <div class="notif empty">
                                Emplacement vide
                            </div>
                        @endif
                    @endfor
                @endfor
            </div>
        @endfor
    @else
        <div class="page">
            <div class="notif empty" style="grid-column: 1 / -1;">
                <strong>Aucun élève impayé trouvé</strong><br>
                <small>Vérifiez les critères de sélection</small>
            </div>
        </div>
    @endif
</body>
</html>