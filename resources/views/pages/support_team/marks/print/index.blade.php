<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de notes de l'élève - {{ $sr->user->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --white: #fff;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 297mm; /* A4 landscape width */
            min-height: 210mm; /* A4 landscape height */
            background: var(--white);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 10mm;
            box-sizing: border-box;
        }

        #print {
            width: 100%;
            height: 100%;
        }

        .bulletin-header {
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 4px solid var(--primary-color);
            padding-bottom: 2px;
            margin-bottom: 5px;
            gap: 15px;
        }

        .school-logo img {
            max-height: 70px;
        }

        .school-info {
            text-align: center;
        }

        .school-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .school-address {
            font-size: 14px;
            color: var(--secondary-color);
            margin: 2px 0;
        }

        .bulletin-title {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .student-photo img {
            width: 100px;
            height: 100px;
            border: 3px solid var(--border-color);
            border-radius: 8px;
        }

        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            background-color: var(--light-gray);
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .detail-item strong {
            color: var(--primary-color);
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .marks-table th, .marks-table td {
            padding: 10px 12px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .marks-table thead th {
            background-color: var(--primary-color);
            color: var(--white);
            font-weight: 700;
            font-size: 16px;
        }

        .marks-table tbody tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        .marks-table .subject-name {
            text-align: left;
            font-weight: 700;
        }

        .marks-table .grade {
            font-weight: 700;
            color: var(--primary-color);
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .summary-item {
            background-color: var(--light-gray);
            padding: 8px;
            border-radius: 8px;
            text-align: center;
        }

        .summary-item h4 {
            font-size: 12px;
            color: var(--primary-color);
            margin: 0 0 2px 0;
        }

        .summary-item p {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: var(--dark-gray);
        }

        .footer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid var(--border-color);
        }

        .comments h4, .signatures h4 {
            font-size: 18px;
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 10px;
        }

        .comments p {
            margin: 0;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature-line {
            width: 30%;
            border-bottom: 1px solid var(--secondary-color);
            text-align: center;
            padding-bottom: 5px;
            color: var(--secondary-color);
        }

        .no-print {
            text-align: center;
            margin-top: 30px;
        }

        .no-print button {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .no-print button:hover {
            background-color: #0056b3;
        }

        @media print {
            body {
                background-color: var(--white);
                padding: 0;
                margin: 0;
            }

            .container {
                width: 100%;
                min-height: initial;
                box-shadow: none;
                padding: 10mm;
            }

            @page {
                size: A4 landscape;
                margin: 0;
            }

            .no-print {
                display: none;
            }

            .marks-table {
                font-size: 12px;
            }

            .marks-table th, .marks-table td {
                padding: 8px 10px;
            }

            .summary-section {
                gap: 15px;
            }

            .summary-item {
                padding: 15px;
            }

            .summary-item p {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="print">
            <div class="bulletin-header">
                <div class="school-logo">
                    <img src="{{ $s['logo'] }}" alt="School Logo">
                </div>
                <div class="school-info">
                    <h1 class="school-title">{{ strtoupper(Qs::getSetting('system_name')) }}</h1>
                    <p class="school-address">{{ ucwords($s['address']) }}</p>
                    <h2 class="bulletin-title">BULLETIN DE NOTES ({{ strtoupper($class_type->name) }})</h2>
                </div>
                
            </div>

            @include('pages.support_team.marks.print.sheet')

        </div>
    </div>

    <script>
        function printBulletin() {
            window.print();
        }
        // Automatically trigger print dialog
        window.onload = function() {
            setTimeout(printBulletin, 1000); // Delay to ensure content loads
        };
    </script>
</body>
</html>
