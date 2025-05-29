<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu_{{ $pr->ref_no.'_'.$sr->user->name }}</title>
    <style>
        @page {
            size: 58mm auto; /* Largeur standard pour imprimante thermique 58mm */
            margin: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 58mm; /* Largeur fixe pour imprimante thermique 58mm */
            font-size: 10pt; /* Taille de police réduite pour s'adapter à la largeur */
            font-weight: bold;
            line-height: 1.2;
        }
        
        .container {
            width: 54mm; /* Légèrement plus petit que la largeur du body pour les marges */
            margin: 0 auto;
            padding: 1mm;
        }
        
        .header {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2mm;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 2mm 0;
            text-transform: uppercase;
        }
        
        .info-section {
            margin-bottom: 2mm;
            font-size: 10pt;
            border: 1px solid #000;
            padding: 1mm;
            background-color: #f8f8f8;
        }
        
        .info-section p {
            margin: 1mm 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .student-name {
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            text-transform: uppercase;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2mm 0;
            border: 1px solid #000;
            background-color: #f8f8f8;
        }
        
        .payment-table th, 
        .payment-table td {
            padding: 1mm;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
        }
        
        .payment-table th {
            border-bottom: 1px solid #000;
            background-color: #f0f0f0;
        }
        
        /* Style pour le tableau historique */
        .payment-table.history th, 
        .payment-table.history td {
            padding: 1mm;
            font-size: 9pt;
            font-weight: bold;
        }
        
        /* Alignement des montants à droite */
        .payment-table td:nth-child(2),
        .payment-table td:nth-child(3) {
            text-align: right;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .total-section {
            margin: 2mm 0;
            border: 1px solid #000;
            padding: 2mm;
            background-color: #f8f8f8;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 1mm 0;
            font-size: 11pt;
            font-weight: bold;
            color: #000;
        }
        
        .total-row span:first-child {
            width: 60%;
        }
        
        .total-row span:last-child {
            width: 40%;
            text-align: right;
        }
        
        .highlight {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
        }
        
        .footer {
            margin-top: 2mm;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            border: 1px solid #000;
            padding: 1mm;
            background-color: #f8f8f8;
        }
        
        .cut-line {
            border-top: 1px dashed #000;
            margin: 3mm 0;
            position: relative;
        }
        
        .cut-line:after {
            content: '✂';
            position: absolute;
            top: -2mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10pt;
            font-weight: bold;
            background-color: white;
            padding: 0 1mm;
        }
        
        @media print {
            body {
                font-weight: bold;
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .container {
                width: 54mm !important;
            }
            
            .student-name, .highlight, .amount {
                font-weight: bold;
            }
            
            .total-section {
                border: 1px solid #000 !important;
            }
            
            .payment-table {
                border: 1px solid #000 !important;
            }
            
            .payment-table th {
                border-bottom: 1px solid #000 !important;
            }
            
            .footer {
                border: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        {{ strtoupper(Qs::getSetting('system_name')) }}
    </div>

    <div class="receipt-title">REÇU</div>

    @php
        use App\Helpers\DateHelper;
        
        // Trier les reçus par date de création
        $sortedReceipts = $receipts->sortBy('created_at');
        
        // Calculer le montant total payé
        $totalPaid = $sortedReceipts->sum('amt_paid');
        
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
    <div style="text-align: center; font-size: 9pt; margin-bottom: 1mm;">
        <strong>REF: {{ $receiptRef }}</strong> | {{ $currentDate }}
    </div>

    <!-- Informations essentielles -->
    <div class="info-section">
        <p class="student-name">{{ strtoupper($sr->user->name) }}</p>
        <p>{{ $sr->my_class->name }} {{ $sr->section->name ? '('.$sr->section->name.')' : '' }}</p>
        <p>{{ $payment->title }} - <strong>{{ number_format($payment->amount, 0, ',', ' ') }} Ar</strong></p>
    </div>

    <!-- Historique des paiements simplifié -->
    @if($sortedReceipts->count() > 0)
    <table class="payment-table history">
        <tr>
            <th>DATE</th>
            <th>PAYÉ</th>
            <th>RESTE</th>
        </tr>
        @foreach($sortedReceipts->take(2) as $receipt)
        <tr>
            <td>{{ DateHelper::formatFrenchShort($receipt->created_at) }}</td>
            <td>{{ number_format($receipt->amt_paid, 0, ',', ' ') }}</td>
            <td>{{ number_format($receipt->balance, 0, ',', ' ') }}</td>
        </tr>
        @endforeach
    </table>
    
    <!-- Méthode de paiement -->
    <div style="text-align: center; font-size: 10pt; margin: 1mm 0;">
        <strong>Mode: {{ $formattedMethod }}</strong>
    </div>
    @endif

    <div class="total-section">
        <div class="total-row highlight">
            <span>RESTE À PAYER:</span>
            <span class="amount">{{ number_format($pr->balance, 0, ',', ' ') }} Ar</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>Caissier:</strong> {{ Auth::user()->name }}</p>
        <p style="font-size: 8pt; margin-top: 1mm;">Merci pour votre paiement</p>
    </div>

    <div class="cut-line"></div>
</div>

<script>
    // Auto-impression
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
</script>
</body>
</html>