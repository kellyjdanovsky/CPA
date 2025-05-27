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
            font-family: 'Courier New', monospace; /* Police adaptée aux imprimantes thermiques */
            margin: 0;
            padding: 0;
            width: 58mm;
            font-size: 14px;
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
            width: 56mm;
            margin: 0 auto;
            padding: 1mm;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 1mm;
        }
        
        .logo img {
            max-width: 20mm;
            height: auto;
        }
        
        /* En-tête avec hiérarchie typographique claire */
        .header {
            text-align: center;
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 2mm;
            border-bottom: 2px solid #000;
            padding-bottom: 1mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            margin: 2mm 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #f0f0f0;
            padding: 1mm;
            border: 1px solid #000;
        }
        
        /* Informations du reçu avec formatage amélioré */
        .receipt-info {
            font-size: 11px;
            margin-bottom: 1mm;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1mm;
        }
        
        .receipt-info .ref-number {
            font-size: 12px;
            font-weight: 900;
            text-align: center;
            background-color: #f8f8f8;
            padding: 0.5mm;
            border: 1px solid #000;
            margin-bottom: 1mm;
        }
        
        /* Informations étudiant avec mise en forme claire */
        .student-info {
            font-size: 14px;
            margin-bottom: 2mm;
            border-bottom: 1px solid #000;
            padding: 1mm;
            background-color: #fafafa;
        }
        
        .student-info p {
            margin: 1mm 0;
            font-weight: 900;
        }
        
        .student-info .student-name {
            font-size: 16px;
            font-weight: 900;
        }
        
        .student-info .student-class {
            font-size: 15px;
            font-weight: 900;
        }
        
        /* Badges de statut améliorés */
        .status-badge {
            display: inline-block;
            padding: 1mm 2mm;
            border-radius: 1mm;
            font-size: 11px;
            font-weight: 900;
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
            font-size: 13px;
            width: 100%;
            margin-bottom: 2mm;
            border: 1px solid #000;
            background-color: #fafafa;
        }
        
        .payment-history-title {
            text-align: center;
            font-weight: 900;
            margin-bottom: 1mm;
            font-size: 15px;
            background-color: #e9ecef;
            padding: 1mm;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }
        
        .payment-duration {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 1mm;
            padding: 0.5mm;
            background-color: #f8f9fa;
            border-bottom: 1px dotted #666;
        }
        
        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-history-table th,
        .payment-history-table td {
            border-bottom: 1px dotted #666;
            padding: 1mm 0.5mm;
            font-size: 12px;
            font-weight: bold;
        }
        
        .payment-history-table th {
            font-weight: 900;
            font-size: 13px;
            background-color: #e9ecef;
            text-transform: uppercase;
        }
        
        .payment-history-table td.text-right {
            text-align: right;
        }
        
        .payment-history-table .amount {
            font-weight: 900;
            font-size: 14px;
        }
        
        .payment-history-table .date {
            font-weight: 900;
            font-size: 13px;
        }
        
        /* Résumé de paiement avec mise en forme professionnelle */
        .payment-summary {
            margin: 1mm 0;
            border: 2px solid #000;
            border-radius: 1mm;
            background-color: #f8f9fa;
        }
        
        .payment-summary-title {
            text-align: center;
            font-weight: 900;
            background-color: #343a40;
            color: white;
            border-bottom: 1px solid #000;
            padding: 1mm;
            font-size: 13px;
            text-transform: uppercase;
        }
        
        .payment-summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-summary-table tr td {
            padding: 1mm;
            font-size: 13px;
            font-weight: bold;
        }
        
        .payment-summary-table tr:not(:last-child) td {
            border-bottom: 1px dotted #666;
        }
        
        .payment-summary-table .amount-label {
            font-weight: 900;
            font-size: 14px;
        }
        
        .payment-summary-table .amount-value {
            text-align: right;
            font-weight: 900;
            font-size: 16px;
        }
        
        .payment-summary-table .highlight-row td {
            background-color: #fff3cd;
            font-weight: 900;
            font-size: 18px;
            border: 1px solid #856404;
        }
        
        /* Pied de page avec informations critiques */
        .footer {
            text-align: center;
            font-size: 12px;
            margin: 1mm 0;
            font-weight: 900;
            padding: 1mm;
            border: 2px solid #000;
            border-radius: 1mm;
            background-color: #f8f9fa;
        }
        
        .footer .critical-amount {
            font-size: 20px;
            font-weight: 900;
            margin-top: 1mm;
            padding: 1mm;
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 0.5mm;
        }
        
        .footer .payment-date {
            font-size: 16px;
            font-weight: 900;
            margin-top: 1mm;
        }
        
        /* Signature et informations du caissier */
        .sign {
            text-align: center;
            font-size: 11px;
            margin-top: 1mm;
            padding-top: 1mm;
            border-top: 1px solid #666;
            font-weight: 900;
        }
        
        .cashier-info {
            font-size: 12px;
            font-weight: 900;
        }
        
        /* Ligne de découpe */
        .cut-line {
            border-top: 2px dashed #000;
            margin: 2mm 0;
            height: 1mm;
            position: relative;
        }
        
        .cut-line:after {
            content: '✂ DÉCOUPER ICI ✂';
            position: absolute;
            top: -2mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            background-color: white;
            padding: 0 1mm;
            font-weight: bold;
        }
        
        /* Date et heure avec formatage localisé */
        .date-time {
            font-size: 11px;
            text-align: center;
            margin-top: 1mm;
            font-weight: 900;
        }
        
        .current-datetime {
            font-size: 10px;
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
            font-weight: 900 !important;
        }
        
        .amount {
            font-weight: 900 !important;
            font-size: 16px !important;
        }
        
        .date {
            font-weight: bold !important;
            font-size: 14px !important;
        }
        
        /* Optimisations pour impression thermique et numérique */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .payment-summary, .footer {
                border: 1px solid #000 !important;
            }
            
            .status-badge {
                border: 1px solid #000 !important;
            }
            
            .payment-summary-table .highlight-row td {
                font-size: 20px !important;
            }
            
            .payment-summary-table tr td {
                font-size: 16px !important;
            }
            
            .footer .critical-amount {
                font-size: 22px !important;
            }
            
            .cashier-info {
                font-size: 16px !important;
            }
            
            .student-name {
                font-size: 18px !important;
            }
            
            .student-class {
                font-size: 17px !important;
            }
            
            .amount {
                font-size: 18px !important;
            }
            
            .date {
                font-size: 16px !important;
            }
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
    <div class="logo">
        <!-- Logo peut être ajouté ici -->
        <!-- <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"> -->
    </div>

    <div class="header">
        {{ strtoupper(Qs::getSetting('system_name')) }}
    </div>

    <div class="receipt-title">Reçu de Paiement</div>

    <div class="student-info">
        <p class="student-name"><strong>ÉLÈVE:</strong> {{ $sr->user->name }}</p>
        <p class="student-class"><strong>CLASSE:</strong> {{ $sr->my_class->name }} {{ $sr->section->name ? '('.$sr->section->name.')' : '' }}</p>
        <p><strong>DESCRIPTION:</strong> {{ $payment->title }}</p>
    </div>

    <!-- Historique des paiements avec dates formatées et triées chronologiquement -->
    @php
        use App\Helpers\DateHelper;
        
        // Trier les reçus par date de création (chronologique: du plus ancien au plus récent)
        $sortedReceipts = $receipts->sortBy('created_at');
        
        // Formatage des dates avec DateHelper
        $firstPaymentDate = $sortedReceipts->first() ?
            DateHelper::formatForReceipt($sortedReceipts->first()->created_at) : 'N/A';
        $lastPaymentDate = $sortedReceipts->last() ?
            DateHelper::formatForReceipt($sortedReceipts->last()->created_at) : 'N/A';
        
        // Période de paiement
        $paymentDuration = DateHelper::formatPeriod(
            $sortedReceipts->first()->created_at ?? null,
            $sortedReceipts->last()->created_at ?? null
        );
        
        // Calculer le montant total payé
        $totalPaid = $sortedReceipts->sum('amt_paid');
        
        // Date de la dernière transaction pour affichage précis
        $lastTransactionDate = $sortedReceipts->last() ?
            DateHelper::formatFrenchWithTime($sortedReceipts->last()->created_at) : 'N/A';
    @endphp

    @if($sortedReceipts->count() > 0)
    <div class="payment-history">
        <div class="payment-history-title">Historique</div>
        <table class="payment-history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Montant</th>
                    <th class="text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortedReceipts->take(3) as $r)
                    @if($r->amt_paid != 0)
                    <tr>
                        <td class="date">{{ DateHelper::formatForPaymentHistory($r->created_at) }}</td>
                        <td class="text-right amount">{{ DateHelper::formatAmount($r->amt_paid) }}</td>
                        <td class="text-right amount">{{ DateHelper::formatAmount($r->balance) }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Résumé financier simplifié -->
    <div class="payment-summary">
        <div class="payment-summary-title">Résumé</div>
        <table class="payment-summary-table">
            <tr>
                <td class="amount-label">Total:</td>
                <td class="amount-value amount">{{ DateHelper::formatAmount($pr->amount) }}</td>
            </tr>
            <tr class="highlight-row">
                <td class="amount-label">Reste:</td>
                <td class="amount-value amount">{{ DateHelper::formatAmount($pr->balance) }}</td>
            </tr>
        </table>
    </div>

    <!-- Statut et informations critiques -->
    <div class="footer">
        @if($pr->paid)
            <span class="status-badge status-normal">✓ ACQUITTÉ</span>
            <div class="payment-date">
                <strong>Payé le:</strong> {{ $lastTransactionDate }}
            </div>
        @else
            <span class="status-badge status-adra">⚠ EN COURS</span>
            <div class="critical-amount">
                <strong>Montant à payer:</strong><br>
                {{ DateHelper::formatAmount($pr->balance) }}
            </div>
        @endif
    </div>

    <!-- Informations simplifiées -->
    <div class="sign">
        <div class="cashier-info" style="font-size: 14px;">
            <strong>Caissier:</strong> {{ Auth::user()->name }}
        </div>
        <div style="margin-top: 1mm; font-size: 13px; text-align: center;">
            {{ DateHelper::now() }}
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
