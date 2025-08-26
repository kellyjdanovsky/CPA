<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notes Pondérées - {{ $my_class->name }} {{ $section->name }} - {{ $ex->name }}</title>
    
    @php
        use App\Helpers\NumberFormat;
    @endphp
    
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .class-info {
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th, .table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }
        
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .student-name-cell {
            text-align: left;
            min-width: 120px;
        }
        
        .subject-grade-cell {
            min-width: 60px;
        }
        
        .ranking-cell {
            font-weight: bold;
            min-width: 40px;
        }
        
        .total-points-cell {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .average-cell {
            font-weight: bold;
            font-size: 13px;
        }
        
        .mention-cell {
            font-weight: bold;
            min-width: 80px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .legend {
            margin-top: 20px;
            font-size: 11px;
        }
        
        .legend h4 {
            margin: 10px 0 5px 0;
            font-size: 13px;
        }
        
        .legend ul {
            margin: 0;
            padding-left: 15px;
        }
        
        .legend li {
            margin-bottom: 2px;
        }
        
        @media print {
            body {
                font-size: 10px;
                padding: 10px;
            }
            
            .table th, .table td {
                padding: 4px 2px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ Qs::getSetting('system_name') }}</div>
        <div class="report-title">FEUILLE DE NOTES PONDÉRÉES AVEC COEFFICIENTS</div>
        <div class="class-info">
            Classe: {{ $my_class->name }} {{ $section->name }} | 
            Examen: {{ $ex->name }} | 
            Session: {{ $year }}
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Rang</th>
                <th>Étudiant</th>
                @foreach($subjects as $subject)
                    <th title="Coefficient: {{ $subject->coef }}">
                        {{ $subject->name }}<br><small>(Coef {{ $subject->coef }})</small>
                    </th>
                @endforeach
                <th>Total Points</th>
                <th>Moyenne</th>
                <th>Mention</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weighted_results as $result)
                <tr>
                    <td class="ranking-cell">{{ $result['rank'] }}</td>
                    <td class="student-name-cell">{{ $result['student_name'] }}</td>
                    @foreach($result['subject_marks'] as $subject_mark)
                        <td class="subject-grade-cell">
                            @if($subject_mark['weighted_mark'] !== null)
                                {{ NumberFormat::formatWithoutRounding($subject_mark['weighted_mark'], 2) }}
                            @else
                                N/A
                            @endif
                        </td>
                    @endforeach
                    <td class="total-points-cell">{{ NumberFormat::formatWithoutRounding($result['total_points'], 2) }}</td>
                    <td class="average-cell">{{ NumberFormat::formatWithoutRounding($result['average'], 2) }}</td>
                    <td class="mention-cell">{{ $result['mention'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="legend">
        <h4>Légende:</h4>
        <p><strong>Mentions:</strong> Très Bien (≥16) | Bien (≥14) | Assez Bien (≥12) | Passable (≥10) | Insuffisant (&lt;10)</p>
        <p><strong>Notes:</strong> Toutes les notes sont affichées avec 2 chiffres après la virgule</p>
        <p><strong>Total Points:</strong> Somme de toutes les notes de matières de l'étudiant</p>
        <p><strong>Moyenne:</strong> Total Points ÷ Somme des Coefficients</p>
        <p><strong>Date d'impression:</strong> {{ date('d/m/Y H:i') }}</p>
    </div>
    
    <div class="footer">
        Document généré par {{ Auth::user()->name }} le {{ date('d/m/Y à H:i') }}
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>