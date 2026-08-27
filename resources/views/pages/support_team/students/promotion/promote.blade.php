<form method="post" action="{{ route('students.promote', [$fc, $fs, $tc, $ts]) }}">
    @csrf

    <!-- Contrôles de sélection rapide -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">Sélection rapide pour tous les élèves :</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-success" id="select-all-promote">
                                    <i class="icon-checkmark3 mr-1"></i>Tous Réinscrire
                                </button>
                                <button type="button" class="btn btn-outline-warning" id="select-all-repeat">
                                    <i class="icon-reload-alt mr-1"></i>Tous Redoubler
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="select-all-graduate">
                                    <i class="icon-graduation mr-1"></i>Tous Diplômés
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Règle Automatique -->
    <div class="alert alert-info py-2 px-3 mb-3 border-0" style="border-radius: 8px;">
        <i class="icon-info22 mr-1"></i> <strong>Règle de gestion automatique :</strong> Tout élève réinscrit pour la nouvelle session devient automatiquement <span class="badge badge-light-primary font-weight-bold">Ancien</span> avec le statut académique <span class="badge badge-light-success font-weight-bold">Passant</span> (vers la classe supérieure) ou <span class="badge badge-light-warning font-weight-bold">Redoublant</span> (même classe).
    </div>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="bg-light">
        <tr>
            <th style="width: 40px;" class="text-center">#</th>
            <th style="width: 45px;" class="text-center">Photo</th>
            <th>Nom & Prénom de l'élève</th>
            <th style="width: 140px;" class="text-center">Statut Actuel</th>
            <th class="text-center bg-success-100" style="width: 200px;">🟢 Promouvoir (Passant)</th>
            <th class="text-center bg-warning-100" style="width: 200px;">🟡 Redoubler (Même classe)</th>
            <th class="text-center bg-danger-100" style="width: 160px;">🔴 Diplômé / Quitter</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students->sortBy('user.name') as $sr)
            <tr>
                <td class="text-center font-weight-semibold">{{ $loop->iteration }}</td>
                <td class="text-center"><img class="rounded-circle" style="height: 30px; width: 30px;" src="{{ $sr->user->photo ?? asset('global_assets/images/user.png') }}" alt="img" onerror="this.src='{{ asset('global_assets/images/user.png') }}'"></td>
                <td>
                    <strong>{{ $sr->user->name }}</strong>
                    <div class="small text-muted">Matricule : {{ $sr->adm_no }}</div>
                </td>
                <td class="text-center">
                    <span class="badge badge-light-secondary">{{ $sr->user->student_type ?? 'Nouveau' }}</span>
                    <span class="badge badge-light-info">{{ $sr->user->academic_status ?? 'Passant' }}</span>
                </td>
                <td class="text-center bg-success-50">
                    <div class="custom-control custom-radio d-inline-block">
                        <input type="radio" class="custom-control-input promotion-radio"
                               id="reinscrire-{{$sr->id}}"
                               name="p-{{$sr->id}}"
                               value="P"
                               data-student-id="{{$sr->id}}"
                               checked>
                        <label class="custom-control-label font-weight-bold text-success" for="reinscrire-{{$sr->id}}">
                            Passant ➔ {{ $my_classes->where('id', $tc)->first()->name ?? 'Niveau Sup.' }}
                        </label>
                    </div>
                </td>
                <td class="text-center bg-warning-50">
                    <div class="custom-control custom-radio d-inline-block">
                        <input type="radio" class="custom-control-input promotion-radio"
                               id="redoubler-{{$sr->id}}"
                               name="p-{{$sr->id}}"
                               value="D"
                               data-student-id="{{$sr->id}}">
                        <label class="custom-control-label font-weight-bold text-warning" for="redoubler-{{$sr->id}}">
                            Redouble ➔ {{ $my_classes->where('id', $fc)->first()->name ?? 'Même classe' }}
                        </label>
                    </div>
                </td>
                <td class="text-center bg-danger-50">
                    <div class="custom-control custom-radio d-inline-block">
                        <input type="radio" class="custom-control-input promotion-radio"
                               id="quitter-{{$sr->id}}"
                               name="p-{{$sr->id}}"
                               value="G"
                               data-student-id="{{$sr->id}}">
                        <label class="custom-control-label font-weight-bold text-danger" for="quitter-{{$sr->id}}">
                            Diplômé / Quitte
                        </label>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-success btn-lg px-4 font-weight-bold shadow-sm"><i class="icon-stairs-up mr-2"></i> Appliquer et Valider les Promotions</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Boutons de sélection rapide pour toute la classe
    $('#select-all-promote').click(function() {
        $('.promotion-radio[value="P"]').prop('checked', true);
    });

    $('#select-all-repeat').click(function() {
        $('.promotion-radio[value="D"]').prop('checked', true);
    });

    $('#select-all-graduate').click(function() {
        $('.promotion-radio[value="G"]').prop('checked', true);
    });

    // Validation et confirmation avant soumission
    $('form').submit(function(e) {
        var promotedCount = $('.promotion-radio[value="P"]:checked').length;
        var repeatingCount = $('.promotion-radio[value="D"]:checked').length;
        var graduatedCount = $('.promotion-radio[value="G"]:checked').length;

        var message = 'Confirmer l\'application des promotions / réinscriptions ?\n\n';
        message += '• 🟢 Passants (classe supérieure) : ' + promotedCount + ' élève(s)\n';
        message += '• 🟡 Redoublants (même classe)    : ' + repeatingCount + ' élève(s)\n';
        message += '• 🔴 Diplômés / Quittent         : ' + graduatedCount + ' élève(s)\n\n';
        message += 'Tous les élèves réinscrits passeront automatiquement au statut "Ancien".';

        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
