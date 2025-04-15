<!DOCTYPE html>
<html>
<head>
    <title>Reçu_decaissement_{{ $dec->ref }}</title>
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
        <div>{{ $dec->ref }}</div>
    </div>


    <div class="payment-info">
        <div><strong>Piece de caise Décaissement</strong></div>
        <div><strong>RÉFÉRENCE:</strong> {{ $dec->ref }}</div>
        <div><strong>Motif:</strong>   {{ $dec->motif }} </div>
        <div><strong>Montant:</strong>   {{ $dec->montant }}  Ariary </div>
        <div><strong>Date:</strong>   {{ date('d/m/Y', strtotime($dec->created_at)) }} session {{ $dec->annee }}  </div>
    </div>


    <div class="footer">

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
