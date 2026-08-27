<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procès_Verbal_Clôture_Caisse_{{ date('d_m_Y', strtotime($startDate)) }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9.5pt;
            line-height: 1.3;
            color: #1a1a1a;
            background: #fff;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 3mm;
            margin-bottom: 4mm;
        }

        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
        }

        .pv-title {
            text-align: center;
            background-color: #1a365d;
            color: #fff;
            padding: 2.5mm;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4mm;
            border-radius: 3px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 4mm;
            border: 1px solid #cbd5e0;
            background-color: #f8fafc;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 2mm 3mm;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
        }

        .info-cell strong {
            color: #475569;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1a365d;
            border-bottom: 1.5px solid #1a365d;
            padding-bottom: 1mm;
            margin: 4mm 0 2mm 0;
            text-transform: uppercase;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }

        .data-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e0;
            padding: 2mm;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #cbd5e0;
            padding: 1.8mm 2mm;
            font-size: 8.5pt;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .total-box {
            background-color: #f0fdf4;
            border: 1.5px solid #16a34a;
            padding: 3mm;
            margin-bottom: 5mm;
            border-radius: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 10.5pt;
            font-weight: bold;
            color: #166534;
        }

        .signatures-table {
            width: 100%;
            margin-top: 8mm;
            border-collapse: collapse;
        }

        .signatures-table td {
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 4mm;
        }

        .signature-box {
            border-top: 1.5px dotted #64748b;
            padding-top: 2mm;
            font-size: 8.5pt;
            min-height: 25mm;
        }

        .no-print-bar {
            background: #f8fafc;
            border: 1px solid #cbd5e0;
            padding: 10px;
            text-align: right;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1a365d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Imprimer le Procès-Verbal (A4)
        </button>
    </div>

    <!-- En-tête officiel -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="school-name">{{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}</div>
                <div style="font-size: 8.5pt; color: #475569;">AVARATETEZANA AMPITATAFIKA - ANTANANARIVO</div>
                <div style="font-size: 8pt; color: #64748b;">SERVICE DE LA COMPTABILITÉ & GESTION FINANCIÈRE</div>
            </td>
            <td style="width: 30%; text-align: right; font-size: 8.5pt;">
                <div>Année Scolaire : <strong>{{ $year ?? Qs::getCurrentSession() }}</strong></div>
                <div>Date Tirage : <strong>{{ date('d/m/Y H:i') }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Titre -->
    <div class="pv-title">
        PROCÈS-VERBAL DE CLÔTURE DE CAISSE
    </div>

    <!-- Période & Caissier -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><strong>Période concernée :</strong> {{ date('d/m/Y', strtotime($startDate)) }} @if($startDate != $endDate) au {{ date('d/m/Y', strtotime($endDate)) }} @endif</div>
            <div class="info-cell"><strong>Opérateur / Caissier :</strong> {{ strtoupper(Auth::user()->name) }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><strong>Nombre d'encaissements :</strong> {{ $receipts->count() }} transaction(s)</div>
            <div class="info-cell"><strong>Total Encaissé :</strong> <span style="color: #16a34a; font-weight: bold;">{{ number_format($totalAmount, 0, ',', ' ') }} Ar</span></div>
        </div>
    </div>

    <!-- Ventilation par mode de règlement -->
    <div class="section-title">1. Répartition par Mode de Règlement</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Mode de Paiement</th>
                <th style="text-align: center;">Nombre de Reçus</th>
                <th style="text-align: right;">Montant Total (Ar)</th>
                <th style="text-align: right;">Pourcentage (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $methods = $receipts->groupBy(function($r) {
                    return strtoupper($r->methode ?? $r->payment_method ?? 'CASH');
                });
            @endphp
            @foreach($methods as $methodName => $methodReceipts)
                @php
                    $mAmount = $methodReceipts->sum('amt_paid');
                    $mPercent = $totalAmount > 0 ? round(($mAmount / $totalAmount) * 100, 1) : 0;
                @endphp
                <tr>
                    <td><strong>{{ $methodName }}</strong></td>
                    <td style="text-align: center;">{{ $methodReceipts->count() }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($mAmount, 0, ',', ' ') }}</td>
                    <td style="text-align: right;">{{ $mPercent }} %</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td>TOTAL ENCAISSÉ</td>
                <td style="text-align: center;">{{ $receipts->count() }}</td>
                <td style="text-align: right; color: #16a34a;">{{ number_format($totalAmount, 0, ',', ' ') }} Ar</td>
                <td style="text-align: right;">100.0 %</td>
            </tr>
        </tfoot>
    </table>

    <!-- Ventilation par objet de paiement -->
    <div class="section-title">2. Répartition par Objet de Frais</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Rubrique / Objet de Frais</th>
                <th style="text-align: right;">Montant Encaissé (Ar)</th>
                <th style="text-align: right;">Part (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentTypeTotals as $pTitle => $pAmt)
                @php $pPct = $totalAmount > 0 ? round(($pAmt / $totalAmount) * 100, 1) : 0; @endphp
                <tr>
                    <td>{{ $pTitle }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($pAmt, 0, ',', ' ') }}</td>
                    <td style="text-align: right;">{{ $pPct }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 3. Billetage Physique des Espèces (Contrôle de Caisse) -->
    <div class="section-title">3. Billetage & Rapprochement Physique des Espèces</div>
    <table class="data-table" style="font-size: 7pt;">
        <thead>
            <tr style="background: #f8fafc;">
                <th style="width: 25%;">Coupure / Billet</th>
                <th style="width: 25%; text-align: center;">Nombre de Billets</th>
                <th style="width: 25%; text-align: right;">Montant Calculé (Ar)</th>
                <th style="width: 25%; text-align: center;">Observations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>20 000 Ariary</strong></td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">...................</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>10 000 Ariary</strong></td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">...................</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>5 000 Ariary</strong></td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">...................</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>2 000 Ariary</strong></td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">...................</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>1 000 Ariary</strong></td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">...................</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>500 Ariary / Pièces</strong></td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">...................</td>
                <td></td>
            </tr>
            <tr style="font-weight: bold; background: #f1f5f9;">
                <td>TOTAL PHYSIQUE COMPTÉ</td>
                <td style="text-align: center; color: #94a3b8;">..........</td>
                <td style="text-align: right; color: #94a3b8;">................... Ar</td>
                <td style="text-align: center; font-size: 7pt;">Écart : [ ] 0 Ar &nbsp; [ ] +/- .....</td>
            </tr>
        </tbody>
    </table>

    <!-- Solde net en caisse -->
    <div class="total-box">
        <div class="total-row">
            <span>MONTANT TOTAL CLÔTURÉ À REVERSER :</span>
            <span>{{ number_format($totalAmount, 0, ',', ' ') }} Ariary</span>
        </div>
    </div>

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-box">
                    <strong>LE CAISSIER / LA CAISSIÈRE</strong><br>
                    <small style="color: #64748b;">(Visa & Décharge)</small><br><br>
                    {{ Auth::user()->name }}
                </div>
            </td>
            <td>
                <div class="signature-box">
                    <strong>LE COMPTABLE</strong><br>
                    <small style="color: #64748b;">(Vérification des pièces)</small><br><br>
                </div>
            </td>
            <td>
                <div class="signature-box">
                    <strong>LE DIRECTEUR / LA DIRECTION</strong><br>
                    <small style="color: #64748b;">(Approbation de clôture)</small><br><br>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
