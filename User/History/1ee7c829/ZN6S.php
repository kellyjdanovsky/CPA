@extends('layouts.master')
@section('page_title', 'Gestion Caisse')
@section('content')
<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
<style>

    .dec {
        height: 84px;
    }
</style>

    <div class="content">

        <div id="ajax-alert" style="display: none"></div>


        <div class="row">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-body bg-blue-400 has-bg-image">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0 solde money" data-solde={{$solde}} >{{ number_format($solde, 2, ',', ' ') }} Ar</h3>

                            <span class="text-uppercase font-size-xs font-weight-bold">Solde</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="fa fa-camera-retro"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-body bg-danger-400 has-bg-image">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0 money">{{ number_format($decM, 2, ',', ' ') }} Ar</h3>
                            <span class="text-uppercase font-size-xs">Decaissement</span>
                        </div>

                        <div class="ml-3 align-self-center">
                           {{--<i class="icon-users2 icon-3x opacity-75"></i>--}}
                        </div>
                    </div>
                </div>
            </div>

        <!-- Add the button here -->
    <button type="button" class="dec btn btn-warning mt-2" data-toggle="modal" data-target="#decaissementModal">
       Faire une décaissement
    </button>
        </div>

    </div>


    <div class="card">

        <div id="ajax-alert" style="display: none"></div>

        <!-- Display Encaissement data in a table -->
        <div class="card-header header-elements-inline">
            <h3 class="card-title">Liste encaissement</h3>
            {!! Qs::getPanelOptions() !!}
        </div>
        <div class="card-body">
            <table class="table datatable-button-html5-columns">

                <thead>
                    <tr>
                        <th>Motif</th>
                    
                        <th>Montant</th>
                        <th>Session</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($encaissement as $payment)
                    @if ($payment->amt_paid)
                    <tr>
                        <td>{{ $payment->payment->title . ' ' . $payment->student->name }}</td>
                        @php
                           // dd($payment->student);
                        @endphp

                        <td>{{$payment->amt_paid }}</td>
                        <td>{{ $payment->year }}</td>
                        <td>{{  date('d/m/Y', strtotime($payment->updated_at))}}</td>
                    </tr>

                    @endif

                    @endforeach
                </tbody>
            </table>

        </div>
    <p></p><p></p><p></p><p></p><p></p><p></p><p></p>
    <div class="card">
    <div id="ajax-alert" style="display: none"></div>

    <!-- Display Encaissement data in a table -->
    <div class="card-header header-elements-inline">
        <h3 class="card-title">Liste Decaissement</h3>
        {!! Qs::getPanelOptions() !!}
    </div>
    <div class="card-body">
        <table class="table datatable-button-html5-columns">
                <thead>
                    <tr>
                        <th>Motif</th>
                        <th>Montant</th>
                        <th>Session</th>
                        <th>Date</th>
                        <th>Piece justificatif</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($decaissement as $payment)

                    <tr>
                        <td>{{ $payment->motif}}</td>
                        <td>{{$payment->montant }}</td>
                        <td>{{ $payment->annee }}</td>
                        <td>{{ $payment->created_at }}</td>
                        <td>@if ($payment->piece)
                            <img style="width: 50px" src="{{ asset('images/' . $payment->piece) }}" alt="Piece justificatif">
                        @else
                           Pas de piece
                        @endif</td>
                       @php
                           $data_created_at = date('Y/m/d', strtotime($payment->created_at));
                       @endphp

                      <td><a href=""

                        data-id="{{ $payment->ref }}"
                        data-motif="{{ $payment->motif }}"
                        data-montant="{{ $payment->montant }}"
                        data-annee="{{ $payment->annee }}"
                        data-created_at="{{ $data_created_at }}"

                        class="btn btn-primary recu-button" target="_blank">Recu</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>


    <!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="decaissementModal" tabindex="-1" role="dialog" aria-labelledby="decaissementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="decaissementModalLabel">Creation Decaissement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add the form for creating Decaissement -->
                <form method="POST" action="{{ route('payments.decaissement') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="motif">Motif:</label>
                        <input type="text" class="form-control" id="motif" name="motif" required>
                    </div>
                    <div class="form-group">
                        <label for="montant">Montant:</label>
                        <input type="number" class="form-control  dcm" min="10" max="" id="montant" name="montant" required>
                    </div>
                    <div class="form-group">
                        <label for="montant">Piece justifitcatif:</label>
                        <input type="file" class="form-control" id="piece" name="piece" >
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Decaisser</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>

                    </div>
                    <p style="color: brown">* decaissement doit etre inferieur au solde svp</p>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Get the value of the 'solde' class
        var soldeValue = parseInt($(".solde").data('solde').text());
        

        // Set the 'max' attribute of the 'dcm' class input
        $(".dcm").attr("max", soldeValue);
    });
</script>

<script src="{{ asset('js/simple.money.format.js') }}" defer></script>
<script>
$(document).ready(function() {
	$('.money').simpleMoneyFormat();

});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const recuButtons = document.querySelectorAll(".recu-button");

        recuButtons.forEach(function (button) {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                const id = this.getAttribute("data-id");
                const motif = this.getAttribute("data-motif");
                const montant = this.getAttribute("data-montant");
                const annee = this.getAttribute("data-annee");
                const created_at = this.getAttribute("data-created_at");
                const caissier = this.getAttribute("data-caissier");


                // Create a new window with dynamic content
                const printWindow = window.open("", "", "width=230,height=600");
                printWindow.document.open();
                printWindow.document.write(`<html><head><title>Reçu_decaissement_${id}</title></head><body>`);
                printWindow.document.write(`<h3>Cololege adventiste Avaratetezana</h3>`);
                printWindow.document.write(`<div><strong>Piece de caise Décaissement</strong></div>`);
                printWindow.document.write(`<div><strong>RÉFÉRENCE:</strong> ${id}</div>`);
                printWindow.document.write(`<div><strong>Motif:</strong> ${motif}</div>`);
                printWindow.document.write(`<div><strong>Montant:</strong> ${montant} Ariary</div>`);
                printWindow.document.write(`<div><strong>Date:</strong> ${created_at} session ${annee}</div>`);
                printWindow.document.write(`</body></html>`);
                printWindow.document.close();

                // Trigger the print dialog for the new window
                printWindow.print();

            });
        });
    });
</script>



@endsection
