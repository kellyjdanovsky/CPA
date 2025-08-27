@extends('layouts.master')
@section('page_title', 'Gestion des Encaissements')

@section('page_style')
<style>
.encaissement-card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}
.encaissement-card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transform: translateY(-2px);
}
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}
.student-selection-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
}
.student-selection-card.selected {
    background: #e3f2fd;
    border-color: #2196f3;
}
.amount-calculation {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 5px;
    padding: 10px;
    margin: 10px 0;
}
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h5 class="card-title">
            <i class="icon-wallet mr-2 text-primary"></i> 
            Gestion des Encaissements ADRA & TEAM 3
        </h5>
        <div class="header-elements">
            <div class="list-icons">
                {!! Qs::getPanelOptions() !!}
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Statistiques rapides -->
        @if(isset($statistics))
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="icon-wallet" style="font-size: 2rem; opacity: 0.8;"></i>
                        <h3 class="mt-2 mb-1">{{ $statistics['total_encaissements'] }}</h3>
                        <p class="mb-0">Total Encaissements</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="icon-coins" style="font-size: 2rem; opacity: 0.8;"></i>
                        <h3 class="mt-2 mb-1">{{ number_format($statistics['total_montant'], 0, ',', ' ') }} Ar</h3>
                        <p class="mb-0">Montant Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="icon-users4" style="font-size: 2rem; opacity: 0.8;"></i>
                        <h3 class="mt-2 mb-1">{{ $statistics['adra_count'] }}</h3>
                        <p class="mb-0">Encaissements ADRA</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="icon-users2" style="font-size: 2rem; opacity: 0.8;"></i>
                        <h3 class="mt-2 mb-1">{{ $statistics['team3_count'] }}</h3>
                        <p class="mb-0">Encaissements TEAM 3</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Navigation -->
        <ul class="nav nav-tabs nav-tabs-highlight mb-4">
            <li class="nav-item">
                <a href="#nouveau-encaissement" class="nav-link {{ (!isset($encaissements) || $encaissements->count() == 0) && !request()->has('tab') ? 'active' : '' }}" data-toggle="tab">
                    <i class="icon-plus-circle2 mr-2"></i>Nouvel Encaissement
                </a>
            </li>
            <li class="nav-item">
                <a href="#liste-encaissements" class="nav-link {{ (isset($encaissements) && $encaissements->count() > 0) || request()->get('tab') == 'liste' ? 'active' : '' }}" data-toggle="tab">
                    <i class="icon-list mr-2"></i>Liste des Encaissements
                    @if(isset($encaissements) && $encaissements->count() > 0)
                        <span class="badge badge-primary ml-1">{{ $encaissements->count() }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Nouveau Encaissement -->
            <div class="tab-pane fade {{ (!isset($encaissements) || $encaissements->count() == 0) && !request()->has('tab') ? 'show active' : '' }}" id="nouveau-encaissement">
                <form id="encaissement-form">
                    @csrf
                    <div class="row">
                        <!-- Configuration -->
                        <div class="col-md-6">
                            <div class="card encaissement-card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-cog mr-2"></i>Configuration
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Classe <span class="text-danger">*</span></label>
                                        <select name="class_id" id="class-select" class="form-control select" required>
                                            <option value="">Choisir une classe</option>
                                            @foreach($my_classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Type d'encaissement <span class="text-danger">*</span></label>
                                        <select name="type_encaissement" id="type-select" class="form-control" required>
                                            <option value="">Choisir le type</option>
                                            <option value="ADRA">ADRA (75% pris en charge)</option>
                                            <option value="TEAM3">TEAM 3 (100% pris en charge)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Paiement <span class="text-danger">*</span></label>
                                        <select name="payment_id" id="payment-select" class="form-control" required disabled>
                                            <option value="">Sélectionner d'abord une classe</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations -->
                        <div class="col-md-6">
                            <div class="card encaissement-card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-info22 mr-2"></i>Informations
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="payment-info" style="display: none;">
                                        <div class="amount-calculation">
                                            <h6 class="mb-2">Calcul automatique :</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Montant original :</strong><br>
                                                    <span id="original-amount" class="text-primary">0 Ar</span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Montant à encaisser :</strong><br>
                                                    <span id="calculated-amount" class="text-success">0 Ar</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="text-center">
                                                <span id="percentage-info" class="badge badge-primary">-</span>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" class="btn btn-info btn-block" onclick="loadEligibleStudents()">
                                                <i class="icon-users mr-2"></i>Charger les étudiants éligibles
                                            </button>
                                        </div>
                                    </div>

                                    <div id="no-payment-info" class="text-center text-muted">
                                        <i class="icon-info22" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="mt-2">Sélectionnez une classe et un paiement</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étudiants éligibles -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card encaissement-card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-users4 mr-2"></i>Étudiants Éligibles
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="students-container">
                                        <div class="text-center text-muted">
                                            <i class="icon-users" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="mt-2">Cliquez sur "Charger les étudiants éligibles"</p>
                                        </div>
                                    </div>

                                    <div id="action-buttons" class="mt-4" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="bg-light p-3 rounded">
                                                    <h6>Résumé :</h6>
                                                    <p class="mb-1">
                                                        <strong>Sélectionnés :</strong> 
                                                        <span id="selected-count" class="text-primary">0</span>
                                                    </p>
                                                    <p class="mb-0">
                                                        <strong>Total :</strong> 
                                                        <span id="total-amount" class="text-success">0 Ar</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="button" class="btn btn-success btn-lg" onclick="processEncaissement()">
                                                    <i class="icon-credit-card mr-2"></i>Encaisser
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Liste des Encaissements -->
            <div class="tab-pane fade {{ (isset($encaissements) && $encaissements->count() > 0) || request()->get('tab') == 'liste' ? 'show active' : '' }}" id="liste-encaissements">
                @if(isset($encaissements) && $encaissements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Référence</th>
                                <th>Étudiant</th>
                                <th>Type</th>
                                <th>Montant Encaissé</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($encaissements as $encaissement)
                            <tr>
                                <td>{{ $encaissement->date_encaissement }}</td>
                                <td><span class="badge badge-info">{{ $encaissement->reference_encaissement }}</span></td>
                                <td>{{ $encaissement->student->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $encaissement->type_encaissement == 'ADRA' ? 'badge-primary' : 'badge-success' }}">
                                        {{ $encaissement->type_encaissement }}
                                    </span>
                                </td>
                                <td><strong class="text-success">{{ number_format($encaissement->montant_encaisse, 0, ',', ' ') }} Ar</strong></td>
                                <td>
                                    <a href="{{ route('payments.encaissements.show', $encaissement->id) }}" class="btn btn-sm btn-info">
                                        <i class="icon-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="icon-wallet" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h4 class="mt-3">Aucun encaissement trouvé</h4>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_script')
<script>
let selectedStudents = [];
let currentPaymentAmount = 0;
let currentType = '';

// Fonction pour afficher des notifications
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'warning' ? 'alert-warning' :
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="icon-${type === 'success' ? 'checkmark-circle2' : type === 'warning' ? 'warning' : type === 'error' ? 'blocked' : 'info22'} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insérer la notification en haut du formulaire
    $('#encaissement-form').prepend(alertHtml);
    
    // Auto-dismiss après 5 secondes
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}

// Charger les paiements quand une classe est sélectionnée
$('#class-select').change(function() {
    const classId = $(this).val();
    const paymentSelect = $('#payment-select');
    
    if (classId) {
        // Afficher un indicateur de chargement
        paymentSelect.prop('disabled', true);
        paymentSelect.html('<option value="">Chargement des paiements...</option>');
        
        $.get('{{ route("payments.encaissements.get_class_payments") }}', { class_id: classId })
        .done(function(response) {
            if (response.success && response.payments.length > 0) {
                paymentSelect.html('<option value="">Choisir un paiement</option>');
                response.payments.forEach(function(payment) {
                    paymentSelect.append(`<option value="${payment.id}" data-amount="${payment.amount}">
                        ${payment.title} - ${Number(payment.amount).toLocaleString()} Ar
                    </option>`);
                });
                paymentSelect.prop('disabled', false);
                
                // Afficher un message de succès
                showNotification('success', 'Paiements chargés avec succès');
            } else {
                paymentSelect.html('<option value="">Aucun paiement disponible</option>');
                paymentSelect.prop('disabled', true);
                
                // Afficher un message d'avertissement
                showNotification('warning', response.message || 'Aucun paiement trouvé pour cette classe');
            }
        })
        .fail(function(xhr) {
            paymentSelect.html('<option value="">Erreur de chargement</option>');
            paymentSelect.prop('disabled', true);
            
            // Afficher un message d'erreur
            showNotification('error', 'Erreur lors du chargement des paiements');
            console.error('Erreur AJAX:', xhr.responseText);
        });
    } else {
        resetPaymentInfo();
    }
});

// Mettre à jour les informations quand un paiement est sélectionné
$('#payment-select, #type-select').change(function() {
    const selectedOption = $('#payment-select').find(':selected');
    const amount = selectedOption.data('amount');
    const type = $('#type-select').val();
    
    if (amount && type) {
        updatePaymentInfo(amount, type);
    }
});

function updatePaymentInfo(amount, type) {
    currentPaymentAmount = amount;
    currentType = type;
    
    const percentage = type === 'ADRA' ? 75 : 100;
    const calculatedAmount = amount * (percentage / 100);
    
    $('#original-amount').text(Number(amount).toLocaleString() + ' Ar');
    $('#calculated-amount').text(Number(calculatedAmount).toLocaleString() + ' Ar');
    $('#percentage-info').text(percentage + '% pris en charge');
    
    $('#payment-info').show();
    $('#no-payment-info').hide();
}

function resetPaymentInfo() {
    $('#payment-info').hide();
    $('#no-payment-info').show();
    selectedStudents = [];
}

function loadEligibleStudents() {
    const classId = $('#class-select').val();
    const paymentId = $('#payment-select').val();
    const type = $('#type-select').val();
    
    if (!classId || !paymentId || !type) {
        showNotification('warning', 'Veuillez sélectionner une classe, un paiement et un type');
        return;
    }
    
    // Afficher un indicateur de chargement
    $('#students-container').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2">Chargement des étudiants éligibles...</p>
        </div>
    `);
    
    $.get('{{ route("payments.encaissements.get_eligible_students") }}', {
        class_id: classId,
        payment_id: paymentId,
        type: type
    })
    .done(function(response) {
        if (response.success && response.students.length > 0) {
            displayStudents(response.students);
            $('#action-buttons').show();
            showNotification('success', `${response.students.length} étudiant(s) trouvé(s)`);
        } else {
            $('#students-container').html(`
                <div class="text-center text-muted">
                    <i class="icon-users" style="font-size: 3rem; opacity: 0.3;"></i>
                    <h4 class="mt-3">Aucun étudiant éligible</h4>
                    <p class="text-muted">Aucun étudiant n'a de paiement en attente pour cette sélection.</p>
                </div>
            `);
            $('#action-buttons').hide();
            showNotification('info', 'Aucun étudiant éligible trouvé pour cette configuration');
        }
    })
    .fail(function(xhr) {
        $('#students-container').html(`
            <div class="text-center text-danger">
                <i class="icon-warning" style="font-size: 3rem; opacity: 0.3;"></i>
                <h4 class="mt-3">Erreur de chargement</h4>
                <p class="text-muted">Impossible de charger les étudiants éligibles.</p>
            </div>
        `);
        $('#action-buttons').hide();
        showNotification('error', 'Erreur lors du chargement des étudiants');
        console.error('Erreur AJAX:', xhr.responseText);
    });
}

function displayStudents(students) {
    let html = '<div class="row">';
    
    students.forEach(function(student) {
        const calculatedAmount = currentPaymentAmount * (currentType === 'ADRA' ? 0.75 : 1);
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="student-selection-card">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input student-checkbox" 
                               id="student_${student.id}" 
                               data-student-id="${student.id}"
                               data-payment-record-id="${student.payment_record_id}"
                               data-amount="${calculatedAmount}">
                        <label class="form-check-label" for="student_${student.id}">
                            <strong>${student.name}</strong><br>
                            <small class="text-muted">${student.adm_no}</small>
                            <div class="badge badge-success float-right">${Number(calculatedAmount).toLocaleString()} Ar</div>
                        </label>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#students-container').html(html);
    
    $('.student-checkbox').change(updateSelection);
}

function updateSelection() {
    selectedStudents = [];
    let totalAmount = 0;
    
    $('.student-checkbox:checked').each(function() {
        selectedStudents.push({
            student_id: $(this).data('student-id'),
            payment_record_id: $(this).data('payment-record-id'),
            montant_original: currentPaymentAmount,
            selected: true
        });
        
        totalAmount += $(this).data('amount');
        $(this).closest('.student-selection-card').addClass('selected');
    });
    
    $('.student-checkbox:not(:checked)').each(function() {
        $(this).closest('.student-selection-card').removeClass('selected');
    });
    
    $('#selected-count').text(selectedStudents.length);
    $('#total-amount').text(Number(totalAmount).toLocaleString() + ' Ar');
}

function processEncaissement() {
    if (selectedStudents.length === 0) {
        showNotification('warning', 'Veuillez sélectionner au moins un étudiant');
        return;
    }
    
    // Confirmer avant de soumettre
    if (!confirm(`Êtes-vous sûr de vouloir encaisser pour ${selectedStudents.length} étudiant(s) ?`)) {
        return;
    }
    
    const formData = {
        class_id: $('#class-select').val(),
        payment_id: $('#payment-select').val(),
        type_encaissement: $('#type-select').val(),
        students: selectedStudents,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    // Afficher un indicateur de chargement
    const submitButton = $('button[onclick="processEncaissement()"]');
    const originalText = submitButton.html();
    submitButton.prop('disabled', true).html('<i class="icon-spinner spinner mr-2"></i>Processing...');
    
    $.post('{{ route("payments.encaissements.process") }}', formData)
    .done(function(response) {
        if (response.success) {
            showNotification('success', 'Encaissement traité avec succès !');
            
            // Basculer vers l'onglet Liste des Encaissements après 1 seconde
            setTimeout(function() {
                // Activer l'onglet "Liste des Encaissements"
                $('a[href="#liste-encaissements"]').tab('show');
                
                // Recharger la page après 1 seconde supplémentaire pour voir les nouveaux encaissements
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }, 1000);
        } else {
            showNotification('error', 'Erreur : ' + (response.message || 'Une erreur est survenue'));
        }
    })
    .fail(function(xhr) {
        showNotification('error', 'Erreur de connexion au serveur');
        console.error('Erreur AJAX:', xhr.responseText);
    })
    .always(function() {
        submitButton.prop('disabled', false).html(originalText);
    });
}

$(document).ready(function() {
    // Initialiser DataTable si présent
    if ($('.datatable-button-html5-columns').length) {
        $('.datatable-button-html5-columns').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    }
    
    // Gérer la navigation par hash dans l'URL
    function handleHashNavigation() {
        const hash = window.location.hash;
        if (hash === '#liste-encaissements') {
            $('a[href="#liste-encaissements"]').tab('show');
        } else if (hash === '#nouveau-encaissement') {
            $('a[href="#nouveau-encaissement"]').tab('show');
        }
    }
    
    // Appliquer la navigation par hash au chargement de la page
    handleHashNavigation();
    
    // Écouter les changements de hash
    $(window).on('hashchange', handleHashNavigation);
    
    // Mettre à jour l'URL quand on change d'onglet
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target) {
            window.location.hash = target;
        }
    });
});
</script>
@endsection