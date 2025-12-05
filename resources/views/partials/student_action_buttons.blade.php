{{-- Partial pour les boutons d'action sur les étudiants --}}
{{-- Usage: @include('partials.student_action_buttons', ['s' => $student]) --}}

<td class="text-center action-column">
    <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
        {{-- Bouton Voir --}}
        <a href="{{ route('students.show', Qs::hash($s->id)) }}" 
           class="btn btn-sm btn-primary rounded-circle" 
           data-toggle="tooltip" 
           title="Voir le profil"
           style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
            <i class="icon-eye"></i>
        </a>
        
        {{-- Bouton Modifier --}}
        @if(Qs::userIsTeamSA())
            <a href="{{ route('students.edit', Qs::hash($s->id)) }}" 
               class="btn btn-sm btn-warning rounded-circle" 
               data-toggle="tooltip" 
               title="Modifier"
               style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                <i class="icon-pencil"></i>
            </a>
        @endif
        
        {{-- Bouton Supprimer --}}
        @if(Qs::userIsSuperAdmin())
            <a id="{{ Qs::hash($s->user->id) }}" 
               onclick="confirmDelete(this.id)" 
               href="#" 
               class="btn btn-sm btn-danger rounded-circle" 
               data-toggle="tooltip" 
               title="Supprimer"
               style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                <i class="icon-trash"></i>
            </a>
            <form method="post" id="item-delete-{{ Qs::hash($s->user->id) }}" action="{{ route('students.destroy', Qs::hash($s->user->id)) }}" class="hidden">@csrf @method('delete')</form>
        @endif
        
        {{-- Menu déroulant pour autres options --}}
        <div class="dropdown d-inline-block">
            <a href="#" class="btn btn-sm btn-secondary rounded-circle" data-toggle="dropdown" title="Plus d'options" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                <i class="icon-more2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                @if(Qs::userIsTeamSA())
                    <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock mr-2"></i>Réinitialiser MDP</a>
                @endif
                <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check mr-2"></i>Fiche de notes</a>
            </div>
        </div>
    </div>
</td>
