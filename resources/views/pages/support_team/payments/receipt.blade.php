<!DOCTYPE html>
<html>
<head>
    <title>Reçu_{{ $pr->ref_no.'_'.$sr->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 58mm;
            margin: 0 auto;
            padding: 5px;
        }
        .header {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-info {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .student-info {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .payment-info {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .payment-table {
            font-size: 10px;
            width: 100%;
            border-collapse: collapse;
        }
        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
        }
        .sign {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
        }
        .cut-line {
            border-top: 1px dashed #000;
            margin-top: 65px;
            height: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        {{ strtoupper(Qs::getSetting('system_name')) }}
    </div>

    <div class="receipt-info">
        <div><strong>Numéro de référence du reçu:</strong></div>
        <div>{{ $pr->ref_no }}</div>
    </div>

    <div class="student-info">
        <div><strong>NOM:</strong> {{ $sr->user->name }}</div>
        <div><strong>ADM_NO:</strong> {{ $sr->adm_no }}</div>
        <div><strong>CLASSE:</strong> {{ $sr->my_class->name }}</div>
    </div>

    <div class="payment-info">
        <div><strong>RÉFÉRENCE:</strong> {{ $payment->ref_no }}</div>
        <div><strong>TITRE:</strong> {{ $payment->title }}</div>
        <div><strong>MONTANT:</strong> {{ $payment->amount }} Ariary</div>
        <div><strong>DESCRIPTION:</strong> {{ $payment->description }}</div>
    </div>

    <div class="payment-table">
        <div><strong>HiSTORIQUE DU PAIEMENT</strong></div>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Montant</th>
                <th>Solde</th>
                <th>Méthode</th>
            </tr>
            </thead>
            <tbody>
            @foreach($receipts as $r)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($r->created_at)) }}</td>
                    <td>{{ $r->amt_paid }} Ar</td>
                    <td>{{ $r->balance }} Ar</td>
                    <td>{{ $r->methode }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        {{ $pr->paid ? 'ÉTAT DU PAIEMENT' : 'TOTAL DÛ en Ariary' }} {{ $pr->paid ? 'ACQUITTÉ' : $pr->balance }}
    </div>

    <div class="sign">
         caissier(e) Mr/Mlle/Mme {{ Auth::user()->name }}
    </div>
    <div class="cut-line"></div> <!-- Add this line for cutting -->
</div>
<script>
    window.print();
</script>
</body>
</html>
