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

    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Nom</th>
            <th>Session actuelle</th>
            <th class="text-center">Réinscrire</th>
            <th class="text-center">Redoubler</th>
            <th class="text-center">Quitter</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students->sortBy('user.name') as $sr)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img class="rounded-circle" style="height: 30px; width: 30px;" src="{{ $sr->user->photo ?? asset('global_assets/images/user.png') }}" alt="img" onerror="this.src='{{ asset('global_assets/images/user.png') }}'"></td>
                <td>{{ $sr->user->name }}</td>
                <td>{{ $sr->session }}</td>
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input promotion-checkbox"
                               id="reinscrire-{{$sr->id}}"
                               name="p-{{$sr->id}}"
                               value="P"
                               data-student-id="{{$sr->id}}"
                               checked>
                        <label class="custom-control-label" for="reinscrire-{{$sr->id}}">
                            <span class="text-success font-weight-semibold">Promouvoir</span>
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input promotion-checkbox"
                               id="redoubler-{{$sr->id}}"
                               name="p-{{$sr->id}}"
                               value="D"
                               data-student-id="{{$sr->id}}">
                        <label class="custom-control-label" for="redoubler-{{$sr->id}}">
                            <span class="text-warning font-weight-semibold">Ne pas promouvoir</span>
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input promotion-checkbox"
                               id="quitter-{{$sr->id}}"
                               name="p-{{$sr->id}}"
                               value="G"
                               data-student-id="{{$sr->id}}">
                        <label class="custom-control-label" for="quitter-{{$sr->id}}">
                            <span class="text-danger font-weight-semibold">Diplômé</span>
                        </label>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-success btn-lg"><i class="icon-stairs-up mr-2"></i> Appliquer les Promotions</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Boutons de sélection rapide
    $('#select-all-promote').click(function() {
        $('.promotion-checkbox').prop('checked', false);
        $('.promotion-checkbox[value="P"]').prop('checked', true);
    });

    $('#select-all-repeat').click(function() {
        $('.promotion-checkbox').prop('checked', false);
        $('.promotion-checkbox[value="D"]').prop('checked', true);
    });

    $('#select-all-graduate').click(function() {
        $('.promotion-checkbox').prop('checked', false);
        $('.promotion-checkbox[value="G"]').prop('checked', true);
    });

    // Gérer les cases à cocher pour qu'une seule option soit sélectionnée par élève
    $('.promotion-checkbox').change(function() {
        if ($(this).is(':checked')) {
            var studentId = $(this).data('student-id');

            // Décocher toutes les autres cases pour cet élève
            $('.promotion-checkbox[data-student-id="' + studentId + '"]').not(this).prop('checked', false);
        }
    });

    // Validation avant soumission
    $('form').submit(function(e) {
        var hasSelection = false;
        var studentIds = [];

        // Récupérer tous les IDs d'élèves
        $('.promotion-checkbox').each(function() {
            var studentId = $(this).data('student-id');
            if (studentIds.indexOf(studentId) === -1) {
                studentIds.push(studentId);
            }
        });

        // Vérifier que chaque élève a au moins une option sélectionnée
        var missingSelections = [];
        studentIds.forEach(function(studentId) {
            var hasChecked = $('.promotion-checkbox[data-student-id="' + studentId + '"]:checked').length > 0;
            if (!hasChecked) {
                missingSelections.push(studentId);
            } else {
                hasSelection = true;
            }
        });

        if (missingSelections.length > 0) {
            e.preventDefault();
            alert('Veuillez sélectionner une option pour tous les élèves avant de continuer.');
            return false;
        }

        if (!hasSelection) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une option pour un élève.');
            return false;
        }

        // Confirmation
        var promotedCount = $('.promotion-checkbox[value="P"]:checked').length;
        var repeatingCount = $('.promotion-checkbox[value="D"]:checked').length;
        var graduatedCount = $('.promotion-checkbox[value="G"]:checked').length;

        var message = 'Confirmer les promotions ?\n\n';
        message += '• Réinscrire (promouvoir): ' + promotedCount + ' élève(s)\n';
        message += '• Redoubler: ' + repeatingCount + ' élève(s)\n';
        message += '• Quitter (diplômé): ' + graduatedCount + ' élève(s)';

        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
