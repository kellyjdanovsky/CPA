<!DOCTYPE html>
<html>
<head>
    <title>Bulletin de notes de l'élève - {{ $sr->user->name }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/my_print.css') }}" />
    <style>
        /* Styles supplémentaires pour garantir l'orientation paysage */
        @page {
            size: A4 landscape !important;
            margin: 8mm;
        }

        @media print {
            body {
                width: 297mm;
                height: 210mm;
                margin: 0;
                padding: 0;
                font-size: 11px;
            }

            /* Ajustement pour s'assurer que tout le contenu tient sur une page */
            #print {
                transform: scale(0.82);
                transform-origin: top left;
                width: 100%;
                height: 100%;
            }

            /* Réduction des espaces pour optimiser l'espace */
            .card {
                margin-bottom: 6px !important;
            }

            .card-header {
                padding: 4px 8px !important;
            }

            .card-body {
                padding: 6px !important;
            }

            table {
                font-size: 9px !important;
                margin: 3px auto !important;
            }

            th, td {
                padding: 2px !important;
                font-size: 8px !important;
                line-height: 1.1 !important;
            }

            .form-group {
                margin-bottom: 3px !important;
            }

            h4, h5 {
                font-size: 11px !important;
                margin: 3px 0 !important;
            }

            /* Optimisation des moyennes annuelles */
            .row {
                margin: 0 !important;
            }

            .col-md-4 {
                padding: 1px !important;
            }

            /* Réduction des marges des commentaires */
            div[style*="margin-top: 10px"] {
                margin-top: 4px !important;
            }

            div[style*="margin-top: 5px"] {
                margin-top: 2px !important;
            }

            /* Optimisation du header */
            table[width="100%"] {
                margin-bottom: 5px !important;
            }

            /* Réduction de la taille du logo */
            img[style*="max-height : 100px"] {
                max-height: 70px !important;
            }

            /* Optimisation des signatures */
            div[style*="display: flex"] {
                margin-top: 3px !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div id="print" xmlns:margin-top="http://www.w3.org/1999/xhtml">
        {{-- Logo et détails de l'école --}}
        <table width="100%">
            <tr>
                <td><img src="{{ $s['logo'] }}" style="max-height : 100px;"></td>

                <td style="text-align: center; ">
                    <strong><span style="color: #1b0c80; font-size: 25px;">{{ strtoupper(Qs::getSetting('system_name')) }}</span></strong><br/>
                   {{-- <strong><span style="color: #1b0c80; font-size: 20px;">MINNA, NIGER STATE</span></strong><br/>--}}
                    <strong><span
                                style="color: #000; font-size: 15px;"><i>{{ ucwords($s['address']) }}</i></span></strong><br/>
                    <strong><span style="color: #000; font-size: 15px;"> BULLETIN DE NOTES {{ '('.strtoupper($class_type->name).')' }}
                    </span></strong>
                </td>
                <td style="width: 100px; height: 100px; float: left;">
                  {{-- <img src="{{ $sr->user->photo }}"
                         alt="..."  width="100" height="100">--}} 
                </td>
            </tr>
        </table>
        {{-- Logo de fond (filigrane) --}}
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; z-index: -1; pointer-events: none;">
            <img src="{{ $s['logo'] }}"
                 style="max-width: 80%; max-height: 80%; opacity: 0.25;" />
        </div>

        {{-- <!-- LE DOCUMENT COMMENCE ICI--> --}}
        @include('pages.support_team.marks.print.sheet')

        {{-- Clé de notation --}}
        {{-- @include('pages.support_team.marks.print.grading') --}}

        {{-- TRAITS - PSYCHOMOTEURS ET AFFECTIFS --}}
        @include('pages.support_team.marks.print.skills')

        <div style="margin-top: 5px; clear: both;"></div>

        {{-- Les commentaires du directeur sont maintenant intégrés dans sheet.blade.php --}}

    </div>
</div>

<script>
    window.print();
</script>
</body>
</html>
