<!DOCTYPE html>
<html>
<head>
    <title>Reçus ADRA/TEAM3</title>
    <style>
        @page {
            size: 58mm auto; /* Largeur de 58mm pour l'imprimante thermique */
            margin: 0;
        }
        body {
            font-family: 'Arial', sans-serif; /* Police plus lisible */
            margin: 0;
            padding: 0;
            font-size: 16px; /* Augmentation de la taille de police */
            font-weight: 900; /* Texte en gras renforcé */
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            line-height: 1.3;
            line-height: 1.3;
        }
        .receipt-container {
            width: 56mm; /* Légèrement plus petit que la page pour éviter les débordements */
            margin: 0 auto;
            padding: 1mm; /Légère augmentagère augmentation du padding */
            page-break-after: always;
        }
        .logo {
            text-align: center;
            margin-bottom: 2mm;
        }
        .logo img {
            max-width: 25mm;
            height: auto;
        }
        .header {
            text-align: center;
            font-size: 18px;le */
            font-weight: 900; /* Bordure plus épaisse */
            margin-bottom: 2mm;
            border-bottom: 2px solid #000; /* Bordure plus épaisse */
            padding-bottom: 1mm;
            text-transform: uppercase;
        }
        .receipt-title {
            text-align: center;
            font-size: 14px;la taille */
            font-weight: 900;
            margin: 2mm 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #f0f0f0;
            padding: 1.5mm;
            border: 1px solid #000;
            border-radius: 2mm;
        }
        .receipt-info {
            font-size: 14px;
            margin-bottom: 2mm;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
            font-weight: 900;
        }
        .student-info {
            font-size: 16px;
            margin-bottom: 3mm;
            border: 2px solid #000;
            padding: 2mm;
            background-color: #f8f8f8;
            border-radius: 2mm;
            border: 2px solid #000;
            padding: 2mm;
            background-color: #f8f8f8;
            border-radius: 2mm;
            font-weight: 900;
        }
        .status-badge {
            display: inline-block;
            padding: 1mm 2mm; /* Augmentation du padding */
            border-radius: 1mm; /* Réduction du rayon */
            font-size: 12px; /* Augmentation à 12px selon les instructions */
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 0.5mm; /* Réduction de la marge */
        }
        .status-normal {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .status-adra {
            background-color: #d9edf7;
            color: #31708f;
        }
        .status-team3 {
            background-color: #fcf8e3;
            color: #8a6d3b;
        }
        .payment-info {
            font-size: 12px; /* Augmentation à 12px selon les instru2mm;
            background-color: #f8f9fa;
            overflow: hidden;
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: adding */
            font-weight: 900;
        }
        .payment-summary {
            margin:            border: 3px solid #000;
            border-radius: 2mm;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        .payment-summary-title {
            text-align: center;
            font-weight: 900;
            background-color: #f8f9fa;
            border-bottom: 2px solid #000;
            padding: 2mm;
        }
        .payment-summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-summary-table tr td {
            padding: 2mm 1mm 1mm;
            font-size: 16px;
            font-weight: 900;
            white-space: nowrap;
        }
        .payment-summary-table tr:not(:last-child) td {
            border-bottom: 1px solid #000;
        }
        .payment-summary-table .amount-label {
            font-weight: 900;
                 width: 60%;
        }
        .payment-summary-table .amount-value {
            text-align: right;
            font-weight: 900;
       white-space: nowrap;
            width: 40%;
        }
        .payment-summary-table .highlight-row td {
            background-color: #fff3cd;
            font-weight: 900;
            font-size: 16px;
            width: 40%;
            white-space: nowrap;
        }
        .ccashier-info {
            text-align: center;
            font-size: 18px;
            margin: 4mm 00;
            font-weight: ;
        }
        .sign {
            text-align: center;
            font-size: 16px;
            margin-top: 3mm;
            padding: 2mm;
            border: 2px solid #000;
            border-radius: 2mm;
            background-color: #f8f8f8;
            font-weight: 900;
        }
        .cashier-info {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .cut-line {
            border-top: 3px dashed #000;
            margin: 4mm 0;
            height: 2mm;
            position: relative;
        }
        .cut-line:after {
            content: '✂';
            position: absolute;
            top: -3mm;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            background-color: white;
            padding: 0;
            font-weight: 900;
            overflow: hidden;
        }
        .date-time {
            font-size: 13px; /* Augmentation à 13px selon les instructions */
            text-align: center;
            margin-top: 1mm; /* Réduction de la marge */
            font-weight: bold;
        }
        .bold {
            font-weight: 7bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .special-note {
            font-size: 13px; /* Augmentation à 13px selon les instructions */
            font-style: italic;
            font-weight: 343a40;
            margin-top: 1mm; /* Réduction de la marge 0*/
            text-align: center;
            padding: 1mm; /* Augmentation du padding */
            background-color: #f8f8f8;
            border: 1px solid #000; /* Bordure plus visible */
            border-bottom: 2px solid #000;
            font-weight: 900;
            text-transform: uppercase; /* Réduction du rayon */
        }

        /* Historique des paiements */
        .payment-history {
            margin: 3mm 0;
            border: 2px solid #000;
            border-radius: 2mm;
            padding: 0;
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .payment-history-title {
            font-size: 15px;
            text-transform: uppercase;
            font-weight: 900;
            text-align: center;
            margin-bottom: 0;
            padding: 1.5mm 1mm;
            background-color: #343a40;
            color: white;
            border-bottom: 2px solid #000;
            text-transform: uppercase;
        }

        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .payment-history-table th,
        .payment-history-table td {
            padding: 1.5mm 0.5mm;
            border-bottom: 1px solid #000;
            text-align: left;
            font-weight: 900;
            white-space: nowrap;
            white-space: nowrap;
        }

        .payment-history-table th {
            font-weight: 900;
            background-color: #e9ecef;
            font-size: 13px;
            text-transform: uppercase;
        }
        
        /* Alignement des montants à droite */
        .payment-history-table td:nth-child(2),
        .payment-history-table td:nth-child(3) {
            text-align: right;
        }

        /* Classes utilitaires supplémentaires */
        .amount {
            font-weight: 900 !important;
            font-size: 16px !important;
        }

        .date {
            font-weight: bold !important;
            font-size: 14px !important;
        }
        
        /* Alignement des montants à droite */
        .payment-history-table td:nth-child(2),
        .payment-history-table td:nth-child(3) {
            text-align: right;
        }

        .student-name {
            font-size: 13px;
            font-weight: bold;
        }

        .student-class {
            font-size: 15px;
            font-weight: bold;
        }

        .cashier-info {
            font-size: 12px;
            font-weight: 900;
        }

        .critical-amount {
            font-size: 20px;
            font-weight: 900;
            margin-top: 1mm;
            padding: 1mm;
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 0.5mm;
        }

        .payment-date {
            font-size: 16px;
            font-weight: 900;
            margin-top: 1mm;
        }

        /* Optimisations pour impression thermique */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .payment-summary, .footer, .special-note {
                border: 0.5px solid #000 !important; /* Bordure continue pour éviter les problèmes d'impression */
            }
            .status-badge {
                border: 0.5px solid #000 !important;
            }
            /* Forçage de la taille en impression */
            .payment-summary-table .highlight-row td {
                font-size: 16px !important; /* Maintien de la taille même en impression */
            }
            .payment-summary-table tr td {
                font-size: 14px !important; /* Maintien de la taille même en impression */
            }
            /* Forçage du montant restant à payer */
            .footer div {
                font-size: 16px !important; /* Maintien de la taille même en impression */
            }

            /* Optimisations pour les nouvelles classes */
            .payment-history, .footer {
                border: 1px solid #000 !important;
            }

            .critical-amount {
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
    </style>
</head>
<body>
@foreach($payment_records as $pr)
    @php
        $sr = $pr->student;
        $payment = $pr->payment;
        $receipts = $pr->receipt;

        // Trier les reçus par date de création (du plus ancien au plus récent)
        $sortedReceipts = $receipts->sortBy('created_at');
        $latestReceipt = $sortedReceipts->last();
        $paymentDate = $latestReceipt ? \App\Helpers\DateHelper::formatForReceipt($latestReceipt->created_at) : \App\Helpers\DateHelper::formatForReceipt(now());

        // Déterminer le statut de l'élève
        $status = $sr->user->status ?? 'Normal';
        $statusClass = 'status-normal';

        if ($status == 'ADRA') {
            $statusClass = 'status-adra';
        } elseif ($status == 'TEAM3') {
            $statusClass = 'status-team3';
        }

        // Calculer le montant facturé en fonction du statut
        $billedAmount = $payment->amount;
        $cashAmount = 0;
        $specialNote = '';

        if ($status == 'ADRA') {
            $billedAmount = $payment->amount * 0.75;
            $cashAmount = $payment->amount * 0.25;
            $specialNote = 'Facturation à 75% du montant total (statut ADRA) + 25% payé en cash';
        } elseif ($status == 'TEAM3') {
            $billedAmount = $payment->amount;
            $specialNote = 'Facturation à 100% du montant total (statut TEAM3)';
        }

        // Formater les montants
        $formattedTotalAmount = number_format($payment->amount, 0, ',', ' ');
        $formattedBilledAmount = number_format($billedAmount, 0, ',', ' ');
        $formattedCashAmount = number_format($cashAmount, 0, ',', ' ');
    @endphp

    <div class="receipt-container">
        <div class="logo">
            <!-- Vous pouvez ajouter un logo ici si nécessaire -->
            <!-- <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"> -->
        </div>

        <div class="header">
            {{ strtoupper($settings['system_name'] ?? 'ÉCOLE') }}
        </div>

        <div class="receipt-title">REÇU</div>

        <div class="student-info">
            <p class="student-name">{{ $sr->user->name }}</p>
            <p>{{ $sr->my_class->name }} {{ $sr->section->name ? '('.$sr->section->name.')' : '' }}</p>
            <p>{{ $payment->title }} - <strong>{{ \App\Helpers\DateHelper::formatAmount($payment->amount) }}</strong></p>
            <p><span class="status-badge {{ $statusClass }}">{{ $status }}</span></p>
        </div>

        <!-- Historique des paiements simplifié -->
        @if($sortedReceipts->count() > 0)
        <div class="payment-history">
            <div class="payment-history-title">Historique</div>
            <table class="payment-history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payé</th>
                        <th>Reste</th>
                        <th>Mode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sortedReceipts->take(2) as $r)
                        @if($r->amt_paid != 0)
                        @php
                            $method = strtoupper($r->payment_method ?? $r->methode ?? 'CASH');
                            if ($method == 'CASH' || $method == 'ESPÈCES' || $method == 'ESPECES') {
                                $method = 'CASH';
                            }
                        @endphp
                        <tr>
                                <td>{{ \App\Helpers\DateHelper::formatForPaymentHistory($r->created_at) }}</td>
                            <td>{{ \App\Helpers\DateHelper::formatAmount($r->amt_paid) }}</td>
                            <td>{{ \App\Helpers\DateHelper::formatAmount($r->balance) }}</td>
                            <td>{{ $method }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @php
            // Pour les élèves ADRA, vérifier si le 25% a été payé
            $remainingCash = null;
            if ($status == 'ADRA') {
                $cashAmount = $payment->amount * 0.25;
                $cashPaid = 0;
                
                // Calculer le montant payé en cash
                foreach ($receipts as $r) {
                    if ($r->payment_method != 'ADRA') {
                        $cashPaid += $r->amt_paid;
                    }
                }

                // Si le montant payé en cash est inférieur à 25%
                if ($cashPaid < $cashAmount) {
                    $remainingCash = $cashAmount - $cashPaid;
              $remainingCash = $cashAmount - $cashPaid;
                }
            }
        @endphp

        <!-- Résumé simplifié -->
        <div class="payment-summary">
            <table class="payment-summary-table">
                <tr class="highlight-row">
                    <td class="amount-label">Reste à payer:</td>
                    <td class="amount-value amount">{{ \App\Helpers\DateHelper::formatAmount($pr->balance ?? 0) }}</td>
                </tr>
                @if(isset($remainingCash) && $remainingCash > 0)
                <tr>
                    <td class="amount-label">Cash 25%:</td>
                    <td class="amount-value amount">{{ \App\Helpers\DateHelper::formatAmount($remainingCash) }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Informations simplifiées -->
        <div class="sign">
            <div class="cashier-info">
                <strong>Caissier:</strong> {{ Auth::user()->name  }
            }
        @endphp

        <!-- Résumé simplifié -->
        <div class="payment-summary">
            <table class="payment-summary-table">
                <tr class="highlight-row">
                    <td class="amount-label">Reste à payer:</td>
                    <td class="amount-value amount">{{ \App\Helpers\DateHelper::formatAmount($pr->balance ?? 0) }}</td>
                </tr>
                @if(isset($remainingCash) && $remainingCash > 0)
                <tr>
                    <td class="amount-label">Cash 25%:</td>
                    <td class="amount-value amount">{{ \App\Helpers\DateHelper::formatAmount($remainingCash) }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Informations simplifiées -->
        <div class="sign">
            <div class="cashier-info">
                <strong>Caissier:</strong> {{ Auth::user()->name }}
            </div>
        </div>

        <div class="cut-line"></div>
    </div>
@endforeach

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
