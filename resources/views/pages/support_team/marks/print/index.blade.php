<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de notes de l'élève - {{ $sr->user->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --light-gray: #f8f9fa;
            --dark-gray: #2c3e50;
            --white: #fff;
            --border-color: #bdc3c7;
        }

        body {
            font-family: 'Poppins', 'Roboto', sans-serif;
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
            padding-bottom: 15px;
            margin-bottom: 20px;
            gap: 15px;
        }

        .school-logo img {
            max-height: 70px;
        }

        .school-info {
            text-align: center;
        }

        .school-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        .school-address {
            font-size: 14px;
            color: var(--secondary-color);
            margin: 2px 0;
        }

        .bulletin-title {
            font-size: 20px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
            letter-spacing: 0.5px;
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
            gap: 8px;
            background-color: var(--light-gray);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .detail-item strong {
            color: var(--primary-color);
            font-weight: 600;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background: var(--white);
            margin-bottom: 20px;
        }

        .marks-table th, .marks-table td {
            padding: 12px 10px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .marks-table thead th {
            background-color: var(--primary-color);
            color: var(--white);
            font-weight: 600;
            font-size: 14px;
        }

        .marks-table tbody tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        .marks-table .subject-name {
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
        }

        .marks-table .grade {
            font-weight: 600;
            color: #27ae60;
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            background-color: var(--light-gray);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-top: 3px solid var(--primary-color);
        }

        .summary-item h4 {
            font-size: 13px;
            color: var(--primary-color);
            margin: 0 0 8px 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid var(--border-color);
        }

        .comments h4, .signatures h4 {
            font-size: 16px;
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 12px;
            font-weight: 600;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary-color);
        }

        .comments p {
            margin: 0 0 8px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature-line {
            width: 30%;
            border-bottom: 2px solid var(--border-color);
            text-align: center;
            padding-bottom: 8px;
            color: var(--secondary-color);
            font-size: 13px;
            font-weight: 500;
        }

        .no-print {
            text-align: center;
            margin-top: 30px;
        }

        .no-print button {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
        }

        .no-print button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.4);
        }

        @media print {
            body * {
                color: #000 !important;
                background: transparent !important;
            }
            
            body {
                background-color: #fff;
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
                margin: 1cm;
            }

            .no-print {
                display: none;
            }

            .bulletin-header {
                border-bottom: 3px solid #000 !important;
            }

            .marks-table {
                font-size: 11px;
            }

            .marks-table th, .marks-table td {
                padding: 8px 6px;
                border: 1px solid #000 !important;
            }

            .marks-table thead th {
                background: #000 !important;
                color: #fff !important;
            }

            .marks-table tbody tr:nth-child(even) {
                background: #f5f5f5 !important;
            }

            .student-details-grid {
                background: #f5f5f5 !important;
            }

            .summary-item {
                border-top: 3px solid #000 !important;
            }

            .comments h4, .signatures h4 {
                border-bottom: 2px solid #000 !important;
            }

            .signature-line {
                border-bottom: 2px solid #000 !important;
            }

            .summary-section {
                gap: 10px;
            }

            .summary-item {
                padding: 12px;
            }

            .summary-item p {
                font-size: 16px;
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
