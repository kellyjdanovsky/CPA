@extends('layouts.master')
@section('page_title', 'Gestion des Paiements ADRA & TEAM 3')

@section('page_style')
<style>
/* Modern DataTable Styling */
.table-modern {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 15px 12px;
    border: none;
    position: relative;
}

.table-modern thead th:first-child {
    border-top-left-radius: 8px;
}

.table-modern thead th:last-child {
    border-top-right-radius: 8px;
}

.table-modern tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #e9ecef;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-modern tbody td {
    padding: 12px;
    vertical-align: middle;
    border: none;
}

.table-modern tbody tr:last-child td:first-child {
    border-bottom-left-radius: 8px;
}

.table-modern tbody tr:last-child td:last-child {
    border-bottom-right-radius: 8px;
}

/* Badge Styling */
.badge-modern {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Status Badges */
.status-adra {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.status-team3 {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

/* Form Controls */
.form-control-sm {
    border-radius: 6px;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
}

.form-control-sm:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Payment Selection */
.payment-selection {
    max-width: 200px;
}

.form-check-inline {
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

/* Action Buttons */
.btn-modern {
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        border-radius: 8px;
    }

    .payment-selection {
        max-width: 150px;
    }

    .form-check-inline {
        display: block;
        margin-right: 0;
    }
}

/* DataTable Custom Styling */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border-radius: 6px;
    border: 1px solid #ddd;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px;
    margin: 0 2px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}

/* Summary Cards */
.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.card.bg-primary, .card.bg-success, .card.bg-info, .card.bg-warning {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card.bg-primary .card-body,
.card.bg-success .card-body,
.card.bg-info .card-body,
.card.bg-warning .card-body {
    padding: 1rem;
}

/* Filter Section */
.form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

/* Action Buttons */
.btn-group-vertical .btn {
    margin-bottom: 0;
}

.btn-group-vertical .btn:not(:last-child) {
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}

.btn-group-vertical .btn:not(:first-child) {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

/* Summary Display */
.font-weight-bold {
    font-weight: 600 !important;
}

.text-success {
    color: #28a745 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.text-warning {
    color: #ffc107 !important;
}

/* Disabled state */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Hidden rows */
tr[style*="display: none"] {
    display: none !important;
}
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">
            <i class="icon-credit-card mr-2"></i>
            Gestion des Paiements ADRA & TEAM 3
        </h6>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
                <a class="list-icons-item" data-action="reload"></a>
                <a class="list-icons-item" data-action="remove"></a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Filter Zone -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="class_selector">Choisir une classe :</label>
                    <select id="class_selector" class="form-control select2">
                        <option value="">-- Sélectionner une classe --</option>
                        <option value="all">📚 TOUTES LES CLASSES</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $class->id == $selectedClassId ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Paiements créés dans cette classe :</label>
                    <div id="payments-container" class="border rounded p-3" style="min-height: 100px; background-color: #f8f9fa;">
                        <div class="text-muted text-center">
                            <i class="icon-info22 mr-2"></i>Choisir d'abord une classe pour voir les paiements
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Summary Info -->
        <div class="row mb-3" id="summary-section" style="display: none;">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="icon-info22 mr-3" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>Classe sélectionnée :</strong> <span id="selected-class-name">-</span> |
                            <strong>Paiements sélectionnés :</strong> <span id="selected-payments-count">0</span> |
                            <strong>Montant total :</strong> <span id="selected-payments-total">0 Ar</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small><strong>Détail :</strong> <span id="selected-payments-detail">Aucun paiement sélectionné</span></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3" id="action-section" style="display: none;">
            <div class="col-md-12 text-center">
                <button type="button" class="btn btn-success btn-lg mr-2" onclick="printAllReceipts()" id="print-all-btn">
                    <i class="icon-printer mr-2"></i>Imprimer tous les reçus 25%
                </button>
                <button type="button" class="btn btn-info btn-lg mr-2" onclick="exportMainTableExcel()">
                    <i class="icon-file-excel mr-2"></i>Export Excel (25%)
                </button>
                <button type="button" class="btn btn-warning btn-lg" onclick="exportSummaryExcel()">
                    <i class="icon-file-excel mr-2"></i>Export Récapitulatif (75%/100%)
                </button>
            </div>
        </div>

        <!-- Nav tabs for main table and summary -->
        <ul class="nav nav-tabs mb-3" id="adraTeam3Tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="main-tab" data-toggle="tab" href="#mainTable" role="tab">
                    <i class="icon-list mr-1"></i>Liste des Paiements (25% ADRA)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="summary-tab" data-toggle="tab" href="#summaryTable" role="tab">
                    <i class="icon-chart mr-1"></i>Récapitulatif Détaillé
                </a>
            </li>
        </ul>

        <div class="tab-content" id="adraTeam3TabContent">
            <!-- Main Students Table -->
            <div class="tab-pane fade show active" id="mainTable" role="tabpanel">
                <div class="table-responsive" id="students-table" style="display: none;">
                    <table id="adra_team3_table" class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nom & Prénoms</th>
                                <th>Classe</th>
                                <th>Statut</th>
                                <th>Code Référence</th>
                                <th>Montant 25% (À Payer)</th>
                                <th>Reste à Payer</th>
                                <th>Imprimer Reçu</th>
                            </tr>
                        </thead>
                        <tbody id="students-tbody">
                            <!-- Les étudiants seront chargés dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="tab-pane fade" id="summaryTable" role="tabpanel">
                <div class="table-responsive" id="summary-table-container" style="display: none;">
                    <table id="summary_table" class="table table-modern table-bordered">
                        <thead id="summary-thead">
                            <!-- Headers will be generated dynamically -->
                        </thead>
                        <tbody id="summary-tbody">
                            <!-- Summary rows will be generated dynamically -->
                        </tbody>
                        <tfoot id="summary-tfoot">
                            <!-- Grand total will be here -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Chargement...</span>
                </div>
                <p class="mt-2">Traitement en cours...</p>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Script inline pour éviter les problèmes de chargement -->
<script>
console.log('🔧 Script inline chargé');

// Fonctions globales définies immédiatement
window.loadClassPayments = function() {
    const classId = document.getElementById('class_selector').value;
    console.log('loadClassPayments called with classId:', classId);

    if (!classId) {
        document.getElementById('payments-container').innerHTML = `
            <div class="text-muted text-center">
                <i class="icon-info22 mr-2"></i>Choisir d'abord une classe pour voir les paiements
            </div>
        `;
        document.getElementById('summary-section').style.display = 'none';
        document.getElementById('action-section').style.display = 'none';
        document.getElementById('students-table').style.display = 'none';
        return;
    }

    // Show loading
    document.getElementById('payments-container').innerHTML = `
        <div class="text-center">
            <i class="icon-spinner2 spinner mr-2"></i>Chargement des paiements...
        </div>
    `;

    console.log('Loading payments for class:', classId);

    // AJAX call
    fetch('{{ route("payments.adra_team3.get_payments") }}?class_id=' + classId)
        .then(response => response.json())
        .then(data => {
            console.log('Payments response:', data);

            if (data.success && data.payments && data.payments.length > 0) {
                let paymentsHtml = '<div class="row">';

                data.payments.forEach(function(payment, index) {
                    const formattedAmount = new Intl.NumberFormat('fr-FR').format(payment.amount) + ' Ar';
                    paymentsHtml += `
                        <div class="col-md-6 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input payment-checkbox"
                                       id="payment_${payment.id}"
                                       value="${payment.id}"
                                       data-amount="${payment.amount}"
                                       data-title="${payment.title}"
                                       onchange="updateSelectedPayments()">
                                <label class="custom-control-label" for="payment_${payment.id}">
                                    <strong>${payment.title}</strong><br>
                                    <small class="text-muted">${formattedAmount}</small>
                                </label>
                            </div>
                        </div>
                    `;
                });

                paymentsHtml += '</div>';
                paymentsHtml += `
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPayments()">
                            <i class="icon-checkmark mr-1"></i>Tout sélectionner
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="clearAllPayments()">
                            <i class="icon-cross mr-1"></i>Tout désélectionner
                        </button>
                    </div>
                `;

                document.getElementById('payments-container').innerHTML = paymentsHtml;

                // Update summary
                const className = document.getElementById('class_selector').options[document.getElementById('class_selector').selectedIndex].text;
                document.getElementById('selected-class-name').textContent = className;
            } else {
                document.getElementById('payments-container').innerHTML = `
                    <div class="text-muted text-center">
                        <i class="icon-warning mr-2"></i>Aucun paiement trouvé pour cette classe
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading payments:', error);
            document.getElementById('payments-container').innerHTML = `
                <div class="text-danger text-center">
                    <i class="icon-warning mr-2"></i>Erreur lors du chargement des paiements
                </div>
            `;
            alert('Erreur lors du chargement des paiements');
        });
};

// Fonction pour sélectionner tous les paiements
window.selectAllPayments = function() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedPayments();
};

// Fonction pour désélectionner tous les paiements
window.clearAllPayments = function() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedPayments();
};

// Fonction pour mettre à jour l'affichage des paiements sélectionnés
window.updateSelectedPayments = function() {
    const selectedCheckboxes = document.querySelectorAll('.payment-checkbox:checked');
    const count = selectedCheckboxes.length;

    if (count === 0) {
        document.getElementById('summary-section').style.display = 'none';
        document.getElementById('action-section').style.display = 'none';
        document.getElementById('students-table').style.display = 'none';
        return;
    }

    let totalAmount = 0;
    let paymentDetails = [];

    selectedCheckboxes.forEach(checkbox => {
        const amount = parseInt(checkbox.getAttribute('data-amount'));
        const title = checkbox.getAttribute('data-title');
        totalAmount += amount;
        paymentDetails.push(title);
    });

    // Mettre à jour l'affichage du résumé
    document.getElementById('selected-payments-count').textContent = count;
    document.getElementById('selected-payments-total').textContent = new Intl.NumberFormat('fr-FR').format(totalAmount) + ' Ar';
    document.getElementById('selected-payments-detail').textContent = paymentDetails.join(', ');
    document.getElementById('summary-section').style.display = 'block';

    // Charger les étudiants pour les paiements sélectionnés
    loadStudentsWithSelectedPayments();
};

window.loadStudentsWithSelectedPayments = function() {
    const classId = document.getElementById('class_selector').value;
    const selectedCheckboxes = document.querySelectorAll('.payment-checkbox:checked');

    if (!classId || selectedCheckboxes.length === 0) {
        document.getElementById('summary-section').style.display = 'none';
        document.getElementById('action-section').style.display = 'none';
        document.getElementById('students-table').style.display = 'none';
        document.getElementById('summary-table-container').style.display = 'none';
        return;
    }

    // Récupérer les paiements sélectionnés avec leurs infos
    const selectedPayments = Array.from(selectedCheckboxes).map(cb => ({
        id: cb.value,
        amount: parseInt(cb.getAttribute('data-amount')),
        title: cb.getAttribute('data-title')
    }));
    const selectedPaymentIds = selectedPayments.map(p => p.id);
    const totalAmount = selectedPayments.reduce((sum, p) => sum + p.amount, 0);

    // AJAX call to get students
    fetch('{{ route("payments.adra_team3.get_students") }}?class_id=' + classId + '&payment_id=' + selectedPaymentIds[0])
        .then(response => response.json())
        .then(data => {
            if (data.success && data.students && data.students.length > 0) {
                let tableRows = '';
                let summaryRows = '';
                let grandTotal = 0;

                // Generate summary table headers
                let summaryHeaders = '<tr><th>Nom Élève</th><th>Statut</th><th>Code Réf</th><th>Classe</th>';
                selectedPayments.forEach(p => {
                    summaryHeaders += `<th>${p.title}</th>`;
                });
                summaryHeaders += '<th>Total</th></tr>';
                document.getElementById('summary-thead').innerHTML = summaryHeaders;

                data.students.forEach(function(student) {
                    const status = student.status;
                    const statusIcon = status === 'ADRA' ? '🏛️' : '👥';
                    const statusClass = status === 'ADRA' ? 'badge-info' : 'badge-success';
                    
                    // Pour ADRA: 25% à payer, pour TEAM3: 0%
                    const percentage = status === 'ADRA' ? 0.25 : 0;
                    const amountToPay = totalAmount * percentage;
                    const paidByProgram = status === 'ADRA' ? (totalAmount * 0.75) : totalAmount;
                    const resteToPay = student.has_paid_25 ? 0 : amountToPay;
                    const resteClass = resteToPay > 0 ? 'text-danger' : 'text-success';
                    const resteText = resteToPay > 0 ? `${new Intl.NumberFormat('fr-FR').format(resteToPay)} Ar` : '✓ Payé';

                    tableRows += `
                        <tr data-student-id="${student.id}" data-status="${status}" data-total-amount="${totalAmount}" data-selected-payments='${JSON.stringify(selectedPaymentIds)}'>
                            <td>
                                <div class="font-weight-semibold">${student.name}</div>
                                <small class="text-muted">${student.adm_no || ''}</small>
                            </td>
                            <td><span class="badge badge-primary">${student.class_name}</span></td>
                            <td><span class="badge badge-modern ${statusClass}">${statusIcon} ${status}</span></td>
                            <td>
                                <input type="text" class="form-control form-control-sm reference-code"
                                       value="${student.reference_code}"
                                       data-student-id="${student.id}"
                                       onblur="saveReferenceCode(this)">
                            </td>
                            <td>
                                <strong class="text-primary">${new Intl.NumberFormat('fr-FR').format(amountToPay)} Ar</strong>
                                <br><small class="text-muted">(25% de ${new Intl.NumberFormat('fr-FR').format(totalAmount)} Ar)</small>
                            </td>
                            <td><strong class="${resteClass}">${resteText}</strong></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="printIndividualReceipt(${student.id})">
                                    <i class="icon-printer"></i> Reçu 25%
                                </button>
                            </td>
                        </tr>
                    `;

                    // Summary row - show 75% for ADRA, 100% for TEAM3
                    let studentTotal = 0;
                    summaryRows += `<tr>
                        <td><strong>${student.name}</strong></td>
                        <td><span class="badge ${statusClass}">${status}</span></td>
                        <td>${student.reference_code}</td>
                        <td>${student.class_name}</td>`;
                    
                    selectedPayments.forEach(p => {
                        const paymentPercent = status === 'ADRA' ? 0.75 : 1;
                        const paymentAmount = p.amount * paymentPercent;
                        studentTotal += paymentAmount;
                        summaryRows += `<td>${new Intl.NumberFormat('fr-FR').format(paymentAmount)} Ar</td>`;
                    });
                    
                    grandTotal += studentTotal;
                    summaryRows += `<td><strong>${new Intl.NumberFormat('fr-FR').format(studentTotal)} Ar</strong></td></tr>`;
                });

                // Grand total row
                const grandTotalRow = `<tr class="table-dark">
                    <td colspan="${4 + selectedPayments.length}" class="text-right"><strong>GRAND TOTAL:</strong></td>
                    <td><strong>${new Intl.NumberFormat('fr-FR').format(grandTotal)} Ar</strong></td>
                </tr>`;

                document.getElementById('students-tbody').innerHTML = tableRows;
                document.getElementById('summary-tbody').innerHTML = summaryRows;
                document.getElementById('summary-tfoot').innerHTML = grandTotalRow;
                document.getElementById('students-table').style.display = 'block';
                document.getElementById('summary-table-container').style.display = 'block';
                document.getElementById('action-section').style.display = 'block';
            } else {
                document.getElementById('students-tbody').innerHTML = '<tr><td colspan="7" class="text-center">Aucun étudiant ADRA ou TEAM3 trouvé</td></tr>';
                document.getElementById('students-table').style.display = 'block';
                document.getElementById('action-section').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            document.getElementById('students-tbody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>';
            document.getElementById('students-table').style.display = 'block';
        });
};

// Fonction pour sauvegarder automatiquement le code référence
window.saveReferenceCode = function(input) {
    const studentId = input.getAttribute('data-student-id');
    const referenceCode = input.value;
    
    fetch('{{ route("payments.adra_team3.update_reference") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            student_id: studentId,
            reference_code: referenceCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.classList.add('border-success');
            setTimeout(() => input.classList.remove('border-success'), 2000);
        }
    })
    .catch(error => console.error('Error saving reference:', error));
};

// Fonction d'impression individuelle pour paiements multiples
window.printIndividualReceipt = function(studentId) {
    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const totalAmount = row.getAttribute('data-total-amount');
    const status = row.getAttribute('data-status');
    const selectedPayments = JSON.parse(row.getAttribute('data-selected-payments'));
    const referenceCode = row.querySelector('.reference-code').value;

    if (!selectedPayments || selectedPayments.length === 0) {
        alert('Aucun paiement sélectionné');
        return;
    }

    console.log('Printing receipt for student:', studentId, 'payments:', selectedPayments, 'total:', totalAmount);

    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('selected_payments', JSON.stringify(selectedPayments));
    formData.append('total_amount', totalAmount);
    formData.append('status', status);
    formData.append('reference_code', referenceCode);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("payments.adra_team3.print_receipt", "") }}/' + studentId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    })
    .catch(error => {
        console.error('❌ Erreur lors de l\'impression:', error);
        alert('Erreur lors de l\'impression');
    });
};

// Fonction d'impression de tous les reçus
window.printAllReceipts = function() {
    const classId = document.getElementById('class_selector').value;
    const selectedCheckboxes = document.querySelectorAll('.payment-checkbox:checked');

    if (!classId || selectedCheckboxes.length === 0) {
        alert('Veuillez sélectionner une classe et au moins un paiement');
        return;
    }

    const selectedPaymentIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    const studentsData = [];
    const rows = document.querySelectorAll('#students-tbody tr[data-student-id]');

    rows.forEach(function(row) {
        const studentId = row.getAttribute('data-student-id');
        const totalAmount = row.getAttribute('data-total-amount');
        const status = row.getAttribute('data-status');
        const referenceCode = row.querySelector('.reference-code').value;

        if (studentId) {
            studentsData.push({
                student_id: studentId,
                selected_payments: selectedPaymentIds,
                total_amount: totalAmount,
                status: status,
                reference_code: referenceCode
            });
        }
    });

    if (studentsData.length === 0) {
        alert('Aucun étudiant à imprimer');
        return;
    }

    const formData = new FormData();
    formData.append('students_data', JSON.stringify(studentsData));
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("payments.adra_team3.print_batch") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    })
    .catch(error => {
        console.error('❌ Erreur lors de l\'impression:', error);
        alert('Erreur lors de l\'impression');
    });
};

// Event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM loaded, setting up event listeners...');

    // Class selector change event
    const classSelector = document.getElementById('class_selector');
    if (classSelector) {
        classSelector.addEventListener('change', function() {
            console.log('Class selector changed:', this.value);
            window.loadClassPayments();
        });
        console.log('✅ Class selector event listener added');
    } else {
        console.error('❌ Class selector not found');
    }

    // Note: Les event listeners pour les paiements sont ajoutés dynamiquement
    // lors du chargement des paiements via onchange="updateSelectedPayments()"

    // Load class payments if class is already selected
    const selectedClassId = classSelector ? classSelector.value : null;
    if (selectedClassId) {
        console.log('Auto-loading payments for pre-selected class:', selectedClassId);
        window.loadClassPayments();
    }

    console.log('✅ All event listeners set up successfully');
});

// Fonction d'export Excel du tableau principal (25%)
window.exportMainTableExcel = function() {
    const rows = document.querySelectorAll('#students-tbody tr[data-student-id]');
    if (rows.length === 0) {
        alert('Aucune donnée à exporter');
        return;
    }

    // Créer les données du tableau
    const data = [['Nom & Prénoms', 'Classe', 'Statut', 'Code Référence', 'Montant 25% (À Payer)', 'Reste à Payer']];
    
    rows.forEach(row => {
        const name = row.querySelector('.font-weight-semibold')?.textContent || '';
        const className = row.querySelector('.badge-primary')?.textContent || '';
        const status = row.getAttribute('data-status') || '';
        const refCode = row.querySelector('.reference-code')?.value || '';
        const amount25 = row.querySelector('.text-primary')?.textContent?.replace(' Ar', '').trim() || '';
        const reste = row.querySelectorAll('strong')[1]?.textContent?.replace(' Ar', '').replace('✓ Payé', '0').trim() || '';
        
        data.push([name, className, status, refCode, amount25, reste]);
    });

    exportToExcel(data, 'Paiements_25_ADRA_' + new Date().toISOString().slice(0,10));
};

// Fonction d'export Excel du récapitulatif (75%/100%)
window.exportSummaryExcel = function() {
    const theadRow = document.querySelector('#summary-thead tr');
    const tbodyRows = document.querySelectorAll('#summary-tbody tr');
    const tfootRow = document.querySelector('#summary-tfoot tr');
    
    if (!theadRow || tbodyRows.length === 0) {
        alert('Aucune donnée à exporter');
        return;
    }

    // Headers
    const headers = [];
    theadRow.querySelectorAll('th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    
    const data = [headers];

    // Body
    tbodyRows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(td => {
            rowData.push(td.textContent.trim());
        });
        data.push(rowData);
    });

    // Footer (Grand Total)
    if (tfootRow) {
        const footerData = [];
        tfootRow.querySelectorAll('td').forEach(td => {
            footerData.push(td.textContent.trim());
        });
        data.push(footerData);
    }

    exportToExcel(data, 'Recapitulatif_75_100_ADRA_TEAM3_' + new Date().toISOString().slice(0,10));
};

// Fonction d'export vers Excel (utilise un format compatible Excel)
window.exportToExcel = function(data, filename) {
    // Créer le contenu en format XML (Excel 2003 XML)
    // On sépare les balises pour éviter que Blade ne les interprète comme du PHP
    let xml = '<' + '?xml version="1.0" encoding="UTF-8"?' + '>\n';
    xml += '<' + '?mso-application progid="Excel.Sheet"?' + '>\n';
    xml += '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
    xml += 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">\n';
    xml += '<Worksheet ss:Name="Données"><Table>\n';
    
    data.forEach((row, rowIndex) => {
        xml += '<Row>\n';
        row.forEach(cell => {
            // Déterminer le type de cellule
            const cellValue = String(cell).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const isNumber = !isNaN(cellValue.replace(/\s/g, '').replace(',', '.')) && cellValue.trim() !== '';
            
            if (isNumber) {
                xml += `<Cell><Data ss:Type="Number">${cellValue.replace(/\s/g, '').replace(',', '.')}</Data></Cell>\n`;
            } else {
                xml += `<Cell><Data ss:Type="String">${cellValue}</Data></Cell>\n`;
            }
        });
        xml += '</Row>\n';
    });
    
    xml += '</Table></Worksheet></Workbook>';
    
    // Télécharger le fichier
    const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename + '.xls';
    link.click();
};
</script>

@section('page_script')
<script>
// Script de compatibilité pour Select2 uniquement
$(document).ready(function() {
    console.log('🔧 jQuery script loaded for Select2 compatibility');

    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            placeholder: 'Sélectionner...',
            allowClear: true
        });
        console.log('✅ Select2 initialized');
    } else {
        console.log('⚠️ Select2 not available');
    }
});
// Note: Les fonctions principales sont définies dans le script inline ci-dessus
// Ce script ne contient que la compatibilité Select2


</script>
@endsection
