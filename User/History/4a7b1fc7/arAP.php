@extends('layouts.master')
@section('page_title', 'Verification ' . $nom_payment->title . ' | Classe ' . $nom_classe->name)
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2"></i>Verification {{$nom_payment->title}} | Classe {{$nom_classe->name}}</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <table class="table datatable-button-html5-columns">
                <thead>
                    <tr>
                        <th>NOM</th>
                        <th>STATUS</th>
                        <th>STATUS</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $s)
                    <tr>
                        <td>
                            {{$s->user->name}}
                        </td>
                        <td>
                            @php
                                $pay = App\Models\PaymentRecord::where('student_id', $s->user_id)
                                ->where('payment_id',$id_pay)
                                ->first();
                            @endphp
                            @if($pay->paid)
                            paid
                            @else
                            unpaid
                            @endif
                        </td>
                        <td>
                            {{$s->user->name}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>

 

@endsection
