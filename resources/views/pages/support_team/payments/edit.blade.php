@extends('layouts.master')
@section('page_title', 'Modifier le paiement')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Modifier le paiement</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-update" method="post" action="{{ route('payments.update', $payment->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Titre <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="title" value="{{ $payment->title }}" required type="text" class="form-control" placeholder="Par exemple, Frais scolaires">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Classe </label>
                            <div class="col-lg-9">
                                <input class="form-control" title="Classe" disabled value="{{ $payment->my_class_id ? $payment->my_class->name : 'Toutes les classes' }}" type="text">
                            </div>
                        </div>

                        <div class="form-group row " style="display: none">
                            <label for="method" class="col-lg-3 col-form-label font-weight-semibold">Méthode de paiement</label>
                            <div class="col-lg-9">
                                <input title="méthode" value="{{ ucwords($payment->method) }}" disabled class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="amount" class="col-lg-3 col-form-label font-weight-semibold">Montant (<del style="text-decoration-style: double">N</del>) </label>
                            <div class="col-lg-9">
                                <input disabled class="form-control" value="{{ $payment->amount }}" id="amount" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-lg-3 col-form-label font-weight-semibold">Description</label>
                            <div class="col-lg-9">
                                <input class="form-control" value="{{ $payment->description }}" name="description" id="description" type="text">
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Valider le formulaire <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--Fin de la modification du paiement--}}

@endsection
