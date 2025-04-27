<div>
    <table class="td-left" style="border-collapse:collapse;">
        <tbody>
        <tr>
            <td><strong>COMMENTAIRE DE L'ENSEIGNANT DE CLASSE :</strong></td>
            <td>  {{ $exr->t_comment ?: str_repeat('__', 40) }}</td>
        </tr>
        <tr>
            <td><strong>COMMENTAIRE DU DIRECTEUR :</strong></td>
            <td>  {{ $exr->p_comment ?: str_repeat('__', 40) }}</td>
        </tr>
    
      
        </tbody>
    </table>
</div>
<div>
    <table class="td-left" style="border-collapse:collapse;">
        <tbody>
        <tr>
            <td><strong>Signature du directeur:</strong></td>
            <td>  {{ str_repeat('__', 40) }}</td>
        </tr>
        <tr>
            <td><strong>Signature des parents:</strong></td>
            <td>  {{ str_repeat('__', 40) }}</td>
        </tr>
    
      
        </tbody>
    </table>
</div>