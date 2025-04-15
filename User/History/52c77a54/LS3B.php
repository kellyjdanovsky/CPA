<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de notes de l'élève - {{ $sr->user->name }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/my_print.css') }}" />
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
        }

        .page {
            width: 50%; /* Divide the page into two halves */
            height: 100vh; /* Full height of the viewport */
            box-sizing: border-box;
            overflow: hidden;
            page-break-before: always;
        }

        @page {
            size: A4 landscape; /* Set the page size to A4 landscape */
            margin: 0;
        }

        #print {
            box-sizing: border-box;
            padding: 15px; /* Adjust padding as needed */
        }

        /* Additional styling for the content as needed */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="page">
        <div id="print">
            {{-- Logo et détails de l'école --}}
            <table width="100%">
                <!-- ... -->
            </table>
            <br/>

            {{-- Logo de fond --}}
            <div style="position: relative; text-align: center;">
                <img src="{{ $s['logo'] }}"
                     style="max-width: 500px; max-height:600px; margin-top: 60px; position:absolute ; opacity: 0.2; margin-left: auto;margin-right: auto; left: 0; right: 0;" />
            </div>

            {{-- <!-- LE DOCUMENT COMMENCE ICI--> --}}
            @include('pages.support_team.marks.print.sheet')
        </div>
    </div>

    <div class="page">
        <!-- Add content for the back side of the page if needed -->
    </div>
</div>

<script>
    window.print();
</script>
</body>
</html>

