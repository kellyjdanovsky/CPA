@extends('layouts.master')
@section('page_title', 'Bulletin de notes de l\'élève')
@section('content')

    <div class="card">
        <div class="card-header text-center">
            <h4 class="card-title font-weight-bold">Bulletin de notes de l'élève =>  {{ $sr->user->name.' ('.$my_class->name.' '.$my_class->section->first()->name.')' }} </h4>
        </div>
    </div>

    @if(isset($director_comment) && !empty($director_comment))
    <div class="card">
        <div class="card-body">
            <h5 class="font-weight-bold">Commentaire du Directeur (Annuel) :</h5>
            <p class="text-muted"><em>{{ $director_comment }}</em></p>
        </div>
    </div>
    @endif

    @foreach($exams as $ex)
        @foreach($exam_records->where('exam_id', $ex->id) as $exr)

                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h6 class="font-weight-bold">{{ $ex->name.' - '.$ex->year }}</h6>
                        {!! Qs::getPanelOptions() !!}
                    </div>

                    <div class="card-body collapse show">

                        {{--Tableau du bulletin--}}
                        @include('pages.support_team.marks.show.sheet')

                        {{--Bouton d'impression--}}
                        <div class="text-center mt-3">
                            <a target="_blank" href="{{ route('marks.print', [Qs::hash($student_id), $ex->id, $year]) }}" class="btn btn-secondary btn-lg">Imprimer le bulletin <i class="icon-printer ml-2"></i></a>
                        </div>

                    </div>

                </div>

            {{--Commentaires de l'examen--}}

            @include('pages.support_team.marks.show.comments')

            {{--Évaluation des compétences--}}
            @include('pages.support_team.marks.show.skills')

        @endforeach
    @endforeach

    {{-- Global CSS for improved report card design --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        .bulletin-container {
            font-family: 'Poppins', 'Roboto', sans-serif;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .bulletin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #3498db;
        }
        
        .school-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }
        
        .exam-title {
            font-size: 20px;
            font-weight: 600;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .student-details {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 0;
        }
        
        .detail-item {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #3498db;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .detail-item strong {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 13px;
            background: white;
        }
        
        .marks-table th {
            background: #2c3e50;
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            border: none;
        }
        
        .marks-table td {
            padding: 12px 8px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .marks-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .marks-table tbody tr:hover {
            background-color: #e3f2fd;
        }
        
        .subject-name {
            text-align: left !important;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .grade {
            font-weight: 600;
            color: #27ae60;
            font-size: 14px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .summary-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid #3498db;
        }
        
        .summary-item h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-item p {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .footer-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 20px;
            background: #ffffff;
            border-top: 2px solid #e9ecef;
        }
        
        .comments, .signatures {
            padding: 0;
        }
        
        .comments h4, .signatures h4 {
            color: #2c3e50;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .comments p {
            margin: 0 0 10px 0;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .signature-line {
            height: 50px;
            border-bottom: 2px solid #bdc3c7;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 13px;
            font-weight: 500;
        }
        
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .no-print button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .no-print button:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        @media print {
            body * {
                color: #000 !important;
                background: transparent !important;
            }
            
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            
            .bulletin-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .bulletin-header {
                background: #000 !important;
                border-bottom: 2px solid #000 !important;
            }
            
            .marks-table th {
                background: #000 !important;
                color: #fff !important;
            }
            
            .marks-table tbody tr:nth-child(even) {
                background: #f5f5f5 !important;
            }
            
            .detail-item {
                border-left: 3px solid #000 !important;
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
            
            .no-print {
                display: none;
            }
            
            .marks-table {
                font-size: 11px;
            }
            
            .marks-table th, .marks-table td {
                padding: 8px 5px;
            }
            
            .subject-name, .grade {
                font-size: 12px;
            }
        }
        
        @media (max-width: 768px) {
            .student-details-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-section {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .footer-section {
                grid-template-columns: 1fr;
            }
            
            .marks-table {
                font-size: 11px;
            }
            
            .marks-table th, .marks-table td {
                padding: 8px 4px;
            }
        }
    </style>

@endsection
