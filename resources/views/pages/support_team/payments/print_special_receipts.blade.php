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
            font-family: 'Courier New', monospace; /* Police adaptée aux imprimantes thermiques */
            margin: 0;
            padding: 0;
            font-size: 12px; /* Augmentation de la taille de police par défaut à 12px */
            font-weight: bold; /* Texte en gras par défaut */
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        .receipt-container {
            width: 56mm; /* Légèrement plus petit que la page pour éviter les débordements */
            margin: 0 auto;
            padding: 0.5mm; /* Réduction du padding */
            page-break-after: always;
        }
        .logo {
            text-align: center;
            margin-bottom: 1mm; /* Réduction de la marge */
        }
        .logo img {
            max-width: 25mm; /* Réduction de la taille max du logo */
            height: auto;
        }
        .header {
            text-align: center;
            font-size: 16px; /* Augmentation à 16px selon les instructions */
            font-weight: bold;
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px solid #000;
            padding-bottom: 0.5mm; /* Réduction du padding */
        }
        .receipt-title {
            text-align: center;
            font-size: 14px; /* Augmentation à 14px selon les instructions */
            font-weight: bold;
            margin: 1mm 0; /* Réduction des marges */
            text-transform: uppercase;
            letter-spacing: 0.5px; /* Espacement des lettres pour meilleure lisibilité */
        }
        .receipt-info {
            font-size: 12px; /* Augmentation à 12px selon les instructions */
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.5mm; /* Réduction du padding */
            font-weight: bold;
        }
        .student-info {
            font-size: 12px; /* Augmentation à 12px selon les instructions */
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.5mm; /* Réduction du padding */
            font-weight: bold;
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
            font-size: 12px; /* Augmentation à 12px selon les instructions */
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.5mm; /* Réduction du padding */
            font-weight: bold;
        }
        .payment-summary {
            margin-top: 1.5mm; /* Réduction de la marge */
            margin-bottom: 1.5mm; /* Réduction de la marge */
            border: 1px solid #000;
            border-radius: 0.5mm; /* Réduction du rayon */
        }
        .payment-summary-title {
            text-align: center;
            font-weight: bold;
            background-color: #f8f8f8;
            border-bottom: 1px solid #000;
            padding: 1mm; /* Augmentation du padding */
            font-size: 15px; /* Augmentation à 15px */
        }
        .payment-summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-summary-table tr td {
            padding: 1.5mm 2mm; /* Augmentation du padding */
            font-size: 14px; /* Augmentation à 14px selon les instructions */
            font-weight: bold;
        }
        .payment-summary-table tr:not(:last-child) td {
            border-bottom: 1px dotted #ccc;
        }
        .payment-summary-table .amount-label {
            font-weight: bold;
        }
        .payment-summary-table .amount-value {
            text-align: right;
            font-weight: bold;
        }
        .payment-summary-table .highlight-row td {
            background-color: #f8f8f8;
            font-weight: bold;
            font-size: 16px; /* Augmentation à 16px (ultra-visible) selon les instructions */
        }
        .footer {
            text-align: center;
            font-size: 13px; /* Augmentation à 13px selon les instructions */
            margin-top: 1.5mm; /* Réduction de la marge */
            font-weight: bold;
            padding: 1.5mm; /* Augmentation du padding */
            border: 1px solid #000; /* Bordure plus visible */
            border-radius: 0.5mm; /* Réduction du rayon */
            background-color: #f8f8f8;
        }
        .sign {
            text-align: center;
            font-size: 13px; /* Augmentation à 13px selon les instructions */
            margin-top: 1.5mm; /* Réduction de la marge */
            padding-top: 1mm; /* Réduction du padding */
            border-top: 1px dotted #ccc;
            font-weight: bold;
        }
        .cut-line {
            border-top: 1px dashed #000;
            margin-top: 3mm; /* Réduction de la marge */
            margin-bottom: 0;
            height: 0.5mm; /* Réduction de la hauteur */
            position: relative;
        }
        .cut-line:after {
            content: '✂';
            position: absolute;
            top: -2mm; /* Ajustement de la position */
            left: -1mm;
            font-size: 8px; /* Réduction de la taille de police */
        }
        .date-time {
            font-size: 13px; /* Augmentation à 13px selon les instructions */
            text-align: center;
            margin-top: 1mm; /* Réduction de la marge */
            font-weight: bold;
        }
        .bold {
            font-weight: bold;
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
            font-weight: bold;
            margin-top: 1mm; /* Réduction de la marge */
            text-align: center;
            padding: 1mm; /* Augmentation du padding */
            background-color: #f8f8f8;
            border: 1px solid #000; /* Bordure plus visible */
            border-radius: 0.5mm; /* Réduction du rayon */
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
        $paymentDate = $latestReceipt ? date('d/m/Y', strtotime($latestReceipt->created_at)) : date('d/m/Y');

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

        <div class="receipt-title">REÇU DE PAIEMENT</div>

        <div class="date-time">
            <span style="font-size: 13px; font-weight: bold;">Date: {{ date('d/m/Y H:i') }}</span>
        </div>

        <div class="receipt-info">
            <div class="bold">N° REÇU: {{ $pr->ref_no }}</div>
        </div>

        <div class="student-info">
            <div><span class="bold">NOM:</span> {{ $sr->user->name }}</div>
            <div><span class="bold">ADM_NO:</span> {{ $sr->adm_no }}</div>
            <div><span class="bold">CLASSE:</span> {{ $sr->my_class->name }}</div>
            <div><span class="bold">STATUT:</span> <span class="status-badge {{ $statusClass }}">{{ $status }}</span></div>
        </div>

        <div class="payment-info">
            <div><span class="bold">TITRE:</span> {{ $payment->title }}</div>
            @if($payment->description)
                <div><span class="bold">DESCRIPTION:</span> {{ $payment->description }}</div>
            @endif
        </div>

        <!-- Tableau de résumé des paiements -->
        <div class="payment-summary">
            <div class="payment-summary-title">DÉTAILS DU PAIEMENT</div>
            <table class="payment-summary-table">
                <tr>
                    <td class="amount-label">TOTAL</td>
                    <td class="amount-value">{{ $formattedTotalAmount }} Ar</td>
                </tr>
                <tr>
                    <td class="amount-label">FACTURÉ</td>
                    <td class="amount-value">{{ $formattedBilledAmount }} Ar</td>
                </tr>
                @if($status == 'ADRA' && $cashAmount > 0)
                <tr>
                    <td class="amount-label">CASH (25%)</td>
                    <td class="amount-value">{{ $formattedCashAmount }} Ar</td>
                </tr>
                @endif
                <tr>
                    <td class="amount-label">PAYÉ</td>
                    <td class="amount-value">{{ $formattedBilledAmount }} Ar</td>
                </tr>
                <tr class="highlight-row">
                    <td class="amount-label">DESCRIPTION</td>
                    <td class="amount-value">{{ $payment->description }}</td>
                </tr>
            </table>
        </div>

        @if($specialNote)
            <div class="special-note">
                {{ $specialNote }}
            </div>
        @endif

        <div class="footer">
            @php
                $paymentStatus = "ACQUITTÉ";
                $statusClass = "status-normal";

                // Pour les élèves ADRA, vérifier si le 25% a été payé
                if ($status == 'ADRA') {
                    $cashAmount = $payment->amount * 0.25;
                    $totalPaid = 0;
                    $cashPaid = 0;

                    // Calculer le montant total payé et le montant payé en cash
                    foreach ($receipts as $r) {
                        $totalPaid += $r->amt_paid;

                        // Compter uniquement les paiements non-ADRA comme cash
                        if ($r->payment_method != 'ADRA') {
                            $cashPaid += $r->amt_paid;
                        }
                    }

                    // Si le montant payé en cash est inférieur à 25%, alors le paiement est en cours
                    if ($cashPaid < $cashAmount) {
                        $paymentStatus = "EN COURS";
                        $statusClass = "status-adra";
                        $remainingCash = $cashAmount - $cashPaid;
                    }
                }
            @endphp

            <span class="status-badge {{ $statusClass }}">{{ $paymentStatus }}</span>
            <div style="margin-top: 1mm; font-size: 16px; font-weight: bold;">
                @if($paymentStatus == "ACQUITTÉ")
                    Payé le {{ date('d/m/y', strtotime($paymentDate)) }}
                @else
                    25% cash à payer
                    @if(isset($remainingCash))
                        <br>Reste: {{ number_format($remainingCash, 0, ',', ' ') }} Ar
                    @endif
                @endif
            </div>
        </div>

        <div class="sign">
            <span style="font-size: 13px; font-weight: bold;">Caissier: {{ Auth::user()->name }}</span>
        </div>

        <div class="date-time">
            <span style="font-size: 13px; font-weight: bold;">Mode: ADRA</span>
        </div>

        <div class="cut-line"></div>
    </div>
@endforeach

<script>
    window.print();
</script>
</body>
</html>
