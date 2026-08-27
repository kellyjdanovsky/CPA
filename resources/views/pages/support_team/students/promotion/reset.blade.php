@extends('layouts.master')
@section('page_title', 'Gérer les promotions')
@section('content')

    {{-- Statistiques de Promotion --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Statistiques des Promotions - Session {{ $old_year }} → {{ $new_year }}</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-arrow-up8 icon-3x opacity-75"></i>
                                        </div>
                                        <div class="flex-grow-1 text-right">
                                            <h3 class="mb-0">{{ $stats['reinscrit'] }}</h3>
                                            <span class="text-uppercase font-size-xs font-weight-semibold">Réinscrits</span>
                                            <div class="font-size-sm opacity-75">{{ $stats['reinscrit_percent'] }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-reload-alt icon-3x opacity-75"></i>
                                        </div>
                                        <div class="flex-grow-1 text-right">
                                            <h3 class="mb-0">{{ $stats['redouble'] }}</h3>
                                            <span class="text-uppercase font-size-xs font-weight-semibold">Redoublants</span>
                                            <div class="font-size-sm opacity-75">{{ $stats['redouble_percent'] }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-graduation icon-3x opacity-75"></i>
                                        </div>
                                        <div class="flex-grow-1 text-right">
                                            <h3 class="mb-0">{{ $stats['quitte'] }}</h3>
                                            <span class="text-uppercase font-size-xs font-weight-semibold">Quittés</span>
                                            <div class="font-size-sm opacity-75">{{ $stats['quitte_percent'] }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-users icon-3x opacity-75"></i>
                                        </div>
                                        <div class="flex-grow-1 text-right">
                                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                            <span class="text-uppercase font-size-xs font-weight-semibold">Total</span>
                                            <div class="font-size-sm opacity-75">100%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Graphique des Statistiques --}}
                    @if($stats['total'] > 0)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Répartition des Promotions</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="promotionChart" width="300" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Détails par Statut</h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="icon-arrow-up8 text-success mr-2"></i>
                                                <strong>Réinscrits (Promus)</strong>
                                                <br><small class="text-muted">Élèves passés à la classe supérieure</small>
                                            </div>
                                            <span class="badge badge-success badge-pill">{{ $stats['reinscrit'] }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="icon-reload-alt text-warning mr-2"></i>
                                                <strong>Redoublants</strong>
                                                <br><small class="text-muted">Élèves restés dans la même classe</small>
                                            </div>
                                            <span class="badge badge-warning badge-pill">{{ $stats['redouble'] }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="icon-graduation text-danger mr-2"></i>
                                                <strong>Quittés (Diplômés)</strong>
                                                <br><small class="text-muted">Élèves ayant terminé leur cursus</small>
                                            </div>
                                            <span class="badge badge-danger badge-pill">{{ $stats['quitte'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{--Réinitialiser tout--}}
    <div class="card">
        <div class="card-body text-center">
            <button id="promotion-reset-all" class="btn btn-danger btn-large">Réinitialiser toutes les promotions pour la session</button>
            <button id="graduated-reset-all" class="btn btn-warning btn-large ml-2">Réinitialiser tous les diplômés</button>
            <button id="reset-all-combined" class="btn btn-dark btn-large ml-2">Réinitialiser tout (promotions + diplômés)</button>
        </div>
    </div>

{{-- Réinitialiser les promotions --}}
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title font-weight-bold">Gérer les promotions - Étudiants qui ont été promus de la session <span class="text-danger">{{ $old_year }}</span> à la session <span class="text-success">{{ $new_year }}</span></h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">

            <table id="promotions-list" class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>De la classe</th>
                    <th>À la classe</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($promotions->sortBy('fc.name')->sortBy('student.name') as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $p->student->photo }}" alt="photo"></td>
                        <td>{{ $p->student->name }}</td>
                        <td>{{ $p->fc->name.' '.$p->fs->name }}</td>
                        <td>{{ $p->tc->name.' '.$p->ts->name }}</td>
                        @if($p->status === 'P')
                            <td><span class="text-success">Promu</span></td>
                        @elseif($p->status === 'D')
                            <td><span class="text-danger">Non promu</span></td>
                        @else
                            <td><span class="text-primary">Diplômé</span></td>
                        @endif
                        <td class="text-center">
                            <button data-id="{{ $p->id }}" class="btn btn-danger promotion-reset">Réinitialiser</button>
                            <form id="promotion-reset-{{ $p->id }}" method="post" action="{{ route('students.promotion_reset', $p->id) }}">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

{{-- Étudiants diplômés --}}
@if($graduated_students && $graduated_students->count() > 0)
    <div class="card mt-4">
        <div class="card-header header-elements-inline">
            <h5 class="card-title font-weight-bold">Étudiants diplômés - Session <span class="text-danger">{{ $old_year }}</span></h5>
            <span class="badge badge-danger">{{ $graduated_students->count() }} élève(s)</span>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <table id="graduated-list" class="table datatable-button-html5-columns">
                <thead>
                <tr>
                    <th>S/N</th>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Classe</th>
                    <th>Section</th>
                    <th>Date de diplôme</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($graduated_students->sortBy('my_class.name')->sortBy('user.name') as $gs)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $gs->user->photo }}" alt="photo"></td>
                        <td>{{ $gs->user->name }}</td>
                        <td>{{ $gs->my_class->name }}</td>
                        <td>{{ $gs->section->name }}</td>
                        <td>{{ $gs->grad_date }}</td>
                        <td class="text-center">
                            <button data-id="{{ $gs->user_id }}" class="btn btn-warning graduated-reset">Réinitialiser</button>
                            <form id="graduated-reset-{{ $gs->user_id }}" method="post" action="{{ route('students.promotion_reset_graduated', $gs->user_id) }}">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
    <script>
        /* Graphique des promotions */
        @if($stats['total'] > 0)
        const ctx = document.getElementById('promotionChart').getContext('2d');
        const promotionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Réinscrits', 'Redoublants', 'Quittés'],
                datasets: [{
                    data: [{{ $stats['reinscrit'] }}, {{ $stats['redouble'] }}, {{ $stats['quitte'] }}],
                    backgroundColor: [
                        '#28a745', // Vert pour réinscrits
                        '#ffc107', // Orange pour redoublants
                        '#dc3545'  // Rouge pour quittés
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = {{ $stats['total'] }};
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif

        /* Réinitialiser individuellement */
        $('.promotion-reset').on('click', function () {
            let pid = $(this).data('id');
            if (confirm('Êtes-vous sûr de vouloir continuer ?')){
                $('form#promotion-reset-'+pid).submit();
            }
            return false;
        });

        /* Réinitialiser toutes les promotions */
        $('#promotion-reset-all').on('click', function () {
            if (confirm('Êtes-vous sûr de vouloir continuer ?')){
                $.ajax({
                    url:"{{ route('students.promotion_reset_all') }}",
                    type:'DELETE',
                    data:{ '_token' : $('#csrf-token').attr('content') },
                    success:function (resp) {
                        $('table#promotions-list > tbody').fadeOut().remove();
                        flash({msg : resp.msg, type : 'success'});

                        // Recharger la page pour mettre à jour les statistiques
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                })
            }
            return false;
        });

        /* Réinitialiser tous les diplômés */
        $('#graduated-reset-all').on('click', function () {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les élèves diplômés ?')){
                $.ajax({
                    url:"{{ route('students.promotion_reset_all_graduated') }}",
                    type:'DELETE',
                    data:{ '_token' : $('#csrf-token').attr('content') },
                    success:function (resp) {
                        $('table#graduated-list > tbody').fadeOut().remove();
                        flash({msg : resp.msg, type : 'success'});

                        // Recharger la page pour mettre à jour les statistiques
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                })
            }
            return false;
        });

        /* Réinitialiser tout (promotions + diplômés) */
        $('#reset-all-combined').on('click', function () {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les promotions ET tous les diplômés ? Cette action est irréversible !')){
                // D'abord réinitialiser les promotions
                $.ajax({
                    url:"{{ route('students.promotion_reset_all') }}",
                    type:'DELETE',
                    data:{ '_token' : $('#csrf-token').attr('content') },
                    success:function (resp) {
                        flash({msg : 'Promotions réinitialisées avec succès', type : 'success'});
                        
                        // Ensuite réinitialiser les diplômés
                        $.ajax({
                            url:"{{ route('students.promotion_reset_all_graduated') }}",
                            type:'DELETE',
                            data:{ '_token' : $('#csrf-token').attr('content') },
                            success:function (resp) {
                                flash({msg : 'Diplômés réinitialisés avec succès', type : 'success'});

                                // Recharger la page pour mettre à jour les statistiques
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        });
                    }
                });
            }
            return false;
        });

        /* Réinitialiser individuellement les diplômés */
        $('.graduated-reset').on('click', function () {
            let sid = $(this).data('id');
            if (confirm('Êtes-vous sûr de vouloir réinitialiser cet élève diplômé ?')){
                $('form#graduated-reset-'+sid).submit();
            }
            return false;
        });
    </script>
@endsection
