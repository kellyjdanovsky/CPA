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

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 58mm;
            font-size: 8pt;
            font-weight: normal;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            line-height: 1.1;
            color: #000;
        }

        .container {
            width: 56mm;
            margin: 0 auto;
            padding: 1mm;
        }

        .school-header {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 2mm;
            text-transform: uppercase;
            line-height: 1.0;
        }

        .receipt-box {
            border: 2px solid #000;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            padding: 1mm;
            margin-bottom: 1mm;
        }

        .ref-box {
            border: 1px solid #000;
            text-align: center;
            font-size: 7pt;
            padding: 1mm;
            margin-bottom: 1mm;
        }

        .student-box {
            border: 1px solid #000;
            padding: 1mm;
            margin-bottom: 1mm;
            font-size: 8pt;
        }

        .student-name {
            font-weight: bold;
            font-size: 9pt;
        }

        .student-class {
            font-size: 8pt;
        }

        .payment-line {
            font-size: 8pt;
            margin-bottom: 1mm;
        }
        
        .history-header {
            background-color: #666;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            padding: 1mm;
            margin-bottom: 0;
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
            padding: 1mm;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
        }

        .history-table td {
            border: 1px solid #000;
            padding: 1mm;
            font-size: 7pt;
            text-align: center;
        }

        .payment-mode {
            text-align: center;
            font-weight: bold;
            margin: 2mm 0;
            font-size: 8pt;
        }

        .balance-box {
            border: 1px solid #000;
            text-align: center;
            padding: 1mm;
            margin-bottom: 2mm;
            font-size: 8pt;
            font-weight: bold;
        }

        .cashier-box {
            border: 1px solid #000;
            text-align: center;
            padding: 1mm;
            margin-bottom: 2mm;
            font-size: 7pt;
        }

        .cashier-name {
            font-weight: bold;
            font-size: 8pt;
        }

        .thank-you {
            text-align: center;
            font-size: 7pt;
            margin-bottom: 2mm;
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
                width: 58mm;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 54mm;
                margin: 0 auto;
                padding: 2mm;
            }

            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête école -->
        <div class="school-header">
            {{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}<br>
            AVARATEZANA
        </div>

        <!-- Titre REÇU -->
        <div class="receipt-box">
            REÇU - {{ $status }}
        </div>

        <!-- Référence et date -->
        <div class="ref-box">
            RÉF: {{ $referenceCode }} | {{ date('d/m/Y H:i') }}
        </div>

        <!-- Informations étudiant -->
        <div class="student-box">
            <div class="student-name">{{ strtoupper($student->user->name) }}</div>
            <div class="student-class">Classe: {{ $student->my_class->name }}</div>
            <div style="margin-top: 2mm; font-size: 7pt;">Code Réf: {{ $referenceCode }}</div>
        </div>

        <!-- Détails des paiements -->
        <div class="history-header">PAIEMENTS SÉLECTIONNÉS</div>
        <table class="history-table">
            <thead>
                <tr>
                    <th>DÉSIGNATION</th>
                    <th>MONTANT 25%</th>
                </tr>
            </thead>
            <tbody>
                @php $total25 = 0; @endphp
                @foreach($payments as $payment)
                    @php 
                        $amount25 = $status === 'ADRA' ? ($payment->amount * 0.25) : 0;
                        $total25 += $amount25;
                    @endphp
                    <tr>
                        <td style="text-align: left;">{{ strtoupper($payment->title) }}</td>
                        <td>{{ number_format($amount25, 0, ',', ' ') }} Ar</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Montant à Payer (25%) -->
        <div class="balance-box" style="background-color: #e8f5e9;">
            <div style="font-size: 7pt; margin-bottom: 1mm;">MONTANT À PAYER (25%)</div>
            <div style="font-size: 10pt;">{{ number_format($cashAmount, 0, ',', ' ') }} Ar</div>
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
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
