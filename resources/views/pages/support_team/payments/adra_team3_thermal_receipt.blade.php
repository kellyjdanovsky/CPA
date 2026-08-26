<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu_{{ $referenceCode }}_{{ $student->user->name }}</title>
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

        .receipt-box {
            border: 1.5px solid #000;
            text-align: center;
            font-size: 9pt;
            font-weight: 900;
            padding: 1mm;
            margin-bottom: 1mm;
            background-color: #f0f0f0;
            text-transform: uppercase;
        }

        .ref-box {
            text-align: center;
            font-size: 7pt;
            padding: 1mm 0;
            margin-bottom: 1.5mm;
            border-bottom: 1px dashed #000;
        }

        .student-box {
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

        .student-class {
            font-size: 7.5pt;
        }

        .badge-status {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 0.5mm 1.5mm;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 2px;
            margin-top: 1mm;
        }

        .section-header {
            background-color: #333;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 7pt;
            padding: 1mm;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .history-table {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 2mm;
        }

        .history-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 1mm 0.5mm;
            font-size: 6.5pt;
            font-weight: 900;
            text-align: center;
        }

        .history-table td {
            border: 1px solid #000;
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

        .balance-box {
            border: 2px solid #000;
            text-align: center;
            padding: 2mm 1mm;
            margin-bottom: 2mm;
            background-color: #f0fdf4;
        }

        .balance-title {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
            text-transform: uppercase;
        }

        .balance-amount {
            font-size: 11pt;
            font-weight: 900;
        }

        .cashier-box {
            border: 1px solid #000;
            text-align: center;
            padding: 1.5mm;
            margin-bottom: 2mm;
            font-size: 7pt;
        }

        .cashier-name {
            font-weight: 900;
        }

        .thank-you {
            font-size: 6.5pt;
            margin-top: 1mm;
            font-style: italic;
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
        <!-- En-tête école -->
        <div class="school-header">
            {{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}<br>
            <span style="font-size: 6.5pt; font-weight: normal;">AVARATETEZANA AMPITATAFIKA</span>
        </div>

        <!-- Titre REÇU -->
        <div class="receipt-box">
            REÇU ENCAISSEMENT {{ $status }}
        </div>

        <!-- Référence et date -->
        <div class="ref-box">
            <strong>RÉF: {{ $referenceCode }}</strong><br>
            Date: {{ date('d/m/Y H:i') }}
        </div>

        <!-- Informations étudiant -->
        <div class="student-box">
            <div class="student-name">{{ strtoupper($student->user->name) }}</div>
            <div class="student-class">Classe: {{ $student->my_class->name }}</div>
            <div>
                <span class="badge-status">
                    RÉGIME : {{ $status }} ({{ $status === 'ADRA' ? 'ADRA 75% | PARENT 25%' : 'TEAM 3 100%' }})
                </span>
            </div>
        </div>

        <!-- Détails des rubriques payées -->
        <div class="section-header">DÉTAIL DES FRAIS CONCERNÉS</div>
        <table class="history-table">
            <thead>
                <tr>
                    <th style="width: 45%;">RUBRIQUE</th>
                    <th style="width: 25%;">TOTAL</th>
                    <th style="width: 30%;">{{ $status === 'ADRA' ? 'PART PARENT' : 'PART TEAM3' }}</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $grandTotal = 0;
                    $totalOrg = 0;
                    $totalParent = 0;
                @endphp
                @foreach($payments as $payment)
                    @php 
                        $grandTotal += $payment->amount;
                        if ($status === 'ADRA') {
                            $orgPart = $payment->amount * 0.75;
                            $parentPart = $payment->amount * 0.25;
                        } else {
                            $orgPart = $payment->amount;
                            $parentPart = 0;
                        }
                        $totalOrg += $orgPart;
                        $totalParent += $parentPart;
                    @endphp
                    <tr>
                        <td style="text-align: left;">{{ strtoupper($payment->title) }}</td>
                        <td style="text-align: right;">{{ number_format($payment->amount, 0, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($status === 'ADRA' ? $parentPart : $orgPart, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Synthèse financière et pourcentages -->
        <div class="breakdown-box">
            <div class="breakdown-row">
                <span>TOTAL DES FRAIS (100%) :</span>
                <strong>{{ number_format($grandTotal, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="breakdown-row" style="color: #1b5e20;">
                <span>PRISE EN CHARGE {{ $status }} ({{ $status === 'ADRA' ? '75%' : '100%' }}) :</span>
                <strong>- {{ number_format($totalOrg, 0, ',', ' ') }} Ar</strong>
            </div>
            <div class="breakdown-row" style="border-top: 1px dotted #000; padding-top: 1mm;">
                <span>PART DUE PAR LE PARENT ({{ $status === 'ADRA' ? '25%' : '0%' }}) :</span>
                <strong>{{ number_format($totalParent, 0, ',', ' ') }} Ar</strong>
            </div>
        </div>

        <!-- Montant Payé / Encaissé -->
        <div class="balance-box">
            <div class="balance-title">
                {{ $status === 'ADRA' ? 'MONTANT ENCAISSÉ PARENT (25%)' : 'MONTANT PRIS EN CHARGE (100%)' }}
            </div>
            <div class="balance-amount">
                {{ number_format($status === 'ADRA' ? $cashAmount : $grandTotal, 0, ',', ' ') }} Ar
            </div>
        </div>

        <!-- Informations caissier -->
        <div class="cashier-box">
            <div class="cashier-name">CAISSIER : {{ strtoupper(Auth::user()->name ?? 'ADMINISTRATEUR') }}</div>
            <div class="thank-you">Merci pour votre confiance</div>
        </div>

        <!-- Ligne de découpe -->
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
