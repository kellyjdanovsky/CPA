<form class="ajax-update" action="{{ route('marks.update', [$exam_id, $my_class_id, $section_id, $subject_id]) }}" method="post">
    @csrf @method('put')

    <div class="alert alert-info py-2 px-3 mb-3 border-0" style="border-radius: 8px;">
        <i class="icon-keyboard mr-1"></i> <strong>Saisie rapide au clavier :</strong> Utilisez les touches fléchées (<strong>↑ ↓ ← →</strong>) ou <strong>Entrée</strong> pour naviguer rapidement de cellule en cellule comme dans Excel.
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover marks-entry-table" id="marks-table">
            <thead class="bg-light">
                <tr>
                    <th style="width: 45px;" class="text-center">N°</th>
                    <th>Nom & Prénom</th>
                    <th style="width: 130px;" class="text-center">N° Admission</th>
                    <th style="width: 140px;" class="text-center bg-primary-100">1ère Interro (/20)</th>
                    <th style="width: 140px;" class="text-center bg-primary-100">2ème Interro (/20)</th>
                    <th style="width: 140px;" class="text-center bg-success-100">EXAMEN (/20)</th>
                </tr>
            </thead>
            <tbody>
            @foreach($marks->sortBy('user.name') as $mk)
                <tr class="mark-row">
                    <td class="text-center font-weight-semibold">{{ $loop->iteration }}</td>
                    <td class="font-weight-bold text-dark">{{ $mk->user->name }}</td>
                    <td class="text-center"><span class="badge badge-light-secondary">{{ $mk->user->student_record->adm_no ?? '-' }}</span></td>

                    {{-- CA ET EXAMENS --}}
                    <td class="text-center p-1">
                        <input title="1er CA" min="0" max="20" step="0.25"
                               class="form-control form-control-sm text-center font-weight-bold mark-input" 
                               data-row="{{ $loop->index }}" data-col="0"
                               name="t1_{{ $mk->id }}" value="{{ $mk->t1 }}" type="number">
                    </td>
                    <td class="text-center p-1">
                        <input title="2ème CA" min="0" max="20" step="0.25"
                               class="form-control form-control-sm text-center font-weight-bold mark-input" 
                               data-row="{{ $loop->index }}" data-col="1"
                               name="t2_{{ $mk->id }}" value="{{ $mk->t2 }}" type="number">
                    </td>
                    <td class="text-center p-1">
                        <input title="EXAMEN" min="0" max="20" step="0.25"
                               class="form-control form-control-sm text-center font-weight-bold text-success mark-input" 
                               data-row="{{ $loop->index }}" data-col="2"
                               name="exm_{{ $mk->id }}" value="{{ $mk->exm }}" type="number">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-center mt-3 mb-2">
        <button type="submit" class="btn btn-success btn-lg px-4 font-weight-bold shadow-sm">
            <i class="icon-floppy-disk mr-2"></i> Enregistrer et Mettre à jour les Notes
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('marks-table');
        if (!table) return;

        const inputs = table.querySelectorAll('.mark-input');

        inputs.forEach(input => {
            input.addEventListener('focus', function () {
                this.select();
                const row = this.closest('tr');
                if (row) row.classList.add('table-primary');
            });

            input.addEventListener('blur', function () {
                const row = this.closest('tr');
                if (row) row.classList.remove('table-primary');
            });

            input.addEventListener('keydown', function (e) {
                const curRow = parseInt(this.getAttribute('data-row'));
                const curCol = parseInt(this.getAttribute('data-col'));

                let targetInput = null;

                if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    targetInput = table.querySelector(`.mark-input[data-row="${curRow + 1}"][data-col="${curCol}"]`);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    targetInput = table.querySelector(`.mark-input[data-row="${curRow - 1}"][data-col="${curCol}"]`);
                } else if (e.key === 'ArrowRight') {
                    if (this.selectionStart === this.value.length) {
                        targetInput = table.querySelector(`.mark-input[data-row="${curRow}"][data-col="${curCol + 1}"]`);
                    }
                } else if (e.key === 'ArrowLeft') {
                    if (this.selectionStart === 0) {
                        targetInput = table.querySelector(`.mark-input[data-row="${curRow}"][data-col="${curCol - 1}"]`);
                    }
                }

                if (targetInput) {
                    targetInput.focus();
                }
            });
        });
    });
</script>
