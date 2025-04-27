@extends('layouts.master')
@section('page_title', 'Gestion des Dépenses')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card fade-in">
            <div class="card-header bg-white header-elements-inline">
                <h5 class="card-title">
                    <i class="icon-cash4 mr-2 text-danger"></i> Gestion des Dépenses
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
                        <a href="{{ route('financial_dashboard.index') }}" class="nav-link">
                            <i class="icon-stats-bars2 mr-2"></i> Tableau de Bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('financial_dashboard.expenses') }}" class="nav-link active">
                            <i class="icon-cash4 mr-2"></i> Dépenses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('decaissements.index') }}" class="nav-link">
                            <i class="icon-list3 mr-2"></i> Gérer les Dépenses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('decaissements.create') }}" class="nav-link">
                            <i class="icon-plus2 mr-2"></i> Nouvelle Dépense
                        </a>
                    </li>
                </ul>

                <!-- Filtres -->
                <form action="{{ route('financial_dashboard.expenses.filter') }}" method="GET" class="mb-4">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="category">Catégorie</label>
                                <select name="category" id="category" class="form-control select">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ $categoryFilter == $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-filter3 mr-2"></i> Filtrer
                                </button>
                                <a href="{{ route('financial_dashboard.expenses') }}" class="btn btn-light ml-2">
                                    <i class="icon-reset mr-2"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Indicateurs clés -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ number_format($totalExpenses, 0, ',', ' ') }} Ar</h3>
                                        <span class="text-uppercase font-size-xs">Total des dépenses</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-cash4 icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ count($expensesByCategory) }}</h3>
                                        <span class="text-uppercase font-size-xs">Catégories de dépenses</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-list3 icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="mb-0">{{ $expenses->count() }}</h3>
                                        <span class="text-uppercase font-size-xs">Transactions</span>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="icon-calculator3 icon-3x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="row">
                    <!-- Graphique en barres: évolution mois par mois des dépenses -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Évolution des dépenses</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="monthly-expenses-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagramme circulaire: répartition par catégories -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Répartition des dépenses</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <div class="chart" id="expenses-pie-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des dépenses -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title">Liste des dépenses</h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($expenses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Catégorie</th>
                                                    <th>Description</th>
                                                    <th>Montant</th>
                                                    <th>Bénéficiaire</th>
                                                    <th>Référence</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($expenses as $expense)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($expense->date_paiement)->format('d/m/Y') }}</td>
                                                        <td>{{ $expense->motif }}</td>
                                                        <td>{{ $expense->description ?? 'N/A' }}</td>
                                                        <td class="text-right font-weight-bold">{{ number_format($expense->montant, 0, ',', ' ') }} Ar</td>
                                                        <td>{{ $expense->beneficiaire ?? 'N/A' }}</td>
                                                        <td>{{ $expense->reference ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary">
                                                    <th colspan="3" class="text-right">Total:</th>
                                                    <th class="text-right">{{ number_format($totalExpenses, 0, ',', ' ') }} Ar</th>
                                                    <th colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        Aucune dépense trouvée pour la période sélectionnée.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'export -->
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <a href="#" class="btn btn-success">
                            <i class="icon-file-excel mr-2"></i> Exporter en Excel
                        </a>
                        <a href="#" class="btn btn-danger ml-2">
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

        // Graphique en barres: évolution mois par mois des dépenses
        var monthlyExpensesChart = c3.generate({
            bindto: '#monthly-expenses-chart',
            data: {
                columns: [
                    ['Dépenses', @foreach($monthlyData as $data) {{ $data['expenses'] }}, @endforeach]
                ],
                types: {
                    Dépenses: 'bar'
                },
                colors: {
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

        // Diagramme circulaire: répartition des dépenses par catégorie
        var expensesPieChart = c3.generate({
            bindto: '#expenses-pie-chart',
            data: {
                columns: [
                    @foreach($expensesByCategory as $category => $amount)
                    ['{{ $category }}', {{ $amount }}],
                    @endforeach
                ],
                type: 'pie',
                colors: {
                    @php $colors = ['#EF5350', '#EC407A', '#AB47BC', '#7E57C2', '#5C6BC0', '#42A5F5', '#26C6DA', '#26A69A']; $i = 0; @endphp
                    @foreach($expensesByCategory as $category => $amount)
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
    });
</script>
@endsection
