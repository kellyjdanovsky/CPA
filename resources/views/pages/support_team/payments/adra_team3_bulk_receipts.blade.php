<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression en masse - Reçus ADRA & TEAM 3</title>
    <style>
        @page {
            size: 58mm auto; /* Largeur de 58mm pour l'imprimante thermique */
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .page-break {
            page-break-after: always;
            margin-bottom: 20mm;
            border-bottom: 1px dashed #000;
            position: relative;
        }
        
        .page-break:after {
            content: '✂';
            position: absolute;
            bottom: -5mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12pt;
            background-color: white;
            padding: 0 2mm;
        }
        
        .receipt {
            width: 58mm;
            margin: 0 auto;
            background-color: white;
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.2;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        .container {
            width: 54mm;
            margin: 0 auto;
            padding: 1mm;
        }
        
        /* En-tête avec hiérarchie typographique claire */
        .header {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 1mm;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
            text-transform: uppercase;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 1mm 0;
            text-transform: uppercase;
            background-color: #f0f0f0;
            padding: 1mm;
            border: 1px solid #000;
        }
        
        /* Informations étudiant avec mise en forme claire */
        .student-info {
            font-size: 10pt;
            margin-bottom: 2mm;
            border: 1px solid #000;
            padding: 1mm;
            background-color: #f8f8f8;
        }
        
        .student-info p {
            margin: 1mm 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .student-info .student-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .student-info .student-class {
            font-size: 10pt;
            font-weight: bold;
        }
        
        /* Badges de statut améliorés */
        .status-badge {
            display: inline-block;
            padding: 0.5mm 1mm;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 1mm;
            border: 1px solid #000;
        }
        
        .status-normal {
            background-color: #d4edda;
            color: #155724;
            border-color: #155724;
        }
        
        .status-adra {
            background-color: #cce7ff;
            color: #004085;
            border-color: #004085;
        }
        
        .status-team3 {
            background-color: #fff3cd;
            color: #856404;
            border-color: #856404;
        }
        
        /* Tableau des paiements */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
            border: 1px solid #000;
        }
        
        .payment-table th {
            background-color: #343a40;
            color: white;
            font-size: 8pt;
            padding: 1mm;
            text-align: left;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }
        
        .payment-table td {
            padding: 1mm;
            font-size: 8pt;
            border-bottom: 1px dashed #ccc;
        }
        
        .payment-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Résumé de paiement avec mise en forme professionnelle */
        .payment-summary {
            margin: 2mm 0;
            border: 1px solid #000;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        
        .payment-summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-summary-table tr td {
            padding: 1mm;
            font-size: 10pt;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .payment-summary-table tr:not(:last-child) td {
            border-bottom: 1px solid #000;
        }
        
        .payment-summary-table .amount-label {
            font-weight: bold;
            font-size: 11pt;
            width: 60%;
        }
        
        .payment-summary-table .amount-value {
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
            width: 40%;
        }
        
        .payment-summary-table .highlight-row td {
            background-color: #fff3cd;
            font-weight: bold;
            font-size: 11pt;
            border: 1px solid #856404;
            padding: 1mm;
            white-space: nowrap;
        }
        
        .amount-in-words {
            margin-top: 15px;
            font-size: 12px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            font-style: italic;
        }
        
        /* Signature et informations du caissier */
        .sign {
            text-align: center;
            font-size: 10pt;
            margin-top: 2mm;
            padding: 1mm;
            border: 1px solid #000;
            background-color: #f8f8f8;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .cashier-info {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        /* Code-barres */
        .barcode {
            text-align: center;
            margin: 2mm 0;
        }
        
        .barcode img {
            max-width: 100%;
            height: auto;
        }
        
        /* Références multiples */
        .reference-codes {
            font-size: 7pt;
            margin-top: 1mm;
            text-align: center;
            padding: 1mm;
            border: 1px dashed #ccc;
            background-color: #f8f8f8;
        }
        
        /* Classes utilitaires */
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: bold !important;
        }
        
        .amount {
            font-weight: bold !important;
            font-size: 11pt !important;
        }
        
        /* Contrôles d'impression */
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-btn {
            background-color: #3a7bd5;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .back-btn {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #333;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 10px;
        }
        
        /* Optimisations pour impression */
        @media print {
            body {
                background-color: white;
                width: 58mm;
                margin: 0;
                padding: 0;
            }
            
            .print-controls {
                display: none !important;
            }
            
            .receipt {
                margin: 0;
                padding: 0;
                width: 58mm;
                box-shadow: none;
            }
            
            .container {
                width: 54mm;
            }
            
            .page-break {
                height: 10mm;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <a href="{{ route('payments.adra_team3') }}" class="back-btn">Retour</a>
        <button onclick="window.print()" class="print-btn">Imprimer tous les reçus</button>
    </div>
    
    @foreach($receipts as $index => $receipt)
    <div class="receipt">
        <div class="container">
            <div class="header">
                {{ strtoupper($s['system_name'] ?? 'SYSTÈME SCOLAIRE') }}
            </div>

            <div class="receipt-title">Reçu {{ $receipt['status'] }}</div>

            <!-- Référence du reçu -->
            <div style="text-align: center; font-size: 8pt; margin-bottom: 1mm;">
                <strong>REF: {{ $receipt['reference_code'] }}</strong> | {{ $receipt['receipt_date'] }}
            </div>

            <!-- Informations essentielles -->
            <div class="student-info">
                <p class="student-name">{{ strtoupper($receipt['student']->user->name) }}</p>
                <p class="student-class">{{ $receipt['student']->my_class->name }}</p>
                <p>
                    @if($receipt['status'] == 'ADRA')
                        <span class="status-badge status-adra">ADRA (75%)</span>
                    @elseif($receipt['status'] == 'TEAM3')
                        <span class="status-badge status-team3">TEAM 3 (100%)</span>
                    @else
                        <span class="status-badge status-normal">{{ $receipt['status'] }}</span>
                    @endif
                </p>
            </div>

            <!-- Tableau des paiements -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Paiement</th>
                        <th class="text-right">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt['payment_details'] as $payment)
                        <tr>
                            <td>{{ $payment['title'] }}</td>
                            <td class="text-right">{{ number_format($payment['paid_amount'], 0, ',', ' ') }}Ar</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Résumé simplifié -->
            <div class="payment-summary">
                <table class="payment-summary-table">
                    <tr>
                        <td class="amount-label">Total:</td>
                        <td class="amount-value amount">{{ number_format($receipt['total_amount'], 0, ',', ' ') }}Ar</td>
                    </tr>
                    @if($receipt['status'] == 'ADRA')
                        <tr class="highlight-row">
                            <td class="amount-label">ADRA (75%):</td>
                            <td class="amount-value amount">{{ number_format($receipt['total_amount'], 0, ',', ' ') }}Ar</td>
                        </tr>
                    @endif
                </table>
            </div>
            
            <!-- Montant en lettres -->
            <div class="amount-in-words">
                <p><strong>Arrêté à la somme de :</strong> {{ NumberToWords::convert($receipt['total_amount']) }}</p>
            </div>

            <!-- Code-barres pour la référence -->
            @if(isset($receipt['reference_codes']) && count($receipt['reference_codes']) > 1)
                <div class="reference-codes">
                    Références: 
                    @foreach($receipt['reference_codes'] as $index => $code)
                        {{ $index > 0 ? ', ' : '' }}{{ $code }}
                    @endforeach
                </div>
            @else
                <div class="barcode">
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt['reference_code'], 'C39', 1, 30) }}" alt="Barcode">
                </div>
            @endif

            <!-- Informations simplifiées -->
            <div class="sign">
                <div class="cashier-info">
                    <strong>Caissier:</strong> {{ Auth::user()->name }}
                </div>
                <div style="font-size: 8pt; margin-top: 1mm;">
                    Merci pour votre paiement
                </div>
            </div>
        </div>
    </div>
    
    @if($index < count($receipts) - 1)
    <div class="page-break"></div>
    @endif
    @endforeach

    <script>
        // Auto-impression après chargement complet
        window.addEventListener('load', function() {
            // Attendre que toutes les images soient chargées
            setTimeout(function() {
                // window.print(); // Décommentez pour activer l'impression automatique
            }, 1000);
        });
    </script>
</body>
</html>