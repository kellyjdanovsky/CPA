<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Décaissement</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 0;
            width: 58mm;
            font-size: 12px;
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
        
        .header {
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 1mm;
            border-bottom: 2px solid #000;
            padding-bottom: 1mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 14px;
            font-weight: 900;
            margin: 1mm 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #f0f0f0;
            padding: 1mm;
            border: 1px solid #000;
        }
        
        .decaissement-info {
            font-size: 11px;
            margin-bottom: 1mm;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1mm;
            background-color: #fafafa;
            padding: 1mm;
        }
        
        .decaissement-info p {
            margin: 0.5mm 0;
            font-weight: bold;
        }
        
        .amount {
            font-weight: 900 !important;
            font-size: 12px !important;
        }
        
        .date {
            font-weight: bold !important;
        }
        
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
        
        .sign {
            text-align: center;
            font-size: 11px;
            margin-top: 1mm;
            padding-top: 1mm;
            border-top: 1px solid #666;
            font-weight: 900;
        }
        
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
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: 900 !important;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
        
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

    <div class="receipt-title">Reçu de Décaissement</div>

    <div class="decaissement-info">
        <p><strong>Date:</strong> {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('DD/MM/YYYY à HH:mm') }}</p>
        <p><strong>Référence:</strong> {{ $reference ?? 'N/A' }}</p>
        <p><strong>Montant:</strong> <span class="amount">{{ number_format($montant ?? 0, 0, ',', ' ') }} Ar</span></p>
        <p><strong>Bénéficiaire:</strong> {{ $beneficiaire ?? 'N/A' }}</p>
        <p><strong>Motif:</strong> {{ $motif ?? 'N/A' }}</p>
    </div>

    <div class="footer">
        <div style="font-size: 14px; font-weight: 900;">
            Montant décaissé: <span class="amount">{{ number_format($montant ?? 0, 0, ',', ' ') }} Ar</span>
        </div>
    </div>

    <div class="sign">
        <div style="font-size: 12px; font-weight: 900;">
            <strong>Caissier:</strong> {{ Auth::user()->name }}
        </div>
        <div style="margin-top: 1mm; font-size: 10px;">
            <strong>Signature:</strong> _________________
        </div>
    </div>

    <div class="text-center" style="margin-top: 1mm; font-size: 11px; font-weight: bold;">
        Merci pour votre confiance!
    </div>

    <div class="cut-line"></div>
</div>

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
</script>
</body>
</html>