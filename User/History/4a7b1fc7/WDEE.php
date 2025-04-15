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
                 
                </tbody>
            </table>
            
        </div>
    </div>

 

@endsection
