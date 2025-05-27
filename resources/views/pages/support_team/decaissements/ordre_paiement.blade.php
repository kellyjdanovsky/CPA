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
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #000;
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
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-right: 20px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
        }
        
        .school-info {
            flex: 1;
            text-align: center;
        }
        
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .school-details {
            font-size: 10pt;
            color: #666;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        /* Informations de l'ordre */
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .order-number {
            font-size: 14pt;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px 15px;
            border: 2px solid #000;
            border-radius: 5px;
        }
        
        .order-date {
            font-size: 12pt;
            font-weight: bold;
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
            background-color: #fff3cd;
            border: 2px solid #856404;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .amount-numbers {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .amount-words {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            font-style: italic;
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
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 30%;
            text-align: center;
            border: 1px solid #000;
            padding: 15px;
            min-height: 80px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 10pt;
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
        
        /* Styles pour l'impression */
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .no-print {
                display: none !important;
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
        }
    </style>
</head>
<body>
    @php
        use App\Helpers\DateHelper;
        
        // Générer le numéro d'ordre automatique
        $orderNumber = 'OP-' . date('Y') . '-' . str_pad($decaissement->id, 4, '0', STR_PAD_LEFT);
        
        // Convertir le montant en lettres (fonction à implémenter)
        $montantEnLettres = convertirMontantEnLettres($decaissement->montant);
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
    
    <!-- Deuxième ordre de paiement (copie) -->
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
                <div class="document-title">Ordre de Paiement / Décaissement <small>(COPIE)</small></div>
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

    <script>
        // Auto-impression
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>

@php
function convertirMontantEnLettres($montant) {
    // Fonction simple de conversion - à améliorer selon les besoins
    $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    $centaines = ['', 'cent', 'deux cents', 'trois cents', 'quatre cents', 'cinq cents', 'six cents', 'sept cents', 'huit cents', 'neuf cents'];
    
    $montant = intval($montant);
    
    if ($montant == 0) return 'Zéro ariary';
    if ($montant < 1000) return 'Moins de mille ariary';
    
    $millions = intval($montant / 1000000);
    $milliers = intval(($montant % 1000000) / 1000);
    $reste = $montant % 1000;
    
    $resultat = '';
    
    if ($millions > 0) {
        if ($millions == 1) {
            $resultat .= 'Un million ';
        } else {
            $resultat .= convertirNombre($millions) . ' millions ';
        }
    }
    
    if ($milliers > 0) {
        if ($milliers == 1) {
            $resultat .= 'mille ';
        } else {
            $resultat .= convertirNombre($milliers) . ' mille ';
        }
    }
    
    if ($reste > 0) {
        $resultat .= convertirNombre($reste) . ' ';
    }
    
    return ucfirst(trim($resultat)) . ' ariary';
}

function convertirNombre($nombre) {
    if ($nombre < 20) {
        $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        return $unites[$nombre];
    }
    
    if ($nombre < 100) {
        $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
        $diz = intval($nombre / 10);
        $unit = $nombre % 10;
        
        if ($unit == 0) {
            return $dizaines[$diz];
        } else {
            return $dizaines[$diz] . '-' . convertirNombre($unit);
        }
    }
    
    if ($nombre < 1000) {
        $cent = intval($nombre / 100);
        $reste = $nombre % 100;
        
        $resultat = '';
        if ($cent == 1) {
            $resultat = 'cent';
        } else {
            $resultat = convertirNombre($cent) . ' cents';
        }
        
        if ($reste > 0) {
            $resultat .= ' ' . convertirNombre($reste);
        }
        
        return $resultat;
    }
    
    return strval($nombre);
}
@endphp