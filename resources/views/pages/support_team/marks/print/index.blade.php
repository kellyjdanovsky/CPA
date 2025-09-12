<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                font-size: 0.75vw; /* Using relative units for better scaling */
            }

            /* Dynamic scaling to fit content on a single page */
            #print {
                transform: scale(0.85); /* Adjusted scale for better fit */
                transform-origin: top left;
                width: 100%;
                height: 100%;
            }

            /* Réduction des espaces pour optimiser l'espace */
            .card {
                margin-bottom: 0.5vh !important;
            }

            .card-header {
                padding: 0.3vh 0.5vw !important;
            }

            .card-body {
                padding: 0.5vh !important;
            }

            table {
                font-size: 0.7vw !important;
                margin: 0.2vh auto !important;
            }

            th, td {
                padding: 0.2vh 0.3vw !important;
                font-size: 0.65vw !important;
                line-height: 1.1 !important;
            }

            .form-group {
                margin-bottom: 0.2vh !important;
            }

            h4, h5 {
                font-size: 0.8vw !important;
                margin: 0.2vh 0 !important;
            }

            /* Optimisation des moyennes annuelles */
            .row {
                margin: 0 !important;
            }

            .col-md-4 {
                padding: 0.1vh !important;
            }

            /* Réduction des marges des commentaires */
            div[style*="margin-top: 10px"] {
                margin-top: 0.5vh !important;
            }

            div[style*="margin-top: 5px"] {
                margin-top: 0.2vh !important;
            }

            /* Optimisation du header */
            table[width="100%"] {
                margin-bottom: 0.3vh !important;
            }

            /* Réduction de la taille du logo */
            img[style*="max-height : 100px"] {
                max-height: 5vh !important;
            }

            /* Optimisation des signatures */
            div[style*="display: flex"] {
                margin-top: 0.3vh !important;
            }
            
            /* Ensure content fits within page boundaries */
            #print {
                max-width: 100vw;
                max-height: 100vh;
                overflow: hidden;
            }
        }
        
        /* Screen view improvements */
        @media screen {
            body {
                font-size: 11px;
                background: #f8f9fa;
                padding: 20px;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .container {
                max-width: 1200px;
                width: 100%;
            }
            
            #print {
                background: white;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                border-radius: 8px;
                overflow: hidden;
                transform: scale(0.9);
                transform-origin: center center;
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
                    <strong><span style="color: #1b0c80; font-size: 2vw;">{{ strtoupper(Qs::getSetting('system_name')) }}</span></strong><br/>
                   {{-- <strong><span style="color: #1b0c80; font-size: 20px;">MINNA, NIGER STATE</span></strong><br/>--}}
                    <strong><span
                                style="color: #000; font-size: 1.2vw;"><i>{{ ucwords($s['address']) }}</i></span></strong><br/>
                    <strong><span style="color: #000; font-size: 1.2vw;"> BULLETIN DE NOTES {{ '('.strtoupper($class_type->name).')' }}
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
    // Automatically print when the page loads, but only in print mode
    if (window.matchMedia && window.matchMedia('print').matches) {
        window.print();
    }
    
    // Also provide a function for manual printing
    function printBulletin() {
        window.print();
    }
</script>
</body>
</html>