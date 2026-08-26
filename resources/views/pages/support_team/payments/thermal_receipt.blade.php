<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu_{{ $pr->ref_no.'_'.$sr->user->name }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 58mm;
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.15;
            color: #000;
            background: #fff;
        }

        .container {
            width: 54mm;
            margin: 0 auto;
            padding: 2mm 1mm;
        }

        .school-header {
            text-align: center;
            font-size: 8pt;
            font-weight: 900;
            margin-bottom: 2mm;
            text-transform: uppercase;
            line-height: 1.1;
            border-bottom: 1px solid #000;
            padding-bottom: 1.5mm;
        }

        .receipt-title {
            text-align: center;
            font-size: 9pt;
            font-weight: 900;
            margin: 1.5mm 0;
            text-transform: uppercase;
            border: 1.5px solid #000;
            padding: 1mm;
            background-color: #f0f0f0;
        }

        .ref-box {
            text-align: center;
            font-size: 7pt;
            margin-bottom: 1.5mm;
            padding: 0.5mm 0;
            border-bottom: 1px dashed #000;
        }

        .info-section {
            border: 1px solid #000;
            padding: 1.5mm;
            margin-bottom: 2mm;
            font-size: 7.5pt;
            background: #fafafa;
        }

        .student-name {
            font-weight: 900;
            font-size: 8.5pt;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
        }

        .badge-status {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 0.5mm 1.5mm;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 2px;
            margin-top: 1mm;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2mm 0;
            border: 1px solid #000;
        }

        .payment-table th {
            background-color: #f0f0f0;
            border-bottom: 1px solid #000;
            padding: 1mm 0.5mm;
            font-size: 6.5pt;
            font-weight: 900;
            text-align: center;
        }

        .payment-table td {
            border-bottom: 1px solid #ddd;
            padding: 1mm 0.5mm;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .breakdown-box {
            border: 1px solid #000;
            padding: 1.5mm;
            margin-bottom: 2mm;
            font-size: 7pt;
            background: #fdfdfd;
        }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8mm;
        }

        .total-section {
            border: 2px solid #000;
            padding: 2mm 1mm;
            margin: 2mm 0;
            text-align: center;
            background-color: #f5f5f5;
        }

        .total-title {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
        }

        .amount {
            font-size: 11pt;
            font-weight: 900;
        }

        .footer {
            border: 1px solid #000;
            padding: 1.5mm;
            margin-top: 2mm;
            text-align: center;
            font-size: 7pt;
        }

        .cut-line {
            text-align: center;
            margin-top: 2mm;
            font-size: 6pt;
            border-top: 1px dashed #000;
            padding-top: 1mm;
        }

        @media print {
            body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .container {
                width: 54mm !important;
                margin: 0 auto !important;
                padding: 1mm !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    @php
        use App\Helpers\DateHelper;
        
        $sortedReceipts = $receipts->sortBy('created_at');
        $totalPaid = $sortedReceipts->sum('amt_paid');
        $paymentMethod = $sortedReceipts->last() ? ($sortedReceipts->last()->payment_method ?? $sortedReceipts->last()->methode ?? 'CASH') : 'CASH';
        $formattedMethod = strtoupper($paymentMethod);
        if (in_array($formattedMethod, ['CASH', 'ESPÈCES', 'ESPECES'])) {
            $formattedMethod = 'ESPÈCES';
        }
        
        $currentDate = date('d/m/Y H:i');
        $receiptRef = $pr->ref_no ?? 'N/A';
        $studentStatus = $sr->user->status ?? 'Normal';
    @endphp

    <!-- En-tête école -->
    <div class="school-header">
        {{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}<br>
        <span style="font-size: 6.5pt; font-weight: normal;">AVARATETEZANA AMPITATAFIKA</span>
    </div>

    <div class="receipt-title">REÇU DE PAIEMENT</div>

    <!-- Référence du reçu -->
    <div class="ref-box">
        <strong>RÉF : {{ $receiptRef }}</strong><br>
        Date : {{ $currentDate }}
    </div>

    <!-- Informations élève & paiement -->
    <div class="info-section">
        <div class="student-name">{{ strtoupper($sr->user->name) }}</div>
        <div>Classe : {{ $sr->my_class->name }} {{ $sr->section->name ? '('.$sr->section->name.')' : '' }}</div>
        <div style="margin-top: 0.5mm;">Frais : {{ $payment->title }}</div>
        
        @if($studentStatus === 'ADRA' || $studentStatus === 'TEAM3' || $studentStatus === 'Team3')
            <div>
                <span class="badge-status">
                    RÉGIME : {{ $studentStatus }} ({{ $studentStatus === 'ADRA' ? 'ADRA 75% | PARENT 25%' : 'TEAM 3 100%' }})
                </span>
            </div>
        @endif
    </div>

    <!-- Détail ADRA / TEAM3 si applicable -->
    @if($studentStatus === 'ADRA')
        @php
            $fraisTotal = $payment->amount;
            $partAdra = $fraisTotal * 0.75;
            $partParent = $fraisTotal * 0.25;
        @endphp
        <div class="breakdown-box">
            <div class="breakdown-row">
                <span>MONTANT GLOBAL FRAIS :</span>
                <strong>{{ number_format($fraisTotal, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="breakdown-row" style="color: #1b5e20;">
                <span>PRISE EN CHARGE ADRA (75%) :</span>
                <strong>- {{ number_format($partAdra, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="breakdown-row" style="border-top: 1px dotted #000; padding-top: 0.5mm;">
                <span>PART DUE PARENT (25%) :</span>
                <strong>{{ number_format($partParent, 0, ',', ' ') }} Ar</strong>
            </div>
        </div>
    @elseif($studentStatus === 'TEAM3' || $studentStatus === 'Team3')
        @php
            $fraisTotal = $payment->amount;
        @endphp
        <div class="breakdown-box">
            <div class="breakdown-row">
                <span>MONTANT GLOBAL FRAIS :</span>
                <strong>{{ number_format($fraisTotal, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="breakdown-row" style="color: #1b5e20;">
                <span>PRISE EN CHARGE TEAM 3 (100%) :</span>
                <strong>- {{ number_format($fraisTotal, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="breakdown-row" style="border-top: 1px dotted #000; padding-top: 0.5mm;">
                <span>PART DUE PARENT (0%) :</span>
                <strong>0 Ar</strong>
            </div>
        </div>
    @endif

    <!-- Historique des versements récents -->
    @if($sortedReceipts->count() > 0)
    <table class="payment-table">
        <thead>
            <tr>
                <th>DATE</th>
                <th>VERSÉ</th>
                <th>RESTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sortedReceipts->take(3) as $receipt)
            <tr>
                <td style="text-align: center;">{{ DateHelper::formatFrenchShort($receipt->created_at) }}</td>
                <td style="text-align: right;">{{ number_format($receipt->amt_paid, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($receipt->balance, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="text-align: center; font-size: 7.5pt; margin: 1mm 0;">
        <strong>Mode de règlement : {{ $formattedMethod }}</strong>
    </div>
    @endif

    <!-- Reste à payer -->
    <div class="total-section">
        <div class="total-title">RESTE À PAYER (SOLDE)</div>
        <div class="amount">{{ number_format($pr->balance, 0, ',', ' ') }} Ar</div>
    </div>

    <!-- Caissier -->
    <div class="footer">
        <div><strong>Caissier :</strong> {{ strtoupper(Auth::user()->name) }}</div>
        <div style="font-size: 6.5pt; margin-top: 0.5mm;">Merci pour votre paiement</div>
    </div>

    <div class="cut-line">
        ✂ - - - - - - - - - - - - - - - - - - - - - - ✂
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 300);
    });
</script>
</body>
</html>