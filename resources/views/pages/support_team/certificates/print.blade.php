@extends('layouts.print')
@section('title', 'Certificat - ' . $certificate->reference_no)

@section('content')
<style>
    @media print {
        @page { size: A4 portrait; margin: 20mm; }
        body { font-family: 'Times New Roman', serif; }
    }
    .cert-container {
        border: 4px double #333;
        padding: 40px;
        margin: 0 auto;
        max-width: 800px;
        min-height: 1000px;
        position: relative;
    }
    .cert-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .cert-logo {
        width: 100px;
        margin-bottom: 10px;
    }
    .cert-school-name {
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 5px 0;
    }
    .cert-address {
        font-size: 14px;
        margin-bottom: 20px;
    }
    .cert-ref {
        text-align: right;
        font-size: 12px;
        margin-bottom: 40px;
    }
    .cert-title {
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 50px;
        text-transform: uppercase;
        font-family: 'Georgia', serif;
    }
    .cert-body {
        font-size: 18px;
        line-height: 2;
        text-align: justify;
        margin-bottom: 60px;
    }
    .cert-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 80px;
    }
    .cert-signature-box {
        text-align: center;
        width: 300px;
    }
</style>

<div class="cert-container">
    <div class="cert-ref">
        Réf: {{ $certificate->reference_no }}<br>
        Année scolaire: {{ $certificate->academic_year }}
    </div>

    <div class="cert-header">
        <h1 class="cert-school-name">COLLÈGE PRIVÉ ADVENTISTE</h1>
        <h2 class="cert-school-name" style="font-size: 20px;">AVARATETEZANA</h2>
        <div class="cert-address">
            BP XXX, Antsirabe, Madagascar<br>
            Tél: +261 XX XX XXX XX
        </div>
    </div>

    @php
        $typesTitle = [
            'scolarite' => 'CERTIFICAT DE SCOLARITÉ',
            'frequentation' => 'CERTIFICAT DE FRÉQUENTATION',
            'reussite' => 'ATTESTATION DE RÉUSSITE',
            'fin_etudes' => 'CERTIFICAT DE FIN D\'ÉTUDES',
            'paiement' => 'ATTESTATION DE PAIEMENT',
            'transfert' => 'LETTRE DE TRANSFERT',
        ];
        
        $nom = strtoupper($certificate->student->name ?? 'NOM DE L\'ÉLÈVE');
        $dob = '...'; // In a real app: date('d/m/Y', strtotime($certificate->student->dob))
        $classe = '...'; // Real app: $certificate->student->student_record->my_class->name
    @endphp

    <div class="cert-title">
        {{ $typesTitle[$certificate->type] ?? 'CERTIFICAT' }}
    </div>

    <div class="cert-body">
        @if($certificate->type == 'scolarite')
            Je soussigné, Le Directeur du Collège Privé Adventiste Avaratetezana certifie que l'élève <strong>{{ $nom }}</strong>, 
            né(e) le <strong>{{ $dob }}</strong> est régulièrement inscrit(e) en classe de <strong>{{ $classe }}</strong> 
            pour l'année scolaire <strong>{{ $certificate->academic_year }}</strong>.
            <br><br>
            Le présent certificat est délivré pour servir et valoir ce que de droit.
            
        @elseif($certificate->type == 'frequentation')
            Je soussigné, Le Directeur du Collège Privé Adventiste Avaratetezana certifie que l'élève <strong>{{ $nom }}</strong>, 
            fréquente régulièrement les cours en classe de <strong>{{ $classe }}</strong> 
            au titre de l'année scolaire <strong>{{ $certificate->academic_year }}</strong>.
            <br><br>
            Le présent certificat est délivré pour servir et valoir ce que de droit.
            
        @elseif($certificate->type == 'reussite')
            Je soussigné, Le Directeur du Collège Privé Adventiste Avaratetezana certifie que l'élève <strong>{{ $nom }}</strong>, 
            a satisfait aux épreuves de fin d'année et est admis(e) en classe supérieure pour la prochaine rentrée scolaire.
            <br><br>
            En foi de quoi, cette attestation lui est délivrée pour servir et valoir ce que de droit.
            
        @elseif($certificate->type == 'fin_etudes')
            Je soussigné, Le Directeur du Collège Privé Adventiste Avaratetezana certifie que l'élève <strong>{{ $nom }}</strong>, 
            a terminé avec succès ses études au sein de notre établissement pour l'année scolaire <strong>{{ $certificate->academic_year }}</strong>.
            <br><br>
            Le présent certificat est délivré pour servir et valoir ce que de droit.
            
        @elseif($certificate->type == 'paiement')
            Je soussigné, Le Directeur du Collège Privé Adventiste Avaratetezana certifie que l'élève <strong>{{ $nom }}</strong>, 
            inscrit(e) en classe de <strong>{{ $classe }}</strong>, est à jour de ses obligations financières 
            envers l'établissement pour l'année scolaire <strong>{{ $certificate->academic_year }}</strong>.
            <br><br>
            La présente attestation est délivrée pour servir et valoir ce que de droit.
            
        @elseif($certificate->type == 'transfert')
            Par la présente, la direction du Collège Privé Adventiste Avaratetezana atteste que l'élève <strong>{{ $nom }}</strong>, 
            quitte notre établissement. Nous confirmons qu'il/elle est en règle avec l'administration.
            <br><br>
            Nous lui souhaitons plein succès dans la poursuite de ses études.
        @endif
    </div>

    <div class="cert-footer">
        <div></div>
        <div class="cert-signature-box">
            Fait à Antsirabe, le {{ date('d/m/Y', strtotime($certificate->date_issued)) }}<br><br><br>
            <strong>Le Directeur</strong>
            <br><br>
            <em>(Signature et Cachet)</em>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
@endsection
