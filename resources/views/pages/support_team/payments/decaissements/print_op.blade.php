<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ordre de Paiement {{ $decaissement->reference_op }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        .op-container {
            width: 100%;
            height: 48%; /* Slightly less than 50% to account for margins */
            border: 2px solid #1a365d;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 2%; /* Gap between copies */
            position: relative;
            background: #fff;
        }

        /* Table Layout for Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            margin-bottom: 15px;
        }
        
        .header-table td {
            vertical-align: middle;
        }

        .logo-img {
            width: 70px;
            height: auto;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
        }

        .school-address {
            font-size: 9pt;
            color: #4a5568;
        }

        /* Banner */
        .banner {
            background-color: #1a365d;
            color: white;
            text-align: center;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .banner h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        
        .banner p {
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 12pt;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
        }

        .label {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: bold;
            width: 25%;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .value {
            font-weight: normal;
        }
        
        .beneficiary {
            font-size: 11pt;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
        }

        /* Amount Box */
        .amount-box {
            background-color: #f0fff4;
            border: 2px solid #38a169;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .amount-row {
            width: 100%;
        }

        .amount-title {
            font-weight: bold;
            color: #276749;
            text-transform: uppercase;
            font-size: 10pt;
        }

        .amount-value {
            font-size: 18pt;
            font-weight: bold;
            color: #22543d;
            text-align: right;
        }

        .amount-words {
            border-top: 1px dashed #68d391;
            margin-top: 5px;
            padding-top: 5px;
            font-style: italic;
            font-size: 9pt;
            color: #2f855a;
        }

        /* Signatures */
        .signatures-table {
            width: 100%;
            margin-top: 30px;
        }

        .signatures-table td {
            width: 33%;
            vertical-align: top;
            padding: 0 10px;
        }

        .sig-box {
            border: 1px solid #e2e8f0;
            height: 80px;
            border-radius: 4px;
            position: relative;
        }

        .sig-header {
            background-color: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 4px;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            color: #4a5568;
            text-transform: uppercase;
        }

        .sig-name {
            text-align: center;
            margin-top: 40px;
            font-size: 8pt;
            color: #4a5568;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            font-size: 8pt;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            color: rgba(0,0,0,0.05);
            z-index: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .copy-label {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #edf2f7;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8pt;
            color: #4a5568;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

@for($i = 0; $i < 2; $i++)
    <div class="op-container">
        @if($decaissement->statut != 'EN_ATTENTE')
            <div class="watermark">{{ $decaissement->statut }}</div>
        @endif
        
        <div class="copy-label">
            {{ $i == 0 ? 'Original' : 'Copie - Archive' }}
        </div>

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 80px;">
                    <img src="{{ public_path(Qs::getSetting('logo')) }}" class="logo-img" alt="Logo">
                </td>
                <td style="text-align: center;">
                    <div class="school-name">{{ Qs::getSetting('system_name') }}</div>
                    <div class="school-address">{{ Qs::getSetting('address') }}</div>
                </td>
                <td style="width: 120px; text-align: right;">
                    <div style="font-size: 8pt; color: #718096;">
                        Date: {{ $decaissement->date_decaissement->format('d/m/Y') }}<br>
                        Time: {{ now()->format('H:i') }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Title -->
        <div class="banner">
            <h1>Ordre de Paiement</h1>
            <p>N° {{ $decaissement->reference_op }}</p>
        </div>

        <!-- Details -->
        <table class="info-table">
            <tr>
                <td class="label">Bénéficiaire</td>
                <td class="value beneficiary">{{ strtoupper($decaissement->beneficiaire) }}</td>
            </tr>
            <tr>
                <td class="label">Motif</td>
                <td class="value">{{ $decaissement->motif }}</td>
            </tr>
            <tr>
                <td class="label">Paiement</td>
                <td class="value">
                    {{ $decaissement->mode_paiement }}
                    @if($decaissement->projet_rubrique)
                        <span style="color: #718096">| {{ $decaissement->projet_rubrique }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Amount -->
        <div class="amount-box">
            <table class="amount-row">
                <tr>
                    <td class="amount-title">Montant Net à Payer</td>
                    <td class="amount-value">{{ number_format($decaissement->montant, 0, ',', ' ') }} Ar</td>
                </tr>
            </table>
            <div class="amount-words">
                Somme en lettres: <strong>{{ ucfirst($decaissement->montant_lettres) }}</strong>
            </div>
        </div>

        <!-- Signatures -->
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="sig-box">
                        <div class="sig-header">Établi par</div>
                        <div class="sig-name">{{ $decaissement->creator->name ?? '...' }}</div>
                    </div>
                </td>
                <td>
                    <div class="sig-box">
                        <div class="sig-header">Validé par</div>
                        <div class="sig-name">{{ $decaissement->approver->name ?? '...' }}</div>
                    </div>
                </td>
                <td>
                    <div class="sig-box">
                        <div class="sig-header">Acquit Bénéficiaire</div>
                        <div class="sig-name" style="margin-top: 50px; border-top: 1px dotted #ccc; width: 80%; margin-left: auto; margin-right: auto;"></div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <table style="width: 100%">
                <tr>
                    <td>PJ: {{ $decaissement->hasPieceJustificative() ? 'OUI' : 'NON' }}</td>
                    <td style="text-align: right">{{ \Illuminate\Support\Str::limit($decaissement->observations, 80) }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    @if($i == 0)
        <div style="border-bottom: 1px dashed #ccc; margin: 10px 0; text-align: center; color: #ccc;">✂ Découper ici</div>
    @endif
@endfor

</body>
</html>
