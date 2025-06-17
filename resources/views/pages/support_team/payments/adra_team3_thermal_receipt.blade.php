<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu_ADRA_TEAM3_{{ $student->user->name }}</title>
    <style>
        @page {
            size: 58mm auto; /* Largeur de 58mm pour l'imprimante thermique */
            margin: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif; /* Police plus lisible */
            margin: 0;
            padding: 0;
            width: 58mm; /* Largeur fixe pour imprimante thermique 58mm */
            font-size: 10pt; /* Taille de police réduite pour s'adapter à la largeur */
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            line-height: 1.2;
        }
        
        /* Application globale du gras pour tous les éléments critiques */
        body, .container, .header, .receipt-info, .student-info, .payment-info, 
        .payment-table, .payment-table th, .payment-table td, .footer, .sign, 
        .cut-line, strong, td, th, div, span, .amount, .date, .critical-info {
            font-weight: bold !important;
        }
        
        .container {
            width: 54mm; /* Légèrement plus petit que la largeur du body pour les marges */
            margin: 0 auto;
            padding: 1mm;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 1mm;
        }
        
        .logo img {
            max-width: 15mm;
            height: auto;
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
        
        /* Informations du reçu avec formatage amélioré */
        .receipt-info {
            font-size: 9pt;
            margin-bottom: 1mm;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1mm;
        }
        
        .receipt-info .ref-number {
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            background-color: #f8f8f8;
            padding: 0.5mm;
            border: 1px solid #000;
            margin-bottom: 1mm;
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
        
        .payment-summary-title {
            text-align: center;
            font-weight: bold;
            background-color: #343a40;
            color: white;
            border-bottom: 1px solid #000;
            padding: 1mm;
            font-size: 10pt;
            text-transform: uppercase;
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
        
        /* Pied de page avec informations critiques */
        .footer {
            text-align: center;
            font-size: 9pt;
            margin: 1mm 0;
            font-weight: bold;
            padding: 1mm;
            border: 1px solid #000;
            background-color: #f8f9fa;
        }
        
        .footer .critical-amount {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 1mm;
            padding: 1mm;
            background-color: #fff;
            border: 1px solid #000;
        }
        
        .footer .payment-date {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 1mm;
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
        
        /* Ligne de découpe */
        .cut-line {
            border-top: 1px dashed #000;
            margin: 3mm 0;
            height: 1mm;
            position: relative;
        }
        
        .cut-line:after {
            content: '✂';
            position: absolute;
            top: -2mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 9pt;
            background-color: white;
            padding: 0 1mm;
            font-weight: bold;
        }
        
        /* Date et heure avec formatage localisé */
        .date-time {
            font-size: 8pt;
            text-align: center;
            margin-top: 1mm;
            font-weight: bold;
        }
        
        .current-datetime {
            font-size: 8pt;
            font-weight: bold;
            color: #666;
            margin-top: 0.5mm;
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
        
        .date {
            font-weight: bold !important;
            font-size: 9pt !important;
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
        
        /* Optimisations pour impression thermique et numérique */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .container {
                width: 54mm !important;
            }
            
            .payment-summary, .footer {
                border: 1px solid #000 !important;
            }
            
            .status-badge {
                border: 1px solid #000 !important;
            }
            
            .payment-summary-table .highlight-row td {
                font-size: 11pt !important;
            }
            
            .payment-summary-table tr td {
                font-size: 10pt !important;
            }
            
            .footer .critical-amount {
                font-size: 12pt !important;
            }
            
            .cashier-info {
                font-size: 10pt !important;
            }
            
            .student-name {
                font-size: 11pt !important;
            }
            
            .student-class {
                font-size: 10pt !important;
            }
            
            .amount {
                font-size: 11pt !important;
            }
            
            .date {
                font-size: 9pt !important;
            }
            
            .receipt-actions {
                display: none !important;
            }
        }
        
        /* Styles pour les méthodes de paiement */
        .method-cash {
            background-color: #e8f5e9 !important;
            border-color: #4caf50 !important;
            color: #2e7d32 !important;
        }
        
        .method-adra {
            background-color: #e3f2fd !important;
            border-color: #2196f3 !important;
            color: #0d47a1 !important;
        }
        
        .method-team3 {
            background-color: #fff3e0 !important;
            border-color: #ff9800 !important;
            color: #e65100 !important;
        }
        
        /* Responsive pour affichage numérique */
        @media screen and (min-width: 768px) {
            body {
                width: auto;
                max-width: 300px;
                margin: 20px auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            
            .container {
                width: 100%;
                padding: 0;
            }
            
            .receipt-actions {
                display: flex;
                justify-content: center;
                margin: 20px 0;
            }
            
            .receipt-action-btn {
                margin: 0 5px;
                padding: 8px 15px;
                border-radius: 4px;
                font-weight: bold;
                cursor: pointer;
            }
            
            .print-btn {
                background-color: #3a7bd5;
                color: white;
                border: none;
            }
            
            .back-btn {
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                color: #333;
            }
        }
    </style>
</head>
<body>
<div class="receipt-actions d-print-none">
    <a href="{{ route('payments.adra_team3') }}" class="btn receipt-action-btn back-btn">
        <i class="icon-arrow-left8 mr-2"></i> Retour
    </a>
    <button onclick="window.print()" class="btn receipt-action-btn print-btn">
        <i class="icon-printer4 mr-2"></i> Imprimer
    </button>
</div>

<div class="container">
    <div class="header">
        {{ strtoupper($s['system_name'] ?? 'SYSTÈME SCOLAIRE') }}
    </div>

    <div class="receipt-title">Reçu {{ $status }}</div>

    <!-- Référence du reçu -->
    <div style="text-align: center; font-size: 8pt; margin-bottom: 1mm;">
        <strong>REF: {{ $reference_code }}</strong> | {{ $receipt_date ?? date('d/m/Y H:i') }}
    </div>

    <!-- Informations essentielles -->
    <div class="student-info">
        <p class="student-name">{{ strtoupper($student->user->name) }}</p>
        <p class="student-class">{{ $student->my_class->name }}</p>
        <p>
            @if($status == 'ADRA')
                <span class="status-badge status-adra">ADRA (75%)</span>
            @elseif($status == 'TEAM3')
                <span class="status-badge status-team3">TEAM 3 (100%)</span>
            @else
                <span class="status-badge status-normal">{{ $status }}</span>
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
            @foreach($payment_details as $payment)
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
                <td class="amount-value amount">{{ number_format($total_amount, 0, ',', ' ') }}Ar</td>
            </tr>
            @if($status == 'ADRA')
                <tr class="highlight-row">
                    <td class="amount-label">ADRA (75%):</td>
                    <td class="amount-value amount">{{ number_format($total_amount, 0, ',', ' ') }}Ar</td>
                </tr>
            @endif
        </table>
    </div>
    
    <!-- Montant en lettres -->
    <div class="amount-in-words">
        <p><strong>Arrêté à la somme de :</strong> {{ NumberToWords::convert($total_amount) }}</p>
    </div>

    <!-- Code-barres pour la référence -->
    @if(isset($reference_codes) && count($reference_codes) > 1)
        <div class="reference-codes">
            Références: 
            @foreach($reference_codes as $index => $code)
                {{ $index > 0 ? ', ' : '' }}{{ $code }}
            @endforeach
        </div>
    @else
        <div class="barcode">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($reference_code, 'C39', 1, 30) }}" alt="Barcode">
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

    <div class="cut-line"></div>
</div>

<script>
    // Auto-impression pour les reçus thermiques
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
</script>
</body>
</html>