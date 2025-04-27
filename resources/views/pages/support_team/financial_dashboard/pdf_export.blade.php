<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tableau de Bord Financier</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18pt;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header p {
            font-size: 10pt;
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 14pt;
            margin: 0 0 10px 0;
            padding: 5px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #E2EFDA;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .indicators {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .indicator {
            width: 23%;
            margin-right: 2%;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .indicator h3 {
            font-size: 12pt;
            margin: 0 0 5px 0;
            color: #333;
        }
        .indicator p {
            font-size: 14pt;
            margin: 0;
            font-weight: bold;
            color: #4472C4;
        }
        .alert {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .page-break {
            page-break-after: always;
        }
        .col-6 {
            width: 48%;
            float: left;
            margin-right: 2%;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TABLEAU DE BORD FINANCIER</h1>
            <p>Année scolaire: {{ $year }}</p>
            <p>Période: {{ $startDateCarbon->format('d/m/Y') }} - {{ $endDateCarbon->format('d/m/Y') }}</p>
            @if($selectedClass)
                <p>Classe: {{ $selectedClass->name }}</p>
            @endif
            @if($selectedPaymentType)
                <p>Type de paiement: {{ $selectedPaymentType->title }}</p>
            @endif
        </div>
        
        <!-- Alertes -->
        @if(count($alerts) > 0)
            <div class="section">
                <h2>Alertes</h2>
                @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }}">
                        {{ $alert['message'] }}
                    </div>
                @endforeach
            </div>
        @endif
        
        <!-- Indicateurs clés -->
        <div class="section">
            <h2>Indicateurs Clés</h2>
            <table>
                <tr>
                    <th>Indicateur</th>
                    <th class="text-right">Valeur</th>
                </tr>
                <tr>
                    <td>Solde de trésorerie</td>
                    <td class="text-right">{{ number_format($cashBalance, 0, ',', ' ') }} Ar</td>
                </tr>
                <tr>
                    <td>Moyenne mensuelle recettes</td>
                    <td class="text-right">{{ number_format($avgMonthlyRevenue, 0, ',', ' ') }} Ar</td>
                </tr>
                <tr>
                    <td>Moyenne mensuelle dépenses</td>
                    <td class="text-right">{{ number_format($avgMonthlyExpenses, 0, ',', ' ') }} Ar</td>
                </tr>
                <tr>
                    <td>Taux de recouvrement</td>
                    <td class="text-right">{{ number_format($recoveryRate, 1) }}%</td>
                </tr>
            </table>
        </div>
        
        <!-- Évolution mensuelle des recettes et dépenses -->
        <div class="section">
            <h2>Évolution Mensuelle des Recettes et Dépenses</h2>
            <table>
                <tr>
                    <th>Mois</th>
                    <th class="text-right">Recettes</th>
                    <th class="text-right">Dépenses</th>
                    <th class="text-right">Solde</th>
                </tr>
                @foreach($monthlyData as $data)
                    <tr>
                        <td>{{ $data['label'] }}</td>
                        <td class="text-right">{{ number_format($data['revenue'], 0, ',', ' ') }} Ar</td>
                        <td class="text-right">{{ number_format($data['expenses'], 0, ',', ' ') }} Ar</td>
                        <td class="text-right">{{ number_format($data['revenue'] - $data['expenses'], 0, ',', ' ') }} Ar</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="text-right">{{ number_format($totalRevenue, 0, ',', ' ') }} Ar</td>
                    <td class="text-right">{{ number_format($totalExpenses, 0, ',', ' ') }} Ar</td>
                    <td class="text-right">{{ number_format($cashBalance, 0, ',', ' ') }} Ar</td>
                </tr>
            </table>
        </div>
        
        <div class="page-break"></div>
        
        <div class="clearfix">
            <!-- Répartition des recettes par catégorie -->
            <div class="col-6">
                <div class="section">
                    <h2>Répartition des Recettes par Catégorie</h2>
                    <table>
                        <tr>
                            <th>Catégorie</th>
                            <th class="text-right">Montant</th>
                            <th class="text-right">Pourcentage</th>
                        </tr>
                        @foreach($revenueByCategory as $category => $amount)
                            <tr>
                                <td>{{ $category }}</td>
                                <td class="text-right">{{ number_format($amount, 0, ',', ' ') }} Ar</td>
                                <td class="text-right">{{ number_format(($amount / $totalRevenue) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td class="text-right">{{ number_format($totalRevenue, 0, ',', ' ') }} Ar</td>
                            <td class="text-right">100%</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Répartition des dépenses par catégorie -->
            <div class="col-6">
                <div class="section">
                    <h2>Répartition des Dépenses par Catégorie</h2>
                    <table>
                        <tr>
                            <th>Catégorie</th>
                            <th class="text-right">Montant</th>
                            <th class="text-right">Pourcentage</th>
                        </tr>
                        @if(count($expensesByCategory) > 0)
                            @foreach($expensesByCategory as $category => $amount)
                                <tr>
                                    <td>{{ $category }}</td>
                                    <td class="text-right">{{ number_format($amount, 0, ',', ' ') }} Ar</td>
                                    <td class="text-right">{{ number_format(($amount / $totalExpenses) * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td>TOTAL</td>
                                <td class="text-right">{{ number_format($totalExpenses, 0, ',', ' ') }} Ar</td>
                                <td class="text-right">100%</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="3" class="text-center">Aucune dépense enregistrée</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        
        <div class="clearfix">
            <!-- Recettes par classe -->
            <div class="col-6">
                <div class="section">
                    <h2>Recettes par Classe</h2>
                    <table>
                        <tr>
                            <th>Classe</th>
                            <th class="text-right">Montant</th>
                            <th class="text-right">Pourcentage</th>
                        </tr>
                        @foreach($revenueByClass as $class => $amount)
                            <tr>
                                <td>{{ $class }}</td>
                                <td class="text-right">{{ number_format($amount, 0, ',', ' ') }} Ar</td>
                                <td class="text-right">{{ number_format(($amount / $totalRevenue) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td class="text-right">{{ number_format($totalRevenue, 0, ',', ' ') }} Ar</td>
                            <td class="text-right">100%</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Retards de paiement par classe -->
            <div class="col-6">
                <div class="section">
                    <h2>Retards de Paiement par Classe</h2>
                    <table>
                        <tr>
                            <th>Classe</th>
                            <th class="text-right">Nombre d'élèves</th>
                        </tr>
                        @php $totalLatePayments = 0; @endphp
                        @foreach($latePaymentsByClass as $class => $count)
                            <tr>
                                <td>{{ $class }}</td>
                                <td class="text-right">{{ $count }}</td>
                            </tr>
                            @php $totalLatePayments += $count; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td class="text-right">{{ $totalLatePayments }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p class="text-center">Rapport généré le {{ date('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
