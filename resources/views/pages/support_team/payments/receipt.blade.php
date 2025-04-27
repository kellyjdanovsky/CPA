<!DOCTYPE html>
<html>
<head>
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
            font-size: 7px; /* Réduction de la taille de police par défaut */
        }
        .container {
            width: 56mm; /* Légèrement plus petit que la page pour éviter les débordements */
            margin: 0 auto;
            padding: 0.5mm; /* Réduction du padding */
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
            font-size: 9px; /* Réduction de la taille de police */
            font-weight: bold;
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px solid #000;
            padding-bottom: 0.5mm; /* Réduction du padding */
        }
        .receipt-title {
            text-align: center;
            font-size: 8px; /* Réduction de la taille de police */
            font-weight: bold;
            margin: 1mm 0; /* Réduction des marges */
            text-transform: uppercase;
        }
        .receipt-info {
            font-size: 7px; /* Réduction de la taille de police */
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.5mm; /* Réduction du padding */
        }
        .student-info {
            font-size: 7px; /* Réduction de la taille de police */
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.5mm; /* Réduction du padding */
        }
        .status-badge {
            display: inline-block;
            padding: 0.5mm 1mm; /* Réduction du padding */
            border-radius: 1mm; /* Réduction du rayon */
            font-size: 6px; /* Réduction de la taille de police */
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
            font-size: 7px; /* Réduction de la taille de police */
            margin-bottom: 1mm; /* Réduction de la marge */
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.5mm; /* Réduction du padding */
        }
        .payment-history {
            font-size: 6px; /* Réduction de la taille de police */
            width: 100%;
            margin-bottom: 1mm; /* Réduction de la marge */
        }
        .payment-history-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 0.5mm; /* Réduction de la marge */
            font-size: 7px; /* Réduction de la taille de police */
        }
        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-history-table th,
        .payment-history-table td {
            border-bottom: 1px dotted #ccc;
            padding: 0.5mm; /* Réduction du padding */
            text-align: left;
            font-size: 6px; /* Réduction de la taille de police */
        }
        .payment-history-table th {
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
            padding: 0.5mm; /* Réduction du padding */
            font-size: 7px; /* Réduction de la taille de police */
        }
        .payment-summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-summary-table tr td {
            padding: 1mm 1.5mm; /* Réduction du padding */
            font-size: 7px; /* Réduction de la taille de police */
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
            font-size: 8px; /* Réduction de la taille de police */
        }
        .footer {
            text-align: center;
            font-size: 7px; /* Réduction de la taille de police */
            margin-top: 1.5mm; /* Réduction de la marge */
            font-weight: bold;
            padding: 1mm; /* Réduction du padding */
            border: 1px solid #ccc;
            border-radius: 0.5mm; /* Réduction du rayon */
            background-color: #f8f8f8;
        }
        .sign {
            text-align: center;
            font-size: 7px; /* Réduction de la taille de police */
            margin-top: 1.5mm; /* Réduction de la marge */
            padding-top: 1mm; /* Réduction du padding */
            border-top: 1px dotted #ccc;
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
            font-size: 6px; /* Réduction de la taille de police */
            text-align: center;
            margin-top: 1mm; /* Réduction de la marge */
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
        /* Optimisations pour impression thermique */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .payment-summary, .footer {
                border: 1px solid #000 !important;
            }
            .status-badge {
                border: 0.5px solid #000 !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <!-- Vous pouvez ajouter un logo ici si nécessaire -->
        <!-- <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"> -->
    </div>

    <div class="header">
        {{ strtoupper(Qs::getSetting('system_name')) }}
    </div>

    <div class="receipt-title">REÇU DE PAIEMENT</div>

    <div class="date-time">
        Date: {{ date('d/m/Y H:i', strtotime($pr->created_at)) }}
    </div>

    <div class="receipt-info">
        <div class="bold">N° REÇU: {{ $pr->ref_no }}</div>
    </div>

    <div class="student-info">
        <div><span class="bold">NOM:</span> {{ $sr->user->name }}</div>
        <div><span class="bold">ADM_NO:</span> {{ $sr->adm_no }}</div>
        <div><span class="bold">CLASSE:</span> {{ $sr->my_class->name }}</div>
        @php
            $status = $sr->user->status ?? 'Normal';
            $statusClass = 'status-normal';

            if ($status == 'ADRA') {
                $statusClass = 'status-adra';
            } elseif ($status == 'Team3') {
                $statusClass = 'status-team3';
            }
        @endphp
        <div><span class="bold">STATUT:</span> <span class="status-badge {{ $statusClass }}">{{ $status }}</span></div>
    </div>

    <div class="payment-info">
        <div><span class="bold">TITRE:</span> {{ $payment->title }}</div>
        <div><span class="bold">DESCRIPTION:</span> {{ $payment->description }}</div>
    </div>

    <!-- Tableau de résumé des paiements -->
    <div class="payment-summary">
        <div class="payment-summary-title">RÉSUMÉ DU PAIEMENT</div>
        <table class="payment-summary-table">
            <tr>
                <td class="amount-label">TOTAL</td>
                <td class="amount-value">{{ number_format($payment->amount, 0, ',', ' ') }} Ar</td>
            </tr>
            <tr>
                <td class="amount-label">PAYÉ</td>
                <td class="amount-value">{{ number_format($pr->amt_paid, 0, ',', ' ') }} Ar</td>
            </tr>
            <tr class="highlight-row">
                <td class="amount-label">RESTE</td>
                <td class="amount-value">{{ number_format($pr->balance, 0, ',', ' ') }} Ar</td>
            </tr>
        </table>
    </div>

    <!-- Historique des paiements -->
    @php
        // Trier les reçus par date de création (du plus ancien au plus récent)
        $sortedReceipts = $receipts->sortBy('created_at');
        $firstPaymentDate = $sortedReceipts->first() ? date('d/m/Y', strtotime($sortedReceipts->first()->created_at)) : 'N/A';
        $lastPaymentDate = $sortedReceipts->last() ? date('d/m/Y', strtotime($sortedReceipts->last()->created_at)) : 'N/A';
        $paymentDuration = $firstPaymentDate . ($firstPaymentDate != $lastPaymentDate ? ' au ' . $lastPaymentDate : '');
    @endphp

    @if($sortedReceipts->count() > 0)
    <div class="payment-history">
        <div class="payment-history-title">HISTORIQUE</div>
        <div class="text-center" style="font-size: 6px; margin-bottom: 0.5mm;">
            {{ $paymentDuration }}
        </div>
        <table class="payment-history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Payé</th>
                    <th class="text-right">Solde</th>
                    <th>Mode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortedReceipts as $r)
                <tr>
                    <td>{{ date('d/m/y', strtotime($r->created_at)) }}</td>
                    <td class="text-right">{{ number_format($r->amt_paid, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($r->balance, 0, ',', ' ') }}</td>
                    <td>{{ $r->methode }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        @if($pr->paid)
            <span class="status-badge status-normal">ACQUITTÉ</span>
            <div style="margin-top: 1mm; font-size: 6px;">
                Payé le {{ date('d/m/y', strtotime($sortedReceipts->last()->created_at)) }}
            </div>
        @else
            <span class="status-badge status-adra">EN COURS</span>
            <div style="margin-top: 1mm; font-size: 6px;">
                À payer: {{ number_format($pr->balance, 0, ',', ' ') }} Ar
            </div>
        @endif
    </div>

    <div class="sign">
        Caissier: {{ Auth::user()->name }}
    </div>

    <div class="date-time">
        Merci pour votre paiement!
    </div>

    <div class="cut-line"></div>
</div>
<script>
    window.print();
</script>
</body>
</html>
