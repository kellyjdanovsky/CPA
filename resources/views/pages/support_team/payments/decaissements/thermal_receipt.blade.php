<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket_Decaissement_{{ $decaissement->reference_op }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 58mm;
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.15;
            color: #000;
            background: #fff;
        }

        .container {
            width: 54mm;
            margin: 0 auto;
            padding: 2mm 1mm;
        }

        .school-header {
            text-align: center;
            font-size: 8pt;
            font-weight: 900;
            margin-bottom: 2mm;
            text-transform: uppercase;
            line-height: 1.1;
            border-bottom: 1px solid #000;
            padding-bottom: 1.5mm;
        }

        .receipt-title {
            text-align: center;
            font-size: 9pt;
            font-weight: 900;
            margin: 2mm 0 1mm 0;
            text-transform: uppercase;
            border: 1.5px solid #000;
            padding: 1mm;
            background-color: #f0f0f0;
        }

        .ref-box {
            text-align: center;
            font-size: 7.5pt;
            margin-bottom: 2mm;
            padding: 1mm 0;
            border-bottom: 1px dashed #000;
        }

        .info-group {
            margin-bottom: 2mm;
            font-size: 7.5pt;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            font-size: 7pt;
        }

        .info-value {
            font-weight: 900;
            text-align: right;
            max-width: 65%;
            word-wrap: break-word;
        }

        .box-motif {
            border: 1px solid #000;
            padding: 1.5mm;
            margin: 2mm 0;
            font-size: 7.5pt;
            background: #fdfdfd;
        }

        .amount-box {
            border: 2px solid #000;
            text-align: center;
            padding: 2mm 1mm;
            margin: 2.5mm 0;
            background-color: #f5f5f5;
        }

        .amount-title {
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .amount-number {
            font-size: 12pt;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .amount-words {
            font-size: 6.5pt;
            font-style: italic;
            margin-top: 1mm;
            text-transform: uppercase;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 3mm;
            margin-bottom: 3mm;
            font-size: 6.5pt;
            text-align: center;
        }

        .sig-col {
            width: 48%;
            border-top: 1px dotted #000;
            padding-top: 1.5mm;
        }

        .footer {
            text-align: center;
            font-size: 6.5pt;
            margin-top: 2mm;
            border-top: 1px solid #000;
            padding-top: 1.5mm;
        }

        .cut-line {
            text-align: center;
            margin-top: 2mm;
            font-size: 6pt;
            border-top: 1px dashed #000;
            padding-top: 1mm;
        }

        @media print {
            body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .container {
                width: 54mm !important;
                margin: 0 auto !important;
                padding: 1mm !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête école -->
        <div class="school-header">
            {{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}<br>
            <span style="font-size: 6.5pt; font-weight: normal;">AVARATETEZANA AMPITATAFIKA</span>
        </div>

        <!-- Titre du ticket -->
        <div class="receipt-title">
            BON DE DÉCAISSEMENT
        </div>

        <!-- Référence et date -->
        <div class="ref-box">
            <strong>RÉF OP : {{ $decaissement->reference_op }}</strong><br>
            Date : {{ date('d/m/Y H:i', strtotime($decaissement->created_at ?? $decaissement->date_decaissement)) }}
        </div>

        <!-- Informations du décaissement -->
        <div class="info-group">
            <div class="info-row">
                <span class="info-label">BÉNÉFICIAIRE :</span>
                <span class="info-value">{{ strtoupper($decaissement->beneficiaire) }}</span>
            </div>
            @if($decaissement->projet_rubrique)
            <div class="info-row">
                <span class="info-label">RUBRIQUE :</span>
                <span class="info-value">{{ $decaissement->projet_rubrique }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">MODE :</span>
                <span class="info-value">{{ strtoupper($decaissement->mode_paiement) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">STATUT :</span>
                <span class="info-value">{{ strtoupper($decaissement->statut) }}</span>
            </div>
        </div>

        <!-- Motif de la dépense -->
        <div class="box-motif">
            <div class="info-label" style="margin-bottom: 0.5mm;">MOTIF DE LA SORTIE :</div>
            <div>{{ $decaissement->motif }}</div>
        </div>

        <!-- Encadré Montant -->
        <div class="amount-box">
            <div class="amount-title">MONTANT DÉCAISSÉ</div>
            <div class="amount-number">{{ number_format($decaissement->montant, 0, ',', ' ') }} Ar</div>
            @if($decaissement->montant_lettres)
                <div class="amount-words">({{ $decaissement->montant_lettres }})</div>
            @endif
        </div>

        <!-- Signatures / Décharge -->
        <div class="signatures">
            <div class="sig-col">
                LE CAISSIER<br>
                <small style="font-weight: normal;">{{ $decaissement->creator->name ?? Auth::user()->name }}</small>
            </div>
            <div class="sig-col">
                LE BÉNÉFICIAIRE<br>
                <small style="font-weight: normal;">(Signature / Décharge)</small>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            Pièce de caisse officielle - Enregistré le {{ date('d/m/Y', strtotime($decaissement->date_decaissement)) }}
        </div>

        <!-- Ligne de découpe -->
        <div class="cut-line">
            ✂ - - - - - - - - - - - - - - - - - - - - - - ✂
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
