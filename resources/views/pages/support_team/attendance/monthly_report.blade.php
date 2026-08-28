@extends('layouts.master')
@section('page_title', 'Rapport Mensuel - ' . $my_class->name . ' - ' . date('m/Y', strtotime($month_year)))

@section('content')

@if(isset($students))
<div class="card">
    <div class="card-header header-elements-inline bg-dark">
        <h6 class="card-title font-weight-bold">Rapport Mensuel : {{ $my_class->name }} | Mois : {{ date('m/Y', strtotime($month_year)) }}</h6>
        <div class="header-elements">
            <a href="{{ route('attendance.print-monthly', ['my_class_id' => $my_class->id, 'month_year' => $month_year]) }}" target="_blank" class="btn btn-light btn-sm"><i class="icon-printer mr-2"></i> Imprimer</a>
            <a href="{{ route('attendance.export-monthly', ['my_class_id' => $my_class->id, 'month_year' => $month_year]) }}" class="btn btn-success btn-sm ml-2"><i class="icon-file-excel mr-2"></i> Export Excel</a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center text-nowrap">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-left align-middle">Nom de l'élève</th>
                        <th colspan="{{ $days_in_month }}">Jours du mois</th>
                        <th colspan="5" class="bg-light">Synthèse</th>
                    </tr>
                    <tr>
                        @for($i = 1; $i <= $days_in_month; $i++)
                            <th class="p-1">{{ $i }}</th>
                        @endfor
                        <th class="bg-success text-white p-1" title="Présent">P</th>
                        <th class="bg-danger text-white p-1" title="Absent">A</th>
                        <th class="bg-warning text-white p-1" title="Retard">R</th>
                        <th class="bg-info text-white p-1" title="Excusé">E</th>
                        <th class="bg-dark text-white p-1">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $sr)
                    @php 
                        $student_att = $attendances->where('student_id', $sr->user_id);
                        $p = $student_att->where('status', 'present')->count();
                        $a = $student_att->where('status', 'absent')->count();
                        $r = $student_att->where('status', 'retard')->count();
                        $e = $student_att->where('status', 'excuse')->count();
                        $total = $student_att->count();
                        $taux = $total > 0 ? round(($p / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="text-left font-weight-bold">
                            <a href="{{ route('attendance.student-report', \App\Helpers\Qs::hash($sr->user_id)) }}">{{ $sr->user->name }}</a>
                        </td>
                        @for($i = 1; $i <= $days_in_month; $i++)
                            @php 
                                $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                $day_att = $student_att->where('date', $date_str)->first();
                                $status_badge = '-';
                                $status_class = '';
                                
                                if($day_att) {
                                    if($day_att->status == 'present') { $status_badge = 'P'; $status_class = 'text-success font-weight-bold'; }
                                    elseif($day_att->status == 'absent') { $status_badge = 'A'; $status_class = 'text-danger font-weight-bold'; }
                                    elseif($day_att->status == 'retard') { $status_badge = 'R'; $status_class = 'text-warning font-weight-bold'; }
                                    elseif($day_att->status == 'excuse') { $status_badge = 'E'; $status_class = 'text-info font-weight-bold'; }
                                }
                            @endphp
                            <td class="{{ $status_class }} p-1">{{ $status_badge }}</td>
                        @endfor
                        <td class="font-weight-bold text-success">{{ $p }}</td>
                        <td class="font-weight-bold text-danger">{{ $a }}</td>
                        <td class="font-weight-bold text-warning">{{ $r }}</td>
                        <td class="font-weight-bold text-info">{{ $e }}</td>
                        <td class="font-weight-bold bg-light">{{ $taux }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
