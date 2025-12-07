<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu_{{ $pr->ref_no.'_'.$sr->user->name }}</title>
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
        
        /* Historique des paiements simplifié */
        .payment-history {
            font-size: 9pt;
            width: 100%;
            margin-bottom: 2mm;
            border: 1px solid #000;
            background-color: #f8f8f8;
            overflow: hidden;
        }
        
        .payment-history-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 0.5mm;
            font-size: 10pt;
            background-color: #343a40;
            color: white;
            padding: 1mm;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }
        
        .payment-duration {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
            padding: 0.5mm;
            background-color: #f8f9fa;
            border-bottom: 1px solid #000;
        }
        
        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-history-table th,
        .payment-history-table td {
            border-bottom: 1px solid #000;
            padding: 1mm 0.5mm;
            font-size: 8pt;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .payment-history-table th {
            font-weight: bold;
            font-size: 8pt;
            background-color: #e9ecef;
            text-transform: uppercase;
        }
        
        .payment-history-table td.text-right,
        .payment-history-table td:nth-child(2),
        .payment-history-table td:nth-child(3) {
            text-align: right;
        }
        
        .payment-history-table .amount {
            font-weight: bold;
            font-size: 9pt;
        }
        
        .payment-history-table .date {
            font-weight: bold;
            font-size: 8pt;
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
        
        .method-team {
            background-color: #fff3e0 !important;
            border-color: #ff9800 !important;
            color: #e65100 !important;
        }
        
        .method-bank {
            background-color: #e8eaf6 !important;
            border-color: #3f51b5 !important;
            color: #1a237e !important;
        }
        
        .method-cheque {
            background-color: #f3e5f5 !important;
            border-color: #9c27b0 !important;
            color: #4a148c !important;
        }
        
        .method-other {
            background-color: #f5f5f5 !important;
            border-color: #607d8b !important;
            color: #263238 !important;
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
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        {{ strtoupper(Qs::getSetting('system_name')) }}
    </div>

    <div class="receipt-title">Reçu</div>

    @php
        use App\Helpers\DateHelper;
        
        // Trier les reçus par date de création
        $sortedReceipts = $receipts->sortBy('created_at');
        
        // Récupérer la méthode de paiement du dernier reçu
        $paymentMethod = $sortedReceipts->last() ? 
            ($sortedReceipts->last()->payment_method ?? 
             $sortedReceipts->last()->methode ?? 'CASH') : 'CASH';
        
        // Formater la méthode de paiement pour l'affichage
        $formattedMethod = strtoupper($paymentMethod);
        if ($formattedMethod == 'CASH' || $formattedMethod == 'ESPÈCES' || $formattedMethod == 'ESPECES') {
            $formattedMethod = 'CASH';
        }
        
        // Formater la date actuelle
        $currentDate = date('d/m/Y H:i');
        
        // Référence du reçu
        $receiptRef = $pr->ref_no ?? 'N/A';
    @endphp

    <!-- Référence du reçu -->
    <div style="text-align: center; font-size: 8pt; margin-bottom: 1mm;">
        <strong>REF: {{ $receiptRef }}</strong> | {{ $currentDate }}
    </div>

    <!-- Informations essentielles -->
    <div class="student-info">
        <p class="student-name">{{ strtoupper($sr->user->name) }}</p>
        <p>{{ $sr->my_class->name }} {{ $sr->section->name ? '('.$sr->section->name.')' : '' }}</p>
        <p>{{ $payment->title }} - <strong>{{ DateHelper::formatAmount($payment->amount) }}</strong></p>
    </div>

    <!-- Historique des paiements -->
    @if($sortedReceipts->count() > 0)
    <div class="payment-history">
        <div class="payment-history-title">Historique des Paiements</div>
        <table class="payment-history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Payé</th>
                    <th>Reste</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortedReceipts as $r)
                    @if($r->amt_paid != 0)
                    <tr>
                        <td>{{ DateHelper::formatForPaymentHistory($r->created_at) }}</td>
                        <td>{{ DateHelper::formatAmount($r->amt_paid) }}</td>
                        <td>{{ DateHelper::formatAmount($r->balance) }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Méthode de paiement -->
    <div style="text-align: center; font-size: 9pt; margin: 1mm 0;">
        <strong>Mode: {{ $formattedMethod }}</strong>
    </div>
    @endif

    <!-- Résumé lié au statut étudiant -->
    @php
        $studentStatus = $sr->user->status ?? 'Normal';
        $isAdra = strtoupper($studentStatus) == 'ADRA';
        $totalAmount = $payment->amount;
        $paidAmount = $pr->amt_paid;
        
        // Logique ADRA : Acquitté si 25% payé
        $adraCleared = $isAdra && ($paidAmount >= ($totalAmount * 0.25));
        
        // Montant en lettres
        $amountInWords = ucfirst(\App\Helpers\DateHelper::convertirMontantEnLettres($paidAmount));
    @endphp

    <div class="payment-summary">
        <table class="payment-summary-table">
            @if($pr->balance == 0 || $adraCleared)
            <tr class="highlight-row">
                <td class="amount-label">Statut:</td>
                <td class="amount-value amount" style="color: #28a745;">Paiement Acquitté</td>
            </tr>
            @else
            <tr class="highlight-row">
                <td class="amount-label">Reste à payer:</td>
                @if($isAdra)
                    {{-- Pour ADRA, le reste à payer est basé sur les 25% --}}
                    @php 
                        $adraBalance = max(0, ($totalAmount * 0.25) - $paidAmount);
                    @endphp
                    <td class="amount-value amount">{{ DateHelper::formatAmount($adraBalance) }}</td>
                @else
                    <td class="amount-value amount">{{ DateHelper::formatAmount($pr->balance) }}</td>
                @endif
            </tr>
            @endif
            
            {{-- Affichage du montant payé en lettres --}}
            <tr>
                <td colspan="2" style="font-size: 9pt; font-style: italic; text-align: center; border-top: 1px solid #000;">
                     La somme de : <strong>{{ $amountInWords }}</strong>
                </td>
            </tr>
        </table>
    </div>

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