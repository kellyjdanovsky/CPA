@extends('layouts.master')
@section('page_title', 'Gérer les paiements')
@php
    use Illuminate\Support\Str;
@endphp
@section('content')

    <div class="card fade-in">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title"><i class="icon-cash2 mr-2 text-primary"></i> Choix de l'année scolaire</h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('payments.select_year') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="year" class="col-form-label font-weight-bold">Choisir l'année scolaire <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar"></i></span>
                                        </div>
                                        <select data-placeholder="Choisir l'année" required id="year" name="year" class="form-control select">
                                            @foreach($years as $yr)
                                                <option {{ ($selected && $year == $yr->year) ? 'selected' : '' }} value="{{ $yr->year }}">{{ $yr->year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mt-4">
                                <div class="text-right mt-1">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="icon-search4 mr-2"></i> Afficher
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@if($selected)
    <div class="card fade-in mt-3">
        <div class="card-header bg-white header-elements-inline">
            <h5 class="card-title">
                <i class="icon-coins mr-2 text-success"></i> Paiements pour l'année {{ $year }}
            </h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-payments" class="nav-link active" data-toggle="tab"><i class="icon-grid mr-2"></i> Toutes les Classes</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="icon-filter3 mr-2"></i> Par Classe</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_classes as $mc)
                            <a href="#pc-{{ $mc->id }}" class="dropdown-item" data-toggle="tab">{{ $mc->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-payments">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Montant</th>
                            <th>Ref_No</th>
                            <th>Classe</th>
                            <th>Méthode</th>
                            <th>Info</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="font-weight-semibold text-primary">{{ $p->title }}</span></td>
                                <td><span class="badge badge-success">{{ number_format($p->amount, 0, ',', ' ') }} Ar</span></td>
                                <td><span class="badge badge-info">{{ $p->ref_no }}</span></td>
                                <td>{{ $p->my_class_id ? $p->my_class->name : '' }}</td>
                                <td>
                                    @php
                                        $methodClass = 'badge-secondary';
                                        if(strtolower($p->method) == 'cash') {
                                            $methodClass = 'badge-success';
                                        } elseif(strtolower($p->method) == 'card') {
                                            $methodClass = 'badge-info';
                                        } elseif(strtolower($p->method) == 'adra') {
                                            $methodClass = 'badge-primary';
                                        }
                                    @endphp
                                    <span class="badge {{ $methodClass }}">{{ ucwords($p->method) }}</span>
                                </td>
                                <td>
                                    @if($p->description)
                                        <span class="text-muted" data-toggle="tooltip" title="{{ $p->description }}">
                                            {{ Str::limit($p->description, 20) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="list-icons action-buttons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right">
                                                {{-- Modifier --}}
                                                <a href="{{ route('payments.edit', $p->id) }}" class="dropdown-item"><i class="icon-pencil text-info"></i> Modifier</a>
                                                {{-- Supprimer --}}
                                                <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash text-danger"></i> Supprimer</a>
                                                <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('payments.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach($my_classes as $mc)
                    <div class="tab-pane fade" id="pc-{{ $mc->id }}">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Montant</th>
                                <th>Ref_No</th>
                                <th>Classe</th>
                                <th>Méthode</th>
                                <th>Info</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments->where('my_class_id', $mc->id) as $p)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="font-weight-semibold text-primary">{{ $p->title }}</span></td>
                                    <td><span class="badge badge-success">{{ number_format($p->amount, 0, ',', ' ') }} Ar</span></td>
                                    <td><span class="badge badge-info">{{ $p->ref_no }}</span></td>
                                    <td>{{ $p->my_class_id ? $p->my_class->name : '' }}</td>
                                    <td>
                                        @php
                                            $methodClass = 'badge-secondary';
                                            if(strtolower($p->method) == 'cash') {
                                                $methodClass = 'badge-success';
                                            } elseif(strtolower($p->method) == 'card') {
                                                $methodClass = 'badge-info';
                                            } elseif(strtolower($p->method) == 'adra') {
                                                $methodClass = 'badge-primary';
                                            }
                                        @endphp
                                        <span class="badge {{ $methodClass }}">{{ ucwords($p->method) }}</span>
                                    </td>
                                    <td>
                                        @if($p->description)
                                            <span class="text-muted" data-toggle="tooltip" title="{{ $p->description }}">
                                                {{ Str::limit($p->description, 20) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="list-icons action-buttons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-right">
                                                    {{-- Modifier --}}
                                                    <a href="{{ route('payments.edit', $p->id) }}" class="dropdown-item"><i class="icon-pencil text-info"></i> Modifier</a>
                                                    {{-- Supprimer --}}
                                                    <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash text-danger"></i> Supprimer</a>
                                                    <form method="post" id="item-delete-{{ $p->id }}" action="{{ route('payments.destroy', $p->id) }}" class="hidden">@csrf @method('delete')</form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

    {{--Liste des paiements terminée--}}

@endsection
