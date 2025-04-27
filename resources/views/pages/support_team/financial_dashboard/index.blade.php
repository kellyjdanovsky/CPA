@extends('layouts.master')
@section('page_title', 'Tableau de Bord Financier')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card fade-in">
            <div class="card-header bg-white header-elements-inline">
                <h5 class="card-title">
                    <i class="icon-stats-bars2 mr-2 text-primary"></i> Tableau de Bord Financier
                </h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                        <a class="list-icons-item" data-action="fullscreen"></a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Navigation entre les onglets -->
                <ul class="nav nav-tabs nav-tabs-highlight mb-4">
                    <li class="nav-item">
                        <a href="{{ route('financial_dashboard.index') }}" class="nav-link active">
                            <i class="icon-stats-bars2 mr-2"></i> Tableau de Bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('financial_dashboard.expenses') }}" class="nav-link">
                            <i class="icon-cash4 mr-2"></i> Dépenses
                        </a>
                    </li>
                </ul>

                <!-- Filtres -->
                <form action="{{ route('financial_dashboard.filter') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="period">Période</label>
                                <select name="period" id="period" class="form-control select" onchange="updateDateFields()">
                                    <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Aujourd'hui</option>
                                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Cette année</option>
                                    <option value="custom" {{ !in_array($period, ['day', 'week', 'month', 'year']) ? 'selected' : '' }}>Personnalisé</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="start_date">Date début</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="end_date">Date fin</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="class_id">Classe</label>
                                <select name="class_id" id="class_id" class="form-control select">
                                    <option value="">Toutes les classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payment_type">Type de paiement</label>
                                <select name="payment_type" id="payment_type" class="form-control select">
                                    <option value="">Tous les types</option>
                                    @foreach($paymentTypes as $type)
                                        <option value="{{ $type->id }}" {{ $paymentType == $type->id ? 'selected' : '' }}>{{ $type->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="icon-filter3 mr-2"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Alertes -->
                @if(count($alerts) > 0)
                    <div class="mb-4">
                        @foreach($alerts as $alert)
                            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show">
                                <i class="{{ $alert['icon'] }} mr-2"></i>
                                {{ $alert['message'] }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Indicateurs clés -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ number_format($cashBalance, 0, ',', ' ') }} Ar</h3>
                                        <span class="text-uppercase font-size-xs">Solde de trésorerie</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-cash3 icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ number_format($avgMonthlyRevenue, 0, ',', ' ') }} Ar</h3>
                                        <span class="text-uppercase font-size-xs">Moyenne mensuelle recettes</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-stats-growth icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ number_format($avgMonthlyExpenses, 0, ',', ' ') }} Ar</h3>
                                        <span class="text-uppercase font-size-xs">Moyenne mensuelle dépenses</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-stats-decline icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ number_format($recoveryRate, 1) }}%</h3>
                                        <span class="text-uppercase font-size-xs">Taux de recouvrement</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-percent icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="row">
                    <!-- Graphique en barres: évolution mois par mois des recettes et dépenses -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Évolution des recettes et dépenses</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="monthly-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagramme circulaire: répartition par catégories -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Répartition des recettes</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="revenue-pie-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Graphique en courbes: suivi de la trésorerie -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Suivi de la trésorerie</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="cash-flow-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte thermique: visualisation des retards de paiement par classe -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Retards de paiement par classe</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="late-payments-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'export -->
                <!-- Tendances annuelles -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Tendances annuelles des recettes</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="yearly-trends-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prévisions de trésorerie -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Prévisions de trésorerie</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="forecast-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'export -->
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('financial_dashboard.export.excel') }}?start_date={{ $startDate }}&end_date={{ $endDate }}&class_id={{ $classId }}&payment_type={{ $paymentType }}" class="btn btn-success">
                            <i class="icon-file-excel mr-2"></i> Exporter en Excel
                        </a>
                        <a href="{{ route('financial_dashboard.export.pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}&class_id={{ $classId }}&payment_type={{ $paymentType }}" class="btn btn-danger ml-2">
                            <i class="icon-file-pdf mr-2"></i> Exporter en PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
<script src="{{ asset('global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/visualization/c3/c3.min.js') }}"></script>

<script>
    $(function() {
        // Fonction pour mettre à jour les champs de date en fonction de la période sélectionnée
        window.updateDateFields = function() {
            var period = $('#period').val();
            var today = new Date();
            var startDate = new Date();
            var endDate = new Date();

            if (period === 'day') {
                // Aujourd'hui
                startDate = today;
                endDate = today;
            } else if (period === 'week') {
                // Cette semaine
                startDate = new Date(today.setDate(today.getDate() - today.getDay()));
                endDate = new Date(new Date().setDate(startDate.getDate() + 6));
            } else if (period === 'month') {
                // Ce mois
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (period === 'year') {
                // Cette année
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
            } else if (period === 'custom') {
                // Ne rien faire, laisser les champs tels quels
                return;
            }

            // Formater les dates au format YYYY-MM-DD
            $('#start_date').val(formatDate(startDate));
            $('#end_date').val(formatDate(endDate));
        }

        // Fonction pour formater une date au format YYYY-MM-DD
        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Graphique en barres: évolution mois par mois des recettes et dépenses
        var monthlyChart = c3.generate({
            bindto: '#monthly-chart',
            data: {
                columns: [
                    ['Recettes', @foreach($monthlyData as $data) {{ $data['revenue'] }}, @endforeach],
                    ['Dépenses', @foreach($monthlyData as $data) {{ $data['expenses'] }}, @endforeach]
                ],
                types: {
                    Recettes: 'bar',
                    Dépenses: 'bar'
                },
                colors: {
                    Recettes: '#26A69A',
                    Dépenses: '#EF5350'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [@foreach($monthlyData as $data) '{{ $data['label'] }}', @endforeach]
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            bar: {
                width: {
                    ratio: 0.5
                }
            }
        });

        // Diagramme circulaire: répartition des recettes par catégorie
        var revenuePieChart = c3.generate({
            bindto: '#revenue-pie-chart',
            data: {
                columns: [
                    @foreach($revenueByCategory as $category => $amount)
                    ['{{ $category }}', {{ $amount }}],
                    @endforeach
                ],
                type: 'pie',
                colors: {
                    @php $colors = ['#26A69A', '#5C6BC0', '#FF7043', '#EC407A', '#AB47BC', '#7E57C2', '#42A5F5', '#26C6DA']; $i = 0; @endphp
                    @foreach($revenueByCategory as $category => $amount)
                    '{{ $category }}': '{{ $colors[$i % count($colors)] }}',
                    @php $i++; @endphp
                    @endforeach
                }
            },
            pie: {
                label: {
                    format: function(value, ratio, id) {
                        return id + ': ' + (ratio * 100).toFixed(1) + '%';
                    }
                }
            }
        });

        // Graphique en courbes: suivi de la trésorerie
        var cashFlowChart = c3.generate({
            bindto: '#cash-flow-chart',
            data: {
                columns: [
                    ['Trésorerie', @foreach($monthlyData as $data) {{ $data['revenue'] - $data['expenses'] }}, @endforeach]
                ],
                type: 'area',
                colors: {
                    Trésorerie: '#5C6BC0'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [@foreach($monthlyData as $data) '{{ $data['label'] }}', @endforeach]
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            }
        });

        // Carte thermique: visualisation des retards de paiement par classe
        var latePaymentsChart = c3.generate({
            bindto: '#late-payments-chart',
            data: {
                columns: [
                    ['Retards de paiement', @foreach($latePaymentsByClass as $class => $count) {{ $count }}, @endforeach]
                ],
                type: 'bar',
                colors: {
                    'Retards de paiement': function(d) {
                        // Couleur en fonction du nombre de retards
                        if (d.value > 10) return '#EF5350'; // Rouge pour beaucoup de retards
                        if (d.value > 5) return '#FF7043'; // Orange pour retards moyens
                        if (d.value > 0) return '#FFCA28'; // Jaune pour peu de retards
                        return '#66BB6A'; // Vert pour aucun retard
                    }
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [@foreach($latePaymentsByClass as $class => $count) '{{ $class }}', @endforeach]
                },
                y: {
                    tick: {
                        format: function(d) { return d; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            bar: {
                width: {
                    ratio: 0.5
                }
            }
        });

        // Graphique de tendances annuelles
        var yearlyTrendsChart = c3.generate({
            bindto: '#yearly-trends-chart',
            data: {
                columns: [
                    @foreach($yearlyTrends as $yearData)
                    ['{{ $yearData['year'] }}', {{ implode(', ', $yearData['revenues']) }}],
                    @endforeach
                ],
                type: 'line'
            },
            axis: {
                x: {
                    type: 'category',
                    categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            point: {
                r: 4
            },
            legend: {
                position: 'right'
            }
        });

        // Graphique de prévisions de trésorerie
        var forecastChart = c3.generate({
            bindto: '#forecast-chart',
            data: {
                columns: [
                    ['Historique',
                        @foreach($cashFlowForecast['historical'] as $month => $value)
                            {{ $value }},
                        @endforeach
                        null, null, null
                    ],
                    ['Prévision',
                        @foreach($cashFlowForecast['historical'] as $month => $value)
                            null,
                        @endforeach
                        @foreach($cashFlowForecast['forecast'] as $month => $value)
                            {{ $value }},
                        @endforeach
                    ]
                ],
                types: {
                    Historique: 'area',
                    Prévision: 'area-spline'
                },
                colors: {
                    Historique: '#5C6BC0',
                    Prévision: '#FF7043'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: [
                        @foreach($cashFlowForecast['historical'] as $month => $value)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}',
                        @endforeach
                        @foreach($cashFlowForecast['forecast'] as $month => $value)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}',
                        @endforeach
                    ]
                },
                y: {
                    tick: {
                        format: function(d) { return d.toLocaleString('fr-FR') + ' Ar'; }
                    }
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            regions: [
                {axis: 'x', start: {{ count($cashFlowForecast['historical']) - 0.5 }}, end: {{ count($cashFlowForecast['historical']) + count($cashFlowForecast['forecast']) }}, class: 'forecast-region'}
            ]
        });
    });
</script>

<style>
    .forecast-region {
        fill: rgba(249, 247, 237, 0.5) !important;
    }
    .c3-line {
        stroke-width: 3px;
    }
    .fade-in {
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection
