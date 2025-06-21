@if(Qs::userIsTeamSAT())
    <div class="card">
        <div class="card-header header-elements-inline bg-dark">
            <h6 class="card-title font-weight-bold">Commentaires de l'examen</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body collapse show">
            <form class="ajax-update" method="post" action="{{ route('marks.comment_update', $exr->id) }}">
                @csrf @method('PUT')

                @isset($exr->t_comment)
                    <div class="alert alert-info">
                        <strong>Commentaire de l'enseignant :</strong> {{ $exr->t_comment }}
                        <button type="button" class="btn btn-sm btn-link delete-comment" data-comment-type="t_comment" data-exam-record-id="{{ $exr->id }}">
                            <i class="icon-trash"></i> Supprimer
                        </button>
                    </div>
                @endisset

                @isset($exr->p_comment)
                    <div class="alert alert-info">
                        <strong>Commentaire du directeur d'école :</strong> {{ $exr->p_comment }}
                        <button type="button" class="btn btn-sm btn-link delete-comment" data-comment-type="p_comment" data-exam-record-id="{{ $exr->id }}">
                            <i class="icon-trash"></i> Supprimer
                        </button>
                    </div>
                @endisset

                @if(Qs::userIsTeamSAT())
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label font-weight-semibold">Nouveau commentaire de l'enseignant</label>
                        <div class="col-lg-8">
                            <input name="t_comment" value="{{ $exr->t_comment }}"  type="text" class="form-control" placeholder="Nouveau commentaire">
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-primary btn-sm update-comment" data-comment-type="t_comment" data-exam-record-id="{{ $exr->id }}">
                                <i class="icon-checkmark"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                @endif

                @if(Qs::userIsTeamSA())
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label font-weight-semibold">Nouveau commentaire du directeur d'école</label>
                        <div class="col-lg-8">
                            <input name="p_comment" value="{{ $exr->p_comment }}"  type="text" class="form-control" placeholder="Nouveau commentaire">
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-primary btn-sm update-comment" data-comment-type="p_comment" data-exam-record-id="{{ $exr->id }}">
                                <i class="icon-checkmark"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                @endif

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Soumettre le formulaire <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle delete comment buttons
            $('.delete-comment').on('click', function() {
                var commentType = $(this).data('comment-type');
                var examRecordId = $(this).data('exam-record-id');
                var button = $(this);

                if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                    // Create form data for deletion
                    var formData = new FormData();
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('_method', 'PUT');
                    formData.append(commentType, ''); // Set comment to empty string

                    $.ajax({
                        url: '{{ route("marks.comment_update", ":id") }}'.replace(':id', examRecordId),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.ok) {
                                // Clear the input field
                                $('input[name="' + commentType + '"]').val('');
                                // Hide the delete button
                                button.hide();
                                // Show success message
                                flash({msg: 'Commentaire supprimé avec succès', type: 'success'});
                            }
                        },
                        error: function() {
                            flash({msg: 'Erreur lors de la suppression du commentaire', type: 'danger'});
                        }
                    });
                }
            });

            // Show delete button when user types in comment field
            $('input[name="t_comment"], input[name="p_comment"]').on('input', function() {
                var commentType = $(this).attr('name');
                var deleteButton = $('.delete-comment[data-comment-type="' + commentType + '"]');

                if ($(this).val().trim() !== '') {
                    deleteButton.show();
                } else {
                    deleteButton.hide();
                }
            });
        });
    </script>
@endif
