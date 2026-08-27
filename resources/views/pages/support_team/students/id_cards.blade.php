<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes_Scolaires_{{ str_replace(' ', '_', $my_class->name) }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 94mm);
            grid-template-rows: repeat(4, 64mm);
            gap: 5mm;
            justify-content: center;
        }

        .id-card {
            width: 94mm;
            height: 64mm;
            border: 1.5px solid #1e3a8a;
            border-radius: 4mm;
            padding: 2mm;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .card-header {
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #1e3a8a;
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
        }

        .card-logo {
            width: 10mm;
            height: 10mm;
            object-fit: contain;
            margin-right: 2mm;
        }

        .card-school-info {
            flex: 1;
            text-align: center;
            line-height: 1.1;
        }

        .card-school-name {
            font-size: 7.5pt;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .card-school-sub {
            font-size: 5.5pt;
            color: #475569;
        }

        .card-body {
            display: flex;
            gap: 2.5mm;
            flex: 1;
        }

        .card-photo-box {
            width: 20mm;
            height: 25mm;
            border: 1px solid #94a3b8;
            border-radius: 2px;
            overflow: hidden;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-student-info {
            flex: 1;
            font-size: 6.5pt;
            line-height: 1.25;
        }

        .student-name {
            font-size: 8pt;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 55mm;
        }

        .card-badge {
            display: inline-block;
            background: #1e3a8a;
            color: white;
            padding: 0.5mm 2mm;
            border-radius: 2px;
            font-weight: bold;
            font-size: 6pt;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #cbd5e0;
            padding-top: 1mm;
            margin-top: 1mm;
            font-size: 5.5pt;
        }

        .barcode-box {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 7.5pt;
            letter-spacing: 1px;
            border: 1px dashed #94a3b8;
            padding: 0.5mm 2mm;
            border-radius: 2px;
        }

        .sig-director {
            text-align: center;
            font-size: 5.5pt;
            color: #64748b;
        }

        .page-break {
            page-break-after: always;
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
            🖨️ Imprimer les Cartes Scolaires (A4)
        </button>
    </div>

    @php
        $studentsChunk = $students->sortBy('user.name')->chunk(8);
    @endphp

    @foreach($studentsChunk as $chunk)
        <div class="cards-grid {{ !$loop->last ? 'page-break' : '' }}">
            @foreach($chunk as $s)
                <div class="id-card">
                    <!-- En-tête carte -->
                    <div class="card-header">
                        <img src="{{ asset('global_assets/images/favicon.png') }}" class="card-logo" alt="Logo">
                        <div class="card-school-info">
                            <div class="card-school-name">{{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE') }}</div>
                            <div class="card-school-sub">CARTE D'IDENTITÉ SCOLAIRE • {{ Qs::getCurrentSession() }}</div>
                        </div>
                    </div>

                    <!-- Corps de carte -->
                    <div class="card-body">
                        <div class="card-photo-box">
                            <img src="{{ $s->user->photo ?? asset('global_assets/images/user.png') }}" class="card-photo" alt="Photo">
                        </div>
                        <div class="card-student-info">
                            <div class="student-name">{{ $s->user->name }}</div>
                            <div>Classe : <strong>{{ $my_class->name }}</strong> @if($s->section) ({{ $s->section->name }}) @endif</div>
                            <div>Né(e) le : <strong>{{ $s->user->dob ? date('d/m/Y', strtotime($s->user->dob)) : '-' }}</strong></div>
                            <div>Matricule : <strong>{{ $s->adm_no }}</strong></div>
                            <div style="margin-top: 1mm;">
                                <span class="card-badge">ÉLÈVE RÉGULIER</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pied de carte -->
                    <div class="card-footer">
                        <div class="barcode-box">
                            *{{ $s->adm_no }}*
                        </div>
                        <div class="sig-director">
                            Le Directeur<br>
                            <em>(Signature & Sceau)</em>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

</body>
</html>
