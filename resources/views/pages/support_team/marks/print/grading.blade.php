<div style="margin-bottom: 5px; text-align: center">
    <table border="0" cellpadding="5" cellspacing="5" style="text-align: center; margin: 0 auto;">
        <tr>
            <td><strong>CLÉ DE NOTATION</strong></td>
            @if(Mk::getGradeList($class_type->id)->count())
                @foreach(Mk::getGradeList($class_type->id) as $gr)
                    <td><strong>{{ $gr->name }}</strong>
                        => {{ $gr->mark_from.' - '.$gr->mark_to }}
                    </td>
                @endforeach
            @endif
        </tr>
    </table>

</div>


<table style="width:100%; border-collapse:collapse; ">
    <tbody>
    <tr>
        <td><strong>NOMBRE DE :</strong></td>
        <td><strong>Distinctions :</strong> {{ Mk::countDistinctions($marks) }}</td>
        <td><strong>Crédits :</strong> {{ Mk::countCredits($marks) }}</td>
        <td><strong>Passages :</strong> {{ Mk::countPasses($marks) }}</td>
        <td><strong>Échecs :</strong> {{ Mk::countFailures($marks) }}</td>
        <td><strong>Matières Offertes :</strong> {{ Mk::countSubjectsOffered($marks) }}</td>
    </tr>

    </tbody>
</table>
