@extends('layouts.master')
@section('page_title', 'Appel - ' . $my_class->name . ' - ' . date('d/m/Y', strtotime($date)))

@section('content')
<div class="card">
    <div class="card-header header-elements-inline bg-dark">
        <h6 class="card-title font-weight-bold">Feuille de présence : {{ $my_class->name }} | Date : {{ date('d/m/Y', strtotime($date)) }} | Période : {{ ucfirst($period) }}</h6>
        <div class="header-elements">
            <button class="btn btn-success btn-sm mr-2" onclick="checkAll('present')">Tous Présents</button>
            <button class="btn btn-danger btn-sm" onclick="checkAll('absent')">Tous Absents</button>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <input type="hidden" name="my_class_id" value="{{ $my_class->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="period" value="{{ $period }}">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Nom complet</th>
                            <th>N° Matricule</th>
                            <th class="text-center text-success">Présent</th>
                            <th class="text-center text-danger">Absent</th>
                            <th class="text-center text-warning">Retard</th>
                            <th class="text-center text-info">Excusé</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $sr)
                        @php 
                            $att = $existing_records->get($sr->user_id);
                            $status = $att ? $att->status : 'present';
                            $obs = $att ? $att->observations : '';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><img class="rounded-circle" style="height: 35px; width: 35px;" src="{{ $sr->user->photo_url ?? asset('global_assets/images/user.png') }}" alt="photo"></td>
                            <td>{{ $sr->user->name }}</td>
                            <td>{{ $sr->adm_no }}</td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $sr->user_id }}]" value="present" class="att-radio radio-present" {{ $status == 'present' ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $sr->user_id }}]" value="absent" class="att-radio radio-absent" {{ $status == 'absent' ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $sr->user_id }}]" value="retard" class="att-radio radio-retard" {{ $status == 'retard' ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $sr->user_id }}]" value="excuse" class="att-radio radio-excuse" {{ $status == 'excuse' ? 'checked' : '' }}>
                            </td>
                            <td>
                                <input type="text" name="observations[{{ $sr->user_id }}]" class="form-control form-control-sm" value="{{ $obs }}" placeholder="Observation éventuelle">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary btn-lg">Enregistrer les présences <i class="icon-floppy-disk ml-2"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
    function checkAll(status) {
        document.querySelectorAll('.radio-' + status).forEach(function(radio) {
            radio.checked = true;
        });
    }
</script>
@endsection
