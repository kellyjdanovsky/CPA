{{-- Template officiel d'Avis des Impayés / Fampahafantarana Fandoavam-bola --}}
{{-- Calibrage strict A4 : 10 avis par page (2 colonnes x 5 rangées) sans coupure de mots --}}

@php
    $studentsPerPage = 10;
    $totalStudents = count($unpaid_students);
    $totalPages = max(1, ceil($totalStudents / $studentsPerPage));
@endphp

@if(!isset($is_preview) && !isset($is_pdf))
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis_Impayes_{{ str_replace(' ', '_', $class_name) }}</title>
@endif

<style>
    @page {
        size: A4 portrait;
        margin: 4mm 4mm;
    }

    * {
        box-sizing: border-box;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        hyphens: none !important;
        -webkit-hyphens: none !important;
        word-break: normal !important;
        overflow-wrap: normal !important;
    }

    body, .notifications-body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: #fff;
        color: #000;
    }

    .page-a4 {
        width: 202mm;
        height: 288mm;
        margin: 0 auto;
        padding: 1mm;
        display: grid;
        grid-template-columns: repeat(2, 98.5mm);
        grid-template-rows: repeat(5, 56mm);
        gap: 2.5mm;
        page-break-inside: avoid;
        page-break-after: always;
        overflow: hidden;
    }

    .page-a4:last-child {
        page-break-after: avoid;
    }

    .notice-card {
        width: 98.5mm;
        height: 56mm;
        border: 1.2px dashed #475569;
        border-radius: 2mm;
        padding: 1.8mm 2mm;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        position: relative;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .notice-card.is-overdue {
        border-color: #b91c1c;
        background: #fffafa;
    }

    /* En-tête */
    .notice-header {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #1e293b;
        padding-bottom: 1mm;
        margin-bottom: 1mm;
    }

    .notice-logo {
        width: 8.5mm;
        height: 8.5mm;
        object-fit: contain;
        margin-right: 1.5mm;
    }

    .notice-school-meta {
        flex: 1;
        text-align: center;
        line-height: 1.1;
    }

    .school-title {
        font-size: 5.8pt;
        font-weight: 900;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.1px;
        white-space: nowrap;
    }

    .school-sub {
        font-size: 4.6pt;
        color: #475569;
        white-space: nowrap;
    }

    /* Bannière Titre */
    .notice-banner {
        background: #f1f5f9;
        border: 0.8px solid #cbd5e1;
        border-radius: 1mm;
        text-align: center;
        font-size: 5.5pt;
        font-weight: 800;
        color: #0f172a;
        padding: 0.6mm;
        text-transform: uppercase;
        margin-bottom: 1mm;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notice-banner.overdue {
        background: #fee2e2;
        border-color: #f87171;
        color: #991b1b;
    }

    /* Informations Élève & Motif */
    .notice-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.8mm;
    }

    .student-row {
        background: #f8fafc;
        border: 0.5px solid #e2e8f0;
        border-radius: 1mm;
        padding: 0.8mm 1.2mm;
        line-height: 1.15;
    }

    .parent-dest {
        font-size: 4.8pt;
        color: #64748b;
        font-weight: 600;
    }

    .student-fullname {
        font-size: 6.8pt;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .student-meta {
        font-size: 5.2pt;
        font-weight: 700;
        color: #1e3a8a;
        display: flex;
        justify-content: space-between;
    }

    .unpaid-reason {
        font-size: 5pt;
        line-height: 1.2;
        background: #fffbeb;
        border: 0.5px solid #fde68a;
        border-radius: 1mm;
        padding: 0.6mm 1.2mm;
        color: #92400e;
    }

    .unpaid-reason strong {
        color: #78350f;
    }

    /* Grille des Montants */
    .amounts-grid {
        background: #ffffff;
        border: 0.8px solid #cbd5e1;
        border-radius: 1mm;
        padding: 0.6mm 1.2mm;
        font-size: 5.2pt;
        line-height: 1.25;
    }

    .amt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .amt-row.highlight {
        border-top: 0.8px solid #cbd5e1;
        margin-top: 0.4mm;
        padding-top: 0.4mm;
        font-size: 6.5pt;
        font-weight: 900;
        color: #b91c1c;
    }

    /* Pied de Carte */
    .notice-footer {
        border-top: 0.8px solid #e2e8f0;
        padding-top: 0.6mm;
        margin-top: 0.6mm;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 4.8pt;
        line-height: 1.1;
    }

    .deadline-badge {
        background: #0f172a;
        color: #ffffff;
        font-weight: 800;
        font-size: 5.2pt;
        padding: 0.4mm 1.2mm;
        border-radius: 0.8mm;
        display: inline-block;
    }

    .thanks-msg {
        font-style: italic;
        color: #475569;
        font-size: 4.5pt;
    }

    .cut-guide {
        position: absolute;
        top: 1mm;
        right: 1mm;
        font-size: 5pt;
        color: #94a3b8;
    }

    .empty-card {
        width: 98.5mm;
        height: 56mm;
        border: 1px dashed #e2e8f0;
        border-radius: 2mm;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 6pt;
        font-style: italic;
    }

    @media print {
        .no-print-bar {
            display: none !important;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

@if(!isset($is_preview) && !isset($is_pdf))
</head>
<body class="notifications-body">
@else
<div class="notifications-body">
@endif

@for ($page = 0; $page < $totalPages; $page++)
    <div class="page-a4">
        @for ($i = 0; $i < $studentsPerPage; $i++)
            @php
                $studentIndex = $page * $studentsPerPage + $i;
            @endphp

            @if ($studentIndex < $totalStudents)
                @php
                    $studentData = $unpaid_students[$studentIndex];
                    $student = $studentData['student'];
                    $status = $studentData['status'];
                    $amountDue = $studentData['amount_due'];
                    $amountPaid = $studentData['amount_paid'] ?? 0;
                    $totalAmount = $studentData['total_amount'] ?? ($amountDue + $amountPaid);
                    $paymentTitles = $studentData['payment_titles'];

                    $isOverdue = false;
                    try {
                        $deadlineTs = strtotime($payment_deadline);
                        $todayTs = strtotime(date('Y-m-d'));
                        $isOverdue = $todayTs > $deadlineTs && ($amountDue ?? 0) > 0;
                    } catch (\Exception $e) {
                        $isOverdue = false;
                    }
                @endphp

                <div class="notice-card {{ $isOverdue ? 'is-overdue' : '' }}">
                    <span class="cut-guide">✂️</span>

                    <!-- En-tête -->
                    <div class="notice-header">
                        <img src="{{ asset('global_assets/images/favicon.png') }}" class="notice-logo" alt="Logo">
                        <div class="notice-school-meta">
                            <div class="school-title">{{ strtoupper(Qs::getSetting('system_name') ?? 'COLLÈGE PRIVÉ ADVENTISTE AVARATETEZANA') }}</div>
                            <div class="school-sub">Ampitatafika • Tél : 038 34 921 09 • Année {{ Qs::getCurrentSession() }}</div>
                        </div>
                    </div>

                    <!-- Bannière Titre -->
                    <div class="notice-banner {{ $isOverdue ? 'overdue' : '' }}">
                        <span>FAMPAHAFANTARANA FANDOAVAM-BOLA</span>
                        <span>{{ $isOverdue ? 'TARA (EN RETARD)' : 'AVIS D\'IMPAYÉ' }}</span>
                    </div>

                    <!-- Corps de l'avis -->
                    <div class="notice-body">
                        <!-- Élève -->
                        <div class="student-row">
                            <div class="parent-dest">Ho an'ny Ray aman-drenin'i (Parent de) :</div>
                            <div class="student-fullname">{{ $student->user->name }}</div>
                            <div class="student-meta">
                                <span>Classe : <strong>{{ $class_name }}{{ $student->section ? ' ' . $student->section->name : '' }}</strong></span>
                                <span>Matricule : <strong>{{ $student->adm_no }}</strong></span>
                            </div>
                        </div>

                        <!-- Motif / Échéance -->
                        <div class="unpaid-reason">
                            <strong>Antony tsy voaloa (Motif) :</strong> {{ $paymentTitles ?: 'Saram-pianarana / Écolage' }}
                        </div>

                        <!-- Décompte Financier -->
                        <div class="amounts-grid">
                            <div class="amt-row">
                                <span>• Totalin'ny sarany (Montant total) :</span>
                                <strong>{{ number_format($totalAmount, 0, ',', ' ') }} Ar</strong>
                            </div>
                            @if($amountPaid > 0)
                            <div class="amt-row" style="color: #15803d;">
                                <span>• Vola efa naloa (Déjà payé) :</span>
                                <strong>- {{ number_format($amountPaid, 0, ',', ' ') }} Ar</strong>
                            </div>
                            @endif
                            <div class="amt-row highlight">
                                <span>• VOLA MBOLA HALOA (RESTE À PAYER) :</span>
                                <span>{{ number_format($amountDue, 0, ',', ' ') }} Ar</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pied & Date Limite -->
                    <div class="notice-footer">
                        <div>
                            <div>Daty farany fandoavana :</div>
                            <div class="deadline-badge">{{ date('d/m/Y', strtotime($payment_deadline)) }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="thanks-msg">Misaotra amin'ny fiaraha-miasa</div>
                            <div style="font-size: 4.5pt; color: #64748b;">Ny Mpitantana Caisse / Comptabilité</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-card">
                    Emplacement libre
                </div>
            @endif
        @endfor
    </div>
@endfor

@if(!isset($is_preview) && !isset($is_pdf))
</body>
</html>
@else
</div>
@endif