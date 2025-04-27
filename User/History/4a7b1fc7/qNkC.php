@extends('layouts.master')
@section('page_title', 'Verification')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2"></i>Verification</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                    <tr>
                        <th>NOM</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pr as $p)
                    <tr>
                        <td>
                            @php
                            // Retrieve the student's name associated with the payment record
                            // $studentRecord = App\Models\StudentRecord::where('id', $p->student_id)->where('my_class_id',$class)->first();
                            $studentRecord = App\Models\StudentRecord::where('my_class_id', $class)->where('id',$p->student_id)->first();

                            $user = $studentRecord ? $studentRecord->user->name : null; // Access user name if student record exists
                            @endphp
                            {{ $user ?? '-' }}
                        </td>
                        <td>
                            @if($p->paid)
                            paid
                            @else
                            unpaid
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>

 

@endsection
