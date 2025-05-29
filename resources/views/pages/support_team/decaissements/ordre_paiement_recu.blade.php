<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordre de Paiement - {{ $decaissement->id }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            font-family: 'Arial', 'Calibri', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        .page {
            width: 100%;
            height: 50vh;
            position: relative;
            page-break-inside: avoid;
        }

        .page:first-child {
            margin-bottom: 2cm;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 1cm;
        }

        /* En-tête officiel */
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }

        .logo {
            width: 90px;
            height: 90px;
            margin-right: 25px;
            border: 2px solid #007bff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #007bff;
            font-weight: bold;
            background: #fff;
        }

        .school-info {
            flex: 1;
            text-align: center;
        }

        .school-name {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #007bff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .school-details {
            font-size: 11pt;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .document-title {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            color: #e74c3c;
            background: #fff;
            padding: 8px 15px;
            border-radius: 25px;
            display: inline-block;
            border: 2px solid #e74c3c;
        }

        /* Informations de l'ordre */
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            align-items: center;
        }

        .order-number {
            font-size: 16pt;
            font-weight: bold;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .order-date {
            font-size: 14pt;
            font-weight: bold;
            color: #495057;
            background: #fff;
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }

        /* Contenu principal */
        .content {
            margin-bottom: 30px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .content-table td {
            padding: 8px 12px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .content-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }

        .content-table .value {
            width: 70%;
        }

        .amount-section {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 3px solid #856404;
            padding: 20px;
            margin: 25px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .amount-section::before {
            content: '💰';
            position: absolute;
            top: -15px;
            left: 20px;
            background: #fff;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 20pt;
        }

        .amount-numbers {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            color: #856404;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .amount-words {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            font-style: italic;
            color: #6c5ce7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Section des pièces justificatives */
        .justificatives {
            margin: 20px 0;
        }

        .justificatives-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .justificatives-list {
            display: flex;
            gap: 20px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .checkbox {
            width: 15px;
            height: 15px;
            border: 2px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 11px;
            font-weight: bold;
        }

        .checkbox.checked {
            background-color: #000;
            color: white;
        }

        /* Section des signatures */
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .signature-box {
            width: 30%;
            text-align: center;
            border: 2px solid #000;
            padding: 20px;
            min-height: 100px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
        }

        .signature-box::before {
            content: '✍️';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 5px;
            border-radius: 50%;
            font-size: 16pt;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
            color: #007bff;
            font-size: 11pt;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 2px dotted #000;
            padding-top: 8px;
            font-size: 10pt;
            color: #666;
        }

        /* QR Code (optionnel) */
        .qr-section {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #666;
        }

        /* Section de confirmation de réception */
        .receipt-confirmation {
            margin-top: 30px;
            border: 2px solid #28a745;
            padding: 15px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }

        .receipt-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 15px;
            color: #28a745;
        }

        .receipt-signature {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .receipt-signature-box {
            width: 45%;
        }

        .receipt-signature-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-signature-line {
            height: 60px;
            border-bottom: 1px solid #000;
        }

        .receipt-stamp {
            height: 60px;
            border: 1px dashed #000;
            border-radius: 5px;
        }

        /* Styles pour l'impression */
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                font-size: 11pt;
            }

            .page {
                height: auto;
                min-height: 45vh;
            }

            .page-break {
                page-break-before: always;
            }

            .no-print {
                display: none !important;
            }

            .header {
                background: #f8f9fa !important;
                border-bottom: 2px solid #000 !important;
            }

            .order-number {
                background: #28a745 !important;
                color: white !important;
            }

            .amount-section {
                background: #fff3cd !important;
                border: 2px solid #856404 !important;
            }

            .signature-box {
                background: #f8f9fa !important;
                border: 1px solid #000 !important;
            }
        }

        /* Responsive pour l'affichage écran */
        @media screen and (max-width: 768px) {
            .order-info {
                flex-direction: column;
                gap: 10px;
            }

            .signatures {
                flex-direction: column;
                gap: 15px;
            }

            .signature-box {
                width: 100%;
            }

            .receipt-signature {
                flex-direction: column;
                gap: 15px;
            }

            .receipt-signature-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @php
        // Générer le numéro d'ordre automatique
        $orderNumber = 'OP-' . date('Y') . '-' . str_pad($decaissement->id, 4, '0', STR_PAD_LEFT);
        $montantEnLettres = DateHelper::convertirMontantEnLettres($decaissement->montant);
    @endphp

    <!-- Premier ordre de paiement -->
    <div class="page">
        <!-- QR Code optionnel -->
        <div class="qr-section">
            QR CODE<br>
            <small>{{ $orderNumber }}</small>
        </div>

        <!-- En-tête officiel -->
        <div class="header">
            <div class="logo">
                LOGO<br>ÉCOLE
            </div>
            <div class="school-info">
                <div class="school-name">{{ strtoupper(Qs::getSetting('system_name')) }}</div>
                <div class="school-details">
                    {{ Qs::getSetting('address') ?? 'Adresse de l\'école' }}<br>
                    Tél: {{ Qs::getSetting('phone') ?? '+261 XX XX XXX XX' }} |
                    Email: {{ Qs::getSetting('email') ?? 'contact@ecole.mg' }}
                </div>
                <div class="document-title">Ordre de Paiement / Décaissement</div>
            </div>
        </div>

        <!-- Informations de l'ordre -->
        <div class="order-info">
            <div class="order-number">N° {{ $orderNumber }}</div>
            <div class="order-date">Date: {{ DateHelper::formatForReceipt($decaissement->date_paiement) }}</div>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <table class="content-table">
                <tr>
                    <td class="label">Projet/Rubrique budgétaire</td>
                    <td class="value">{{ $decaissement->projet->nom ?? $decaissement->motif }}</td>
                </tr>
                <tr>
                    <td class="label">Bénéficiaire</td>
                    <td class="value">
                        {{ $decaissement->beneficiaire }}
                        @if($decaissement->details_bancaires)
                            <br><small><strong>Détails bancaires:</strong> {{ $decaissement->details_bancaires }}</small>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Motif du paiement</td>
                    <td class="value">{{ $decaissement->description ?? $decaissement->motif }}</td>
                </tr>
                <tr>
                    <td class="label">Mode de paiement</td>
                    <td class="value">{{ ucfirst(str_replace('_', ' ', $decaissement->methode_paiement)) }}</td>
                </tr>
                <tr>
                    <td class="label">Référence du dossier</td>
                    <td class="value">{{ $decaissement->reference ?? 'N/A' }}</td>
                </tr>
                @if($decaissement->user)
                <tr>
                    <td class="label">Demandé par</td>
                    <td class="value">{{ $decaissement->user->name }}</td>
                </tr>
                @endif
            </table>

            <!-- Section montant -->
            <div class="amount-section">
                <div class="amount-numbers">
                    Montant: {{ DateHelper::formatAmount($decaissement->montant) }}
                </div>
                <div class="amount-words">
                    {{ $montantEnLettres }}
                </div>
            </div>

            <!-- Pièces justificatives -->
            <div class="justificatives">
                <div class="justificatives-title">Pièces justificatives jointes:</div>
                <div class="justificatives-list">
                    <div class="checkbox-item">
                        <span class="checkbox {{ $decaissement->piece ? 'checked' : '' }}">{{ $decaissement->piece ? '✓' : '' }}</span>
                        <span>Facture</span>
                    </div>
                    <div class="checkbox-item">
                        <span class="checkbox">{{ $decaissement->description ? '✓' : '' }}</span>
                        <span>Devis</span>
                    </div>
                    <div class="checkbox-item">
                        <span class="checkbox">{{ $decaissement->reference ? '✓' : '' }}</span>
                        <span>Note de frais</span>
                    </div>
                    <div class="checkbox-item">
                        <span class="checkbox"></span>
                        <span>Autre: _____________</span>
                    </div>
                </div>
            </div>

            @if($decaissement->description && strlen($decaissement->description) > 100)
            <div style="margin-top: 15px;">
                <strong>Observations:</strong><br>
                {{ $decaissement->description }}
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-title">Établi par</div>
                <div class="signature-line">
                    {{ $decaissement->user->name ?? 'N/A' }}<br>
                    {{ DateHelper::formatForReceipt($decaissement->created_at) }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Visa du Responsable Financier</div>
                <div class="signature-line">
                    Signature + Cachet
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Validation du Directeur</div>
                <div class="signature-line">
                    Signature + Cachet
                </div>
            </div>
        </div>
    </div>

    <!-- Ligne de coupe -->
    <div style="text-align: center; margin: 20px 0; color: #666; font-size: 10pt;">
        ✂ -------------------------------- DÉCOUPER ICI -------------------------------- ✂
    </div>

    <!-- Deuxième page: REÇU DE PAIEMENT -->
    <div class="page page-break">
        <!-- QR Code optionnel -->
        <div class="qr-section">
            QR CODE<br>
            <small>{{ $orderNumber }}</small>
        </div>

        <!-- En-tête officiel -->
        <div class="header">
            <div class="logo">
                LOGO<br>ÉCOLE
            </div>
            <div class="school-info">
                <div class="school-name">{{ strtoupper(Qs::getSetting('system_name')) }}</div>
                <div class="school-details">
                    {{ Qs::getSetting('address') ?? 'Adresse de l\'école' }}<br>
                    Tél: {{ Qs::getSetting('phone') ?? '+261 XX XX XXX XX' }} |
                    Email: {{ Qs::getSetting('email') ?? 'contact@ecole.mg' }}
                </div>
                <div class="document-title" style="background-color: #28a745; border-color: #28a745; color: white;">REÇU DE PAIEMENT</div>
            </div>
        </div>

        <!-- Informations de l'ordre -->
        <div class="order-info">
            <div class="order-number">N° {{ $orderNumber }}</div>
            <div class="order-date">Date: {{ DateHelper::formatForReceipt($decaissement->date_paiement) }}</div>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <table class="content-table">
                <tr>
                    <td class="label">Projet/Rubrique budgétaire</td>
                    <td class="value">{{ $decaissement->projet->nom ?? $decaissement->motif }}</td>
                </tr>
                <tr>
                    <td class="label">Bénéficiaire</td>
                    <td class="value">
                        {{ $decaissement->beneficiaire }}
                        @if($decaissement->details_bancaires)
                            <br><small><strong>Détails bancaires:</strong> {{ $decaissement->details_bancaires }}</small>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Motif du paiement</td>
                    <td class="value">{{ $decaissement->description ?? $decaissement->motif }}</td>
                </tr>
                <tr>
                    <td class="label">Mode de paiement</td>
                    <td class="value">{{ ucfirst(str_replace('_', ' ', $decaissement->methode_paiement)) }}</td>
                </tr>
                <tr>
                    <td class="label">Référence du dossier</td>
                    <td class="value">{{ $decaissement->reference ?? 'N/A' }}</td>
                </tr>
            </table>

            <!-- Section montant -->
            <div class="amount-section">
                <div class="amount-numbers">
                    Montant: {{ DateHelper::formatAmount($decaissement->montant) }}
                </div>
                <div class="amount-words">
                    {{ $montantEnLettres }}
                </div>
            </div>

            <!-- Section de confirmation de réception -->
            <div class="receipt-confirmation">
                <div class="receipt-title">
                    CONFIRMATION DE RÉCEPTION
                </div>
                <p>
                    Je soussigné(e), <strong>{{ $decaissement->beneficiaire }}</strong>, confirme avoir reçu la somme de 
                    <strong>{{ DateHelper::formatAmount($decaissement->montant) }}</strong> 
                    ({{ $montantEnLettres }}) en date du _____________________, 
                    par {{ ucfirst(str_replace('_', ' ', $decaissement->methode_paiement)) }}.
                </p>
                <div class="receipt-signature">
                    <div class="receipt-signature-box">
                        <div class="receipt-signature-label">Signature du bénéficiaire:</div>
                        <div class="receipt-signature-line"></div>
                    </div>
                    <div class="receipt-signature-box">
                        <div class="receipt-signature-label">Cachet (si applicable):</div>
                        <div class="receipt-stamp"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-title">Établi par</div>
                <div class="signature-line">
                    {{ $decaissement->user->name ?? 'N/A' }}<br>
                    {{ DateHelper::formatForReceipt($decaissement->created_at) }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Visa du Responsable Financier</div>
                <div class="signature-line">
                    Signature + Cachet
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Validation du Directeur</div>
                <div class="signature-line">
                    Signature + Cachet
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'impression (visible uniquement à l'écran) -->
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14pt;">
            <span style="margin-right: 5px;">🖨️</span> Imprimer
        </button>
    </div>
</body>
</html>