<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordre de Paiement - {{ $decaissement->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .content {
            margin: 20px 0;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        td {
            padding: 8px;
            border: 1px solid #000;
        }
        .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ORDRE DE PAIEMENT</h1>
        <p>N° OP-{{ date('Y') }}-{{ str_pad($decaissement->id, 4, '0', STR_PAD_LEFT) }}</p>
        <p>Date: {{ $decaissement->date_paiement->format('d/m/Y') }}</p>
    </div>

    <div class="content">
        <table>
            <tr>
                <td class="label">Bénéficiaire</td>
                <td>{{ $decaissement->beneficiaire }}</td>
            </tr>
            <tr>
                <td class="label">Motif</td>
                <td>{{ $decaissement->motif }}</td>
            </tr>
            <tr>
                <td class="label">Description</td>
                <td>{{ $decaissement->description ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Mode de paiement</td>
                <td>{{ ucfirst(str_replace('_', ' ', $decaissement->methode_paiement)) }}</td>
            </tr>
            <tr>
                <td class="label">Référence</td>
                <td>{{ $decaissement->reference ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="amount">
            Montant: {{ number_format($decaissement->montant, 0, ',', ' ') }} Ar
        </div>

        <p><strong>Créé par:</strong> {{ $decaissement->user->name ?? 'N/A' }}</p>
        <p><strong>Date de création:</strong> {{ $decaissement->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Statut:</strong> {{ ucfirst($decaissement->status) }}</p>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>