<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre_Presence_{{ str_replace(' ', '_', $my_class->name) }}</title>
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
            color: #000;
            background: #fff;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
        }

        .school-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .sheet-title {
            font-size: 11pt;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            background: #f1f5f9;
            border: 1px solid #cbd5e0;
            padding: 1.5mm 3mm;
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 3mm;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #000;
            padding: 1.2mm 1mm;
            font-size: 7.5pt;
        }

        .attendance-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        .day-col {
            width: 4.5mm;
            text-align: center;
            font-size: 6.5pt;
        }

        .student-name {
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 55mm;
        }

        .footer-signatures {
            width: 100%;
            margin-top: 4mm;
            border-collapse: collapse;
        }

        .footer-signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
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
        <button onclick="window.print()" style="padding: 6px 14px; background: #1a365d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Imprimer la Feuille de Présence (A4 Paysage)
        </button>
    </div>

    <!-- En-tête -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="school-title">{{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}</div>
                <div style="font-size: 8pt; color: #475569;">AVARATETEZANA AMPITATAFIKA</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="sheet-title">REGISTRE D'APPEL & FEUILLE D'ÉMARGEMENT</div>
                <div style="font-size: 8pt;">Année Scolaire : <strong>{{ Qs::getCurrentSession() }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Barre d'infos classe et mois -->
    <div class="info-bar">
        <div>Classe : <strong>{{ $my_class->name }}</strong> @if(isset($section)) - Section : <strong>{{ $section->name }}</strong> @endif</div>
        <div>Mois : ________________________ 202___</div>
        <div>Effectif total : <strong>{{ $students->count() }} élèves</strong> (♂ {{ $students->where('user.gender', 'Male')->count() }} | ♀ {{ $students->where('user.gender', 'Female')->count() }})</div>
    </div>

    <!-- Tableau de pointage journalier -->
    <table class="attendance-table">
        <thead>
            <tr>
                <th style="width: 25px;">N°</th>
                <th style="width: 160px; text-align: left;">Nom & Prénom de l'élève</th>
                <th style="width: 30px;">Sexe</th>
                <th style="width: 65px;">N° Adm</th>
                @for($d = 1; $d <= 31; $d++)
                    <th class="day-col">{{ $d }}</th>
                @endfor
                <th style="width: 35px;">Total Abs.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students->sortBy('user.name') as $s)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $loop->iteration }}</td>
                    <td class="student-name">{{ strtoupper($s->user->name) }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $s->user->gender == 'Male' ? 'M' : ($s->user->gender == 'Female' ? 'F' : '-') }}</td>
                    <td style="text-align: center; font-size: 7pt;">{{ $s->adm_no }}</td>
                    @for($d = 1; $d <= 31; $d++)
                        <td class="day-col"></td>
                    @endfor
                    <td style="text-align: center;"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="footer-signatures">
        <tr>
            <td>
                <strong>LE PROFESSEUR / L'ENSEIGNANT(E) TITULAIRE</strong><br><br><br>
                <small style="color: #64748b;">(Visa mensuel)</small>
            </td>
            <td>
                <strong>LE DIRECTEUR DES ÉTUDES / LA DIRECTION</strong><br><br><br>
                <small style="color: #64748b;">(Contrôle & Visa)</small>
            </td>
        </tr>
    </table>

</body>
</html>
