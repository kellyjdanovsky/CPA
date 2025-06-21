<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçus_Batch_{{ date('Y-m-d_H-i-s') }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 0;
            width: 58mm;
            font-size: 9pt;
            font-weight: bold;
            line-height: 1.1;
            color: #000;
        }
        
        .receipt {
            width: 54mm;
            margin: 0 auto 5mm auto;
            padding: 2mm;
            page-break-after: always;
        }
        
        .receipt:last-child {
            page-break-after: avoid;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        
        .title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        
        .separator {
            text-align: center;
            margin: 1mm 0;
        }
        
        .info-section {
            margin-bottom: 2mm;
        }
        
        .info-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5mm;
            font-size: 8pt;
        }
        
        .info-label {
            font-weight: bold;
            width: 40%;
        }
        
        .info-value {
            width: 60%;
            text-align: right;
        }
        
        .payments-section {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 2mm 0;
            margin: 2mm 0;
        }
        
        .payments-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1mm;
            font-size: 9pt;
        }
        
        .payment-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5mm;
            font-size: 8pt;
        }
        
        .payment-name {
            width: 65%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .payment-amount {
            width: 35%;
            text-align: right;
            font-weight: bold;
        }
        
        .totals-section {
            margin: 2mm 0;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5mm;
            font-size: 8pt;
        }
        
        .total-line.main {
            font-size: 9pt;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 1mm;
        }
        
        .status-section {
            background-color: #f0f0f0;
            padding: 1mm;
            margin: 2mm 0;
            border: 1px solid #000;
        }
        
        .status-line {
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            margin-bottom: 0.5mm;
        }
        
        .amount-words {
            margin: 2mm 0;
            font-size: 7pt;
            text-align: center;
            font-style: italic;
        }
        
        .footer {
            border-top: 1px dashed #000;
            padding-top: 2mm;
            margin-top: 2mm;
        }
        
        .footer-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5mm;
            font-size: 8pt;
        }
        
        .signature-section {
            margin-top: 3mm;
            text-align: center;
            font-size: 8pt;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 30mm;
            margin: 2mm auto;
            height: 5mm;
        }
        
        @media print {
            body {
                width: 58mm;
                margin: 0;
                padding: 0;
            }
            
            .receipt {
                width: 54mm;
                margin: 0 auto 5mm auto;
                padding: 2mm;
                page-break-after: always;
            }
            
            .receipt:last-child {
                page-break-after: avoid;
            }
            
            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @foreach($receiptsData as $receiptData)
        @php
            $student = $receiptData['student'];
            $payments = $receiptData['payments'];
            $totalAmount = $receiptData['totalAmount'];
            $amountToPay = $receiptData['amountToPay'];
            $cashAmount = $receiptData['cashAmount'];
            $status = $receiptData['status'];
            $referenceCode = $receiptData['referenceCode'];
        @endphp
        
        <div class="receipt">
            <!-- Header -->
            <div class="header">
                <div class="title">REÇU DE PAIEMENT</div>
                <div class="separator">-------------------</div>
            </div>
            
            <!-- En-tête école -->
            <div class="school-header">
                {{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}<br>
                AVARATEZANA
            </div>

            <!-- Titre REÇU -->
            <div class="receipt-box">
                REÇU
            </div>

            <!-- Référence et date -->
            <div class="ref-box">
                RÉF: {{ $referenceCode }} | {{ date('d/m/Y H:i') }}
            </div>

            <!-- Informations étudiant et paiement -->
            <div class="student-box">
                <div class="student-name">{{ strtoupper($student->user->name) }}</div>
                <div class="student-class">{{ $student->my_class->name }}</div>
                @foreach($payments as $payment)
                    <div class="payment-line">{{ strtoupper($payment->title) }} - {{ number_format($payment->amount, 0, ',', ' ') }} AR</div>
                @endforeach
            </div>
            
            <!-- Section HISTORIQUE -->
            <div class="history-header">HISTORIQUE</div>

            <table class="history-table">
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>PAYÉ</th>
                        <th>RESTE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ date('d/m/y') }}</td>
                        <td>{{ number_format($amountToPay, 0, ',', ' ') }} Ar</td>
                        <td>{{ number_format($cashAmount, 0, ',', ' ') }} Ar</td>
                    </tr>
                    @if($status === 'ADRA' && $cashAmount > 0)
                    <tr>
                        <td>{{ date('d/m/y') }}</td>
                        <td>{{ number_format($cashAmount, 0, ',', ' ') }} Ar</td>
                        <td>0 Ar</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <!-- Mode de paiement -->
            <div class="payment-mode">
                Mode: {{ $status }}{{ $status === 'ADRA' ? ' + CASH' : '' }}
            </div>

            <!-- Reste à payer -->
            <div class="balance-box">
                Reste à payer: {{ $status === 'ADRA' && $cashAmount > 0 ? '0' : number_format($cashAmount, 0, ',', ' ') }} Ar
            </div>

            <!-- Informations caissier -->
            <div class="cashier-box">
                <div class="cashier-name">CAISSIER: {{ strtoupper(Auth::user()->name ?? 'ADMINISTRATEUR') }}</div>
                <div class="thank-you">MERCI POUR VOTRE PAIEMENT</div>
            </div>

            <!-- Ligne de découpe -->
            <div class="cut-line">
                ✂ - - - - - - - - - - - - - - - - - - - - - - ✂
            </div>
        </div>
    @endforeach
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
