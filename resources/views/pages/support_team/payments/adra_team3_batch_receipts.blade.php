<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçus_Batch_{{ date('Y-m-d_H-i-s') }}</title>
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
            page-break-after: always;
        }

        .container:last-child {
            page-break-after: avoid;
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
            font-size: 10pt; /* Reduced slightly to fit long labels */
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
        
        .amount {
            font-weight: bold !important;
            font-size: 11pt !important;
        }

        /* Styles ADRA spécifiques */
        .adra-details {
            font-size: 8pt;
            margin-bottom: 1mm;
            border-bottom: 1px dashed #ccc;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            body {
                width: 58mm !important;
            }
            .container {
                width: 54mm !important;
            }
        }
    </style>
</head>
<body>
    @foreach($receiptsData as $receiptData)
        @php
            $student = $receiptData['student'];
            $payments = $receiptData['payments'];
            $totalAmount = $receiptData['totalAmount'];
            $amountToPay = $receiptData['amountToPay']; // This is the amount paid in this transaction
            $cashAmount = $receiptData['cashAmount']; // Remaining to be paid by others or total covered by others logic?
                                                    // In previous logic: cashAmount was "covered amount" or "remainder".
                                                    // Let's re-verify: usually 'amountToPay' is what the student pays.
            $status = $receiptData['status'];
            $referenceCode = $receiptData['referenceCode'];
            $isFullyPaid = $receiptData['isFullyPaid'] ?? false;
            
            // Calcul du montant en lettres
            $amountInWords = ucfirst(\App\Helpers\DateHelper::convertirMontantEnLettres($amountToPay));
        @endphp

        <div class="container">
            <div class="header">
                {{ strtoupper(Qs::getSetting('system_name')) }}
            </div>

            <div class="receipt-title">Reçu</div>

            <!-- Référence du reçu -->
            <div style="text-align: center; font-size: 8pt; margin-bottom: 1mm;">
                <strong>REF: {{ $referenceCode }}</strong> | {{ date('d/m/Y H:i') }}
            </div>

            <!-- Informations essentielles -->
            <div class="student-info">
                <p class="student-name">{{ strtoupper($student->user->name) }}</p>
                <p>{{ $student->my_class->name }} {{ $student->section->name ? '('.$student->section->name.')' : '' }}</p>
                
                <div style="border-top: 1px dotted #000; margin-top: 1mm; padding-top: 1mm;">
                {{-- Affichage de la liste propre des paiements --}}
                @foreach($payments as $payment)
                    <p style="font-size: 9pt; margin: 0.5mm 0;">
                        {{ strtoupper($payment->title) }}
                    </p>
                @endforeach
                </div>
            </div>

            <!-- Résumé lié au statut étudiant -->
            <div class="payment-summary">
                <table class="payment-summary-table">
                    {{-- 
                        Pour ADRA, on affiche uniquement la part étudiant (25%).
                    --}}
                    @php
                        $displayAmount = ($status == 'ADRA') ? $totalAmount * 0.25 : $amountToPay;
                        $displayWords = ucfirst(\App\Helpers\DateHelper::convertirMontantEnLettres($displayAmount));
                        
                        // Mode d'affichage simple demandé
                        $formattedMethod = strtoupper($status); 
                    @endphp

                    <tr class="highlight-row">
                        <td class="amount-label">Montant Payé:</td>
                        <td class="amount-value amount">{{ number_format($displayAmount, 0, ',', ' ') }} Ar</td>
                    </tr>
                    
                    {{-- Affichage du montant payé en lettres --}}
                    <tr>
                        <td colspan="2" style="font-size: 9pt; font-style: italic; text-align: center; border-top: 1px solid #000;">
                             La somme de : <strong>{{ $displayWords }}</strong>
                        </td>
                    </tr>
                    
                    @if($isFullyPaid)
                        <tr>
                            <td class="amount-label" style="color: #28a745;">Statut:</td>
                            <td class="amount-value" style="color: #28a745;">ACQUITTÉ</td>
                        </tr>
                    @else
                        {{-- Afficher le reste SEULEMENT si non acquitté --}}
                        <tr>
                            <td class="amount-label">Reste à payer:</td>
                            <td class="amount-value">{{ number_format($cashAmount, 0, ',', ' ') }} Ar</td>
                        </tr>
                    @endif
                </table>
            </div>
            
            <!-- Méthode de paiement -->
            <div style="text-align: center; font-size: 9pt; margin: 1mm 0;">
                <strong>Mode: {{ $formattedMethod }}</strong>
            </div>

            <!-- Informations simplifiées -->
            <div class="sign">
                <div class="cashier-info">
                    <strong>Caissier:</strong> {{ strtoupper(Auth::user()->name) }}
                </div>
                <div style="font-size: 8pt; margin-top: 1mm;">
                    MERCI POUR VOTRE PAIEMENT
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
