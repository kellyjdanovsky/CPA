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
            font-size: 12px; /* Augmentation de la taille de police par défaut à 12px */
            font-weight: 900; /* Texte en très gras pour meilleure lisibilité */
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body, .container, .header, .receipt-info, .student-info, .payment-info, .payment-table, .payment-table th, .payment-table td, .footer, .sign, .cut-line, strong, td, th, div, span {
            font-weight: bold !important;
        }
        .container {
            width: 56mm;
            margin: 0 auto;
            padding: 0.3mm;
            font-weight: 900;
        }
        .logo {
            text-align: center;
            margin-bottom: 0.5mm;
            font-weight: 900;
        }
        .logo img {
            max-width: 20mm;
            height: auto;
        }
        .header {
            text-align: center;
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 0.5mm;
            border-bottom: 1px solid #000;
            padding-bottom: 0.3mm;
        }
        .receipt-title {
            text-align: center;
            font-size: 12px;
            font-weight: 900;
            margin: 0.5mm 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .receipt-info {
            font-size: 11px;
            margin-bottom: 0.5mm;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.3mm;
            font-weight: 900;
        }
        .student-info {
            font-size: 11px;
            margin-bottom: 0.5mm;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.3mm;
            font-weight: 900;
        }
        .status-badge {
            display: inline-block;
            padding: 0.5mm 1mm;
            border-radius: 0.5mm;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            margin-top: 0.3mm;
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
            font-size: 11px;
            margin-bottom: 0.5mm;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 0.3mm;
            font-weight: 900;
        }
        .payment-history {
            font-size: 11px;
            width: 100%;
            margin-bottom: 0.5mm;
            font-weight: 900;
        }
        .payment-history-title {
            text-align: center;
            font-weight: 900;
            margin-bottom: 0.3mm;
            font-size: 12px;
        }
        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-history-table th,
        .payment-history-table td {
            border-bottom: 1px dotted #ccc;
            padding: 0.5mm;
            font-size: 11px;
            font-weight: 900;
        }
        .payment-history-table td.text-right {
            text-align: right;
        }
        .payment-history-table th {
            font-weight: 900;
            font-size: 12px;
        }
        .payment-summary {
            margin: 0.5mm;
            border: 1px solid #000;
            border-radius: 0.3mm;
            font-weight: 900;
        }
        .payment-summary-title {
            text-align: center;
            font-weight: 900;
            background-color: #f8f8f8;
            border-bottom: 1px solid #000;
            padding: 0.5mm;
            font-size: 12px;
        }
        .payment-summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-summary-table tr td {
            padding: 0.8mm 1mm;
            font-size: 11px;
            font-weight: 900;
        }
        .payment-summary-table tr:not(:last-child) td {
            border-bottom: 1px dotted #ccc;
        }
        .payment-summary-table .amount-label {
            font-weight: 900;
        }
        .payment-summary-table .amount-value {
            text-align: right;
            font-weight: 900;
        }
        .payment-summary-table .highlight-row td {
            background-color: #f8f8f8;
            font-weight: 900;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            margin: 0.5mm 0;
            font-weight: 900;
            padding: 0.5mm;
            border: 1px solid #000;
            border-radius: 0.3mm;
            background-color: #f8f8f8;
        }
        .sign {
            text-align: center;
            font-size: 11px;
            margin-top: 0.5mm;
            padding-top: 0.3mm;
            border-top: 1px dotted #ccc;
            font-weight: 900;
        }
        .cut-line {
            border-top: 1px dashed #000;
            margin: 1mm 0;
            height: 0.3mm;
            position: relative;
        }
        .cut-line:after {
            content: '✂';
            position: absolute;
            top: -1.5mm;
            left: -1mm;
            font-size: 6px;
        }
        .date-time {
            font-size: 11px;
            text-align: center;
            margin-top: 0.5mm;
            font-weight: 900;
        }
        .text-center {
            text-align: center;
            font-weight: 900;
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
<div class="container">
    <div class="logo">
        <!-- Vous pouvez ajouter un logo ici si nécessaire -->
        <!-- <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"> -->
    </div>

    <div class="header">
        {{ strtoupper(Qs::getSetting('system_name')) }}
    </div>

    <div class="receipt-title">REÇU DE PAIEMENT</div>



    <div class="student-info">
        <p>ELEVE: {{ $sr->user->name }}</p>
        <p>CLASSE: {{ $sr->my_class->name }} {{ $sr->section->name ? '('.$sr->section->name.')' : '' }}</p>
        <p>DESCRIPTION: {{ $pr->description }} - {{ $payment->title }}</p>
    </div>

    <!-- Historique des paiements -->
    @php
        // Trier les reçus par date de création (du plus ancien au plus récent)
        $sortedReceipts = $receipts->sortBy('created_at');
        $firstPaymentDate = $sortedReceipts->first() ? date('d/m/Y', strtotime($sortedReceipts->first()->created_at)) : 'N/A';
        $lastPaymentDate = $sortedReceipts->last() ? date('d/m/Y', strtotime($sortedReceipts->last()->created_at)) : 'N/A';
        $paymentDuration = $firstPaymentDate . ($firstPaymentDate != $lastPaymentDate ? ' au ' . $lastPaymentDate : '');
        // Calculer le montant total payé
        $totalPaid = $sortedReceipts->sum('amt_paid');
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
                    <th class="text-right">Reste</th>
                    <th>Méthode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $r)
                    @if($r->amt_paid != 0)
                    <tr>
                        <td>{{ date('d/m/y', strtotime($r->created_at)) }}</td>
                        <td class="text-right">{{ number_format($r->amt_paid, 0, ',', ' ') }} Ar</td>
                        <td class="text-right">{{ number_format($r->balance, 0, ',', ' ') }} Ar</td>
                        <td>{{ $r->methode }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        @if($pr->paid)
            <span class="status-badge status-normal">ACQUITTÉ</span>
            <div style="margin-top: 1mm; font-size: 13px; font-weight: bold;">
                Payé le {{ date('d/m/y', strtotime($sortedReceipts->last()->created_at)) }}
            </div>
        @else
            <span class="status-badge status-adra">EN COURS</span>
            <div style="margin-top: 1mm; font-size: 16px; font-weight: bold;">
                À payer: {{ number_format($pr->balance, 0, ',', ' ') }} Ar
            </div>
        @endif
    </div>

    <div class="sign">
        <span style="font-size: 13px; font-weight: bold;">Caissier: {{ Auth::user()->name }}</span>
    </div>

    <div class="date-time">
        <span style="font-size: 13px; font-weight: bold;">Merci pour votre paiement!</span>
    </div>

    <div class="cut-line"></div>
</div>
<script>
    window.print();
</script>
</body>
</html>
