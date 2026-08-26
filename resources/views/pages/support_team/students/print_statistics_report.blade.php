<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport_Statistiques_Eleves_{{ date('Y_m_d') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 8pt;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.25;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
        }

        .school-title {
            font-size: 13pt;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 11pt;
            font-weight: 900;
            text-align: right;
            text-transform: uppercase;
            color: #0f172a;
        }

        .kpi-row {
            display: flex;
            gap: 2mm;
            margin-bottom: 3mm;
        }

        .kpi-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            padding: 2mm;
            text-align: center;
        }

        .kpi-value {
            font-size: 13pt;
            font-weight: 900;
            color: #1e3a8a;
        }

        .kpi-label {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }

        .section-header {
            background-color: #1e3a8a;
            color: white;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 1.5mm 3mm;
            margin: 3mm 0 1.5mm 0;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e0;
            padding: 1.5mm 1.5mm;
            font-size: 7.5pt;
        }

        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .data-table .total-row {
            background-color: #e2e8f0 !important;
            font-weight: bold;
        }

        .grid-2col {
            display: flex;
            gap: 4mm;
            margin-bottom: 3mm;
        }

        .grid-2col > div {
            flex: 1;
        }

        .signatures-table {
            width: 100%;
            margin-top: 4mm;
            border-collapse: collapse;
        }

        .signatures-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
        }

        .sig-box {
            border-top: 1px dotted #64748b;
            padding-top: 2mm;
            min-height: 18mm;
        }

        .no-print {
            background: #f8fafc;
            border: 1px solid #cbd5e0;
            padding: 8px;
            text-align: right;
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 6px 14px; background: #1e3a8a; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Imprimer le Rapport Statistique (A4 Paysage)
        </button>
    </div>

    <!-- En-tête officiel -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="school-title">{{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}</div>
                <div style="font-size: 8pt; color: #475569;">AVARATETEZANA AMPITATAFIKA - ANTANANARIVO</div>
                <div style="font-size: 7.5pt; color: #64748b;">DIRECTION DES ÉTUDES & REGISTRE DES EFFECTIFS</div>
            </td>
            <td style="width: 45%; text-align: right;">
                <div class="report-title">RAPPORT STATISTIQUE GÉNÉRAL DES ÉLÈVES</div>
                <div style="font-size: 8pt;">Année Scolaire : <strong>{{ $year ?? Qs::getCurrentSession() }}</strong></div>
                <div style="font-size: 7.5pt; color: #64748b;">Édité le : <strong>{{ date('d/m/Y H:i') }}</strong> par {{ Auth::user()->name }}</div>
            </td>
        </tr>
    </table>

    <!-- Synthèse Chiffrée (KPIs) -->
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-value">{{ $total_students }}</div>
            <div class="kpi-label">Effectif Total</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color: #2563eb;">{{ $total_boys }} <span style="font-size: 8pt; font-weight: normal;">({{ $total_students > 0 ? round(($total_boys/$total_students)*100, 1) : 0 }}%)</span></div>
            <div class="kpi-label">Garçons (♂)</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color: #db2777;">{{ $total_girls }} <span style="font-size: 8pt; font-weight: normal;">({{ $total_students > 0 ? round(($total_girls/$total_students)*100, 1) : 0 }}%)</span></div>
            <div class="kpi-label">Filles (♀)</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color: #059669;">{{ $avg_age }} ans</div>
            <div class="kpi-label">Âge Moyen</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color: #d97706;">{{ $total_adra }} <span style="font-size: 8pt; font-weight: normal;">({{ $total_students > 0 ? round(($total_adra/$total_students)*100, 1) : 0 }}%)</span></div>
            <div class="kpi-label">Bénéficiaires ADRA</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color: #dc2626;">{{ $total_team3 }} <span style="font-size: 8pt; font-weight: normal;">({{ $total_students > 0 ? round(($total_team3/$total_students)*100, 1) : 0 }}%)</span></div>
            <div class="kpi-label">Bénéficiaires TEAM 3</div>
        </div>
    </div>

    <!-- 1. Tableau Croisé Détaillé par Classe -->
    <div class="section-header">1. Synthèse Croisée par Classe (Genre, Âge, Statut, Profil & Religions)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="text-align: left; width: 90px;">Classe</th>
                <th rowspan="2" style="width: 45px;">Total</th>
                <th colspan="3" style="background: #e0e7ff;">Genre</th>
                <th rowspan="2" style="width: 45px;">Âge Moy.</th>
                <th colspan="3" style="background: #ecfdf5;">Régime / Statut</th>
                <th colspan="2" style="background: #fef3c7;">Type</th>
                <th colspan="2" style="background: #f1f5f9;">Statut Acad.</th>
                <th colspan="4" style="background: #fae8ff;">Religions Principales</th>
            </tr>
            <tr>
                <th style="width: 32px; background: #e0e7ff;">♂ M</th>
                <th style="width: 32px; background: #e0e7ff;">♀ F</th>
                <th style="width: 38px; background: #e0e7ff;">% F</th>
                <th style="width: 40px; background: #ecfdf5;">Normal</th>
                <th style="width: 38px; background: #ecfdf5;">ADRA</th>
                <th style="width: 40px; background: #ecfdf5;">TEAM3</th>
                <th style="width: 42px; background: #fef3c7;">Nouv.</th>
                <th style="width: 42px; background: #fef3c7;">Anc.</th>
                <th style="width: 42px; background: #f1f5f9;">Pass.</th>
                <th style="width: 42px; background: #f1f5f9;">Redoub.</th>
                <th style="width: 35px; background: #fae8ff;">Adv.</th>
                <th style="width: 35px; background: #fae8ff;">Cath.</th>
                <th style="width: 35px; background: #fae8ff;">FJKM</th>
                <th style="width: 35px; background: #fae8ff;">Autr.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($class_matrix as $row)
                <tr>
                    <td style="font-weight: bold;">{{ $row['name'] }}</td>
                    <td style="text-align: center; font-weight: bold; background: #f8fafc;">{{ $row['total'] }}</td>
                    <td style="text-align: center;">{{ $row['boys'] }}</td>
                    <td style="text-align: center;">{{ $row['girls'] }}</td>
                    <td style="text-align: center; font-size: 7pt;">{{ $row['total'] > 0 ? round(($row['girls'] / $row['total']) * 100, 0) : 0 }}%</td>
                    <td style="text-align: center;">{{ $row['avg_age'] }}</td>
                    <td style="text-align: center;">{{ $row['normal'] }}</td>
                    <td style="text-align: center;">{{ $row['adra'] }}</td>
                    <td style="text-align: center;">{{ $row['team3'] }}</td>
                    <td style="text-align: center;">{{ $row['nouveau'] }}</td>
                    <td style="text-align: center;">{{ $row['ancien'] }}</td>
                    <td style="text-align: center;">{{ $row['passant'] }}</td>
                    <td style="text-align: center;">{{ $row['redoublant'] }}</td>
                    <td style="text-align: center;">{{ $row['adventiste'] }}</td>
                    <td style="text-align: center;">{{ $row['catholique'] }}</td>
                    <td style="text-align: center;">{{ $row['fjkm'] }}</td>
                    <td style="text-align: center;">{{ $row['autres_rel'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>TOTAL ÉCOLE</td>
                <td style="text-align: center; font-size: 8pt;">{{ $total_students }}</td>
                <td style="text-align: center;">{{ $total_boys }}</td>
                <td style="text-align: center;">{{ $total_girls }}</td>
                <td style="text-align: center;">{{ $total_students > 0 ? round(($total_girls / $total_students) * 100, 1) : 0 }}%</td>
                <td style="text-align: center;">{{ $avg_age }}</td>
                <td style="text-align: center;">{{ $total_normal }}</td>
                <td style="text-align: center;">{{ $total_adra }}</td>
                <td style="text-align: center;">{{ $total_team3 }}</td>
                <td style="text-align: center;">{{ $total_nouveaux }}</td>
                <td style="text-align: center;">{{ $total_anciens }}</td>
                <td style="text-align: center;">{{ $total_passants }}</td>
                <td style="text-align: center;">{{ $total_redoublants }}</td>
                <td style="text-align: center;">{{ $total_adventiste }}</td>
                <td style="text-align: center;">{{ $total_catholique }}</td>
                <td style="text-align: center;">{{ $total_fjkm }}</td>
                <td style="text-align: center;">{{ $total_autres_rel }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- 2. Grilles Répartition par Âge et Répartition par Religion -->
    <div class="grid-2col">
        <!-- Pyramide des Âges -->
        <div>
            <div class="section-header">2. Pyramide des Âges par Genre</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tranche d'âge</th>
                        <th style="text-align: center;">♂ Garçons</th>
                        <th style="text-align: center;">♀ Filles</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: center;">Part (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($age_breakdown as $range => $aData)
                        <tr>
                            <td><strong>{{ $range }}</strong></td>
                            <td style="text-align: center;">{{ $aData['boys'] }}</td>
                            <td style="text-align: center;">{{ $aData['girls'] }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $aData['total'] }}</td>
                            <td style="text-align: center;">{{ $total_students > 0 ? round(($aData['total'] / $total_students) * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Répartition par Religion -->
        <div>
            <div class="section-header">3. Répartition par Religion / Culte</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Religion / Dénomination</th>
                        <th style="text-align: center;">Effectif</th>
                        <th style="text-align: center;">♂ Garçons</th>
                        <th style="text-align: center;">♀ Filles</th>
                        <th style="text-align: center;">Pourcentage (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($religion_breakdown as $relName => $rData)
                        <tr>
                            <td><strong>{{ $relName }}</strong></td>
                            <td style="text-align: center; font-weight: bold;">{{ $rData['total'] }}</td>
                            <td style="text-align: center;">{{ $rData['boys'] }}</td>
                            <td style="text-align: center;">{{ $rData['girls'] }}</td>
                            <td style="text-align: center;">{{ $total_students > 0 ? round(($rData['total'] / $total_students) * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-box">
                    <strong>LE RESPONSABLE DE LA SCOLARITÉ</strong><br><br><br>
                </div>
            </td>
            <td>
                <div class="sig-box">
                    <strong>LE CONSEILLER PÉDAGOGIQUE</strong><br><br><br>
                </div>
            </td>
            <td>
                <div class="sig-box">
                    <strong>LE DIRECTEUR DU COLLÈGE</strong><br><br><br>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
