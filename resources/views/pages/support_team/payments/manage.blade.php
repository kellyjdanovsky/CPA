@extends('layouts.master')
@section('page_title', 'Paiement des Étudiants')

@section('content')
    <style>
        /* Styles personnalisés pour la page de gestion des paiements */
        .payment-header {
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            color: white;
            border-radius: 5px 5px 0 0;
            padding: 20px;
            margin-bottom: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .payment-card {
            border: none;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .class-select-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .select-class-label {
            color: #3a7bd5;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .select-class-input {
            border: 2px solid #e9ecef;
            border-radius: 5px;
            padding: 10px;
            transition: all 0.3s ease;
        }
        
        .select-class-input:focus {
            border-color: #3a7bd5;
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.25);
        }
        
        .submit-btn {
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            background: linear-gradient(to right, #3a7bd5, #3a9bd5);
        }
        
        .submit-btn i {
            transition: transform 0.3s ease;
        }
        
        .submit-btn:hover i {
            transform: translateX(5px);
        }
        
        .nav-tabs-custom {
            border-bottom: 2px solid #e9ecef;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 15px 20px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-tabs-custom .nav-link.active {
            color: #3a7bd5;
            background-color: transparent;
        }
        
        .nav-tabs-custom .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            border-radius: 3px 3px 0 0;
        }
        
        /* Custom styles for checkboxes */
        .student-checkbox {
            transform: scale(1.3);
            margin-right: 10px;
        }
        
        .select-all-checkbox {
            transform: scale(1.3);
        }
        
        /* Grouped payment button */
        #grouped-payment-btn {
            background: linear-gradient(to right, #ff416c, #ff4b2b);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            display: none;
        }
        
        #grouped-payment-btn:enabled {
            background: linear-gradient(to right, #00b09b, #96c93d);
        }
        
        #grouped-payment-btn:hover:enabled {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        #grouped-payment-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Modal styles */
        .modal-header {
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            color: white;
            border-radius: 5px 5px 0 0;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 5px;
            padding: 10px;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            border-color: #3a7bd5;
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.25);
        }
        
        .btn-custom {
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
    </style>

    <div class="card payment-card">
        <div class="payment-header">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <i class="icon-cash2 icon-2x"></i>
                </div>
                <div>
                    <h4 class="mb-0 font-weight-semibold">Paiement des Étudiants</h4>
                    <span class="text-white-50">Gérez les paiements par classe</span>
                </div>
                <div class="ml-auto">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-custom">
                <li class="nav-item">
                    <a href="#manage-payments" class="nav-link active" data-toggle="tab">
                        <i class="icon-stack mr-2"></i> Gestion des paiements
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="manage-payments">
                    <div class="class-select-form">
                        <form method="post" action="{{ route('payments.select_class') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <div class="row align-items-end">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="my_class_id" class="select-class-label">
                                                    <i class="icon-users4 mr-2"></i>Sélectionnez une classe
                                                </label>
                                                <select required id="my_class_id" name="my_class_id" class="form-control select select-class-input">
                                                    <option value="">Choisir une classe</option>
                                                    @foreach($my_classes as $c)
                                                        <option {{ ($selected && $my_class_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group text-right">
                                                <button type="submit" class="btn submit-btn">
                                                    <span>Afficher</span>
                                                    <i class="icon-arrow-right8 ml-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($selected)
        <button type="button" id="grouped-payment-btn" class="btn" disabled>
            <i class="icon-coins mr-2"></i> Paiement groupé
        </button>
        
        <style>
            /* Styles pour le tableau des étudiants */
            .student-table-card {
                border: none;
                border-radius: 5px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }
            
            .student-table-header {
                background: linear-gradient(to right, #3a7bd5, #00d2ff);
                color: white;
                padding: 15px 20px;
                font-weight: 600;
            }
            
            .student-table {
                border-collapse: separate;
                border-spacing: 0;
                width: 100% !important;
                margin-bottom: 0 !important;
            }
            
            .student-table thead th {
                background: linear-gradient(to right, #f8f9fa, #f1f3f5);
                color: #495057;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 12px;
                letter-spacing: 0.5px;
                padding: 15px;
                border-bottom: 2px solid #e9ecef;
                position: sticky;
                top: 0;
                z-index: 10;
                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            }
            
            .student-table tbody tr {
                transition: all 0.3s ease;
                border-bottom: 1px solid #e9ecef;
            }
            
            .student-table tbody tr:hover {
                background-color: rgba(58, 123, 213, 0.05);
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.05);
                z-index: 5;
                position: relative;
            }
            
            .student-table tbody tr:last-child {
                border-bottom: none;
            }
            
            .student-table td {
                padding: 15px;
                vertical-align: middle;
                border: none;
                transition: all 0.3s ease;
            }
            
            /* Amélioration de la réactivité */
            @media (max-width: 992px) {
                .student-table thead th {
                    padding: 10px;
                    font-size: 11px;
                }
                
                .student-table td {
                    padding: 10px;
                }
                
                .student-photo {
                    height: 40px;
                    width: 40px;
                }
            }
            
            @media (max-width: 768px) {
                .table-responsive {
                    border-radius: 8px;
                    overflow: hidden;
                }
            }
            
            /* Styles pour les éléments DataTables */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_processing,
            .dataTables_wrapper .dataTables_paginate {
                color: #495057;
                font-size: 13px;
            }
            
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #e9ecef;
                border-radius: 4px;
                padding: 5px 10px;
                margin: 0 5px;
                background-color: #fff;
            }
            
            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #e9ecef;
                border-radius: 4px;
                padding: 5px 10px;
                margin-left: 5px;
                background-color: #fff;
                min-width: 200px;
            }
            
            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #3a7bd5;
                outline: none;
                box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.25);
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 5px 10px;
                margin: 0 2px;
                border-radius: 4px;
                border: none !important;
                background: none !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: linear-gradient(to right, #3a7bd5, #00d2ff) !important;
                color: white !important;
                border: none !important;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #f8f9fa !important;
                color: #3a7bd5 !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
                color: #adb5bd !important;
                cursor: not-allowed;
            }
            
            .dataTables_wrapper .dataTables_info {
                padding-top: 10px;
            }
            
            .dataTables_wrapper .dataTables_empty {
                padding: 50px 0;
                text-align: center;
                color: #6c757d;
                background-color: #f8f9fa;
                border-radius: 8px;
            }
            
            /* Styles pour les boutons d'export */
            .dt-buttons {
                margin-bottom: 15px;
            }
            
            .dt-button {
                margin-right: 5px !important;
                border-radius: 4px !important;
                box-shadow: none !important;
                font-size: 12px !important;
                padding: 6px 12px !important;
                transition: all 0.3s ease !important;
            }
            
            .dt-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            }
            
            .student-photo {
                height: 50px;
                width: 50px;
                object-fit: cover;
                border: 3px solid white;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }
            
            .student-photo:hover {
                transform: scale(1.2);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }
            
            .student-name {
                font-weight: 600;
                color: #3a7bd5;
            }
            
            .student-ref {
                font-family: monospace;
                font-weight: 600;
                color: #6c757d;
                background-color: #f8f9fa;
                padding: 5px 10px;
                border-radius: 3px;
            }
            
            .status-badge {
                padding: 8px 12px;
                font-weight: 600;
                border-radius: 50px;
                font-size: 11px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }
            
            .status-normal {
                background-color: #e8f5e9;
                color: #2e7d32;
            }
            
            .status-adra {
                background-color: #e3f2fd;
                color: #1565c0;
            }
            
            .status-team3 {
                background-color: #fff8e1;
                color: #ff8f00;
            }
            
            .action-btn {
                background: linear-gradient(to right, #ff416c, #ff4b2b);
                border: none;
                color: white;
                padding: 10px 15px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 13px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                display: inline-block;
                text-align: center;
                white-space: nowrap;
                position: relative;
                z-index: 1; /* Assure que le bouton est au-dessus */
                width: auto;
                min-width: 180px;
            }
            
            .action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
                background: linear-gradient(to right, #ff416c, #ff6b6b);
                color: white;
                text-decoration: none;
            }
            
            .action-btn i {
                transition: transform 0.3s ease;
                position: relative;
                top: 1px;
            }
            
            .action-btn:hover i {
                transform: translateY(2px);
            }
            
            /* Assurer que la cellule d'action a suffisamment d'espace */
            .student-table td:last-child {
                min-width: 200px;
                position: relative;
            }
            
            .dropdown-menu {
                border: none;
                border-radius: 5px;
                box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
                padding: 10px 0;
                z-index: 1050 !important; /* Assure que le menu est au-dessus des autres éléments */
                position: absolute !important;
                min-width: 220px; /* Largeur minimale pour éviter les textes coupés */
            }
            
            .dropdown-menu-left {
                right: auto !important;
                left: 0 !important; /* Force le positionnement à gauche */
            }
            
            .dropdown-item {
                padding: 10px 20px;
                color: #495057;
                font-weight: 500;
                transition: all 0.2s ease;
                white-space: nowrap; /* Empêche le texte de se couper */
            }
            
            .dropdown-item:hover {
                background-color: #f8f9fa;
                color: #3a7bd5;
                padding-left: 25px;
            }
            
            /* Styles améliorés pour les actions de paiement */
            .payment-actions-cell {
                position: relative !important;
                min-width: 200px !important;
                overflow: visible !important;
            }
            
            .payment-actions-wrapper {
                position: relative !important;
                display: block !important;
                width: 100% !important;
            }
            
            .payment-actions-group {
                display: block !important;
                width: 100% !important;
            }
            
            .payment-actions-group .action-btn {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                background: linear-gradient(to right, #ff416c, #ff4b2b) !important;
                border: none !important;
                color: white !important;
                padding: 10px 15px !important;
                border-radius: 50px !important;
                font-weight: 600 !important;
                font-size: 13px !important;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
                transition: all 0.3s ease !important;
                position: relative !important;
                z-index: 1 !important;
            }
            
            .payment-actions-group .action-btn:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15) !important;
                background: linear-gradient(to right, #ff416c, #ff6b6b) !important;
            }
            
            .payment-actions-group .action-btn i {
                transition: transform 0.3s ease !important;
                position: relative !important;
                top: 1px !important;
            }
            
            .payment-actions-group .action-btn:hover i {
                transform: translateY(2px) !important;
            }
            
            /* Styles pour le menu déroulant */
            .payment-actions-menu {
                position: absolute !important;
                left: 0 !important;
                min-width: 250px !important;
                max-width: 300px !important;
                padding: 0 !important;
                margin-top: 5px !important;
                border: none !important;
                border-radius: 8px !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
                z-index: 9999 !important;
                overflow: hidden !important;
                background-color: white !important;
            }
            
            .payment-actions-menu .dropdown-header {
                font-size: 12px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                color: #3a7bd5 !important;
                background-color: #f8f9fa !important;
                padding: 10px 15px !important;
                font-weight: 600 !important;
                margin: 0 !important;
            }
            
            .payment-actions-menu .dropdown-item {
                padding: 10px 15px !important;
                color: #495057 !important;
                font-weight: 500 !important;
                transition: all 0.2s ease !important;
                white-space: nowrap !important;
            }
            
            .payment-actions-menu .dropdown-item:hover {
                background-color: #f8f9fa !important;
                color: #3a7bd5 !important;
                padding-left: 20px !important;
            }
            
            .payment-actions-menu .dropdown-divider {
                margin: 5px 0 !important;
                border-top: 1px solid #e9ecef !important;
            }
            
            .dropdown-item:first-child {
                font-weight: 600;
                color: #3a7bd5;
                border-bottom: 1px solid #e9ecef;
            }
            
            .empty-result {
                text-align: center;
                padding: 50px 20px;
                color: #6c757d;
            }
            
            .empty-result i {
                font-size: 48px;
                margin-bottom: 15px;
                color: #e9ecef;
            }
            
            .pagination-container {
                margin-top: 20px;
                display: flex;
                justify-content: flex-end;
            }
        </style>
        
        <div class="card student-table-card">
            <div class="student-table-header d-flex align-items-center">
                <i class="icon-users4 mr-2"></i>
                <span>Liste des étudiants - {{ $selected && isset($my_class_id) ? $my_classes->where('id', $my_class_id)->first()->name : '' }}</span>
                <div class="ml-auto">
                    <span class="badge badge-pill badge-light">{{ count($students) }} étudiants</span>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if(count($students) > 0)
                    <div class="table-responsive">
                        <table class="table student-table payments-datatable">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="select-all" class="select-all-checkbox">
                                    </th>
                                    <th width="5%">N°</th>
                                    <th width="10%">Photo</th>
                                    <th width="25%">Nom</th>
                                    <th width="15%">Référence</th>
                                    <th width="15%">Statut</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $s)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="student-checkbox" data-student-id="{{ $s->user_id }}">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <img class="rounded-circle student-photo" src="{{ $s->user->photo }}" alt="photo">
                                        </td>
                                        <td>
                                            <span class="student-name">{{ $s->user->name }}</span>
                                        </td>
                                        <td>
                                            <span class="student-ref">{{ $s->adm_no }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $s->user->status ?? 'Normal';
                                                $statusClass = 'status-normal';
                                                $statusText = $status;

                                                // Définir la classe CSS en fonction du statut
                                                if ($status == 'Normal') {
                                                    $statusClass = 'status-normal';
                                                } elseif ($status == 'ADRA') {
                                                    $statusClass = 'status-adra';
                                                } elseif ($status == 'Team3') {
                                                    $statusClass = 'status-team3';
                                                }
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td class="payment-actions-cell">
                                            <div class="payment-actions-wrapper">
                                                <div class="btn-group payment-actions-group">
                                                    <button type="button" class="btn action-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span>Gestion par session</span>
                                                        <i class="icon-arrow-down5 ml-2"></i>
                                                    </button>
                                                    <div class="dropdown-menu payment-actions-menu">
                                                        <h6 class="dropdown-header">Options de paiement</h6>
                                                        <a href="{{ route('payments.invoice', [Qs::hash($s->user_id)]) }}" class="dropdown-item">
                                                            <i class="icon-file-text2 mr-2"></i> Tous les paiements
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <h6 class="dropdown-header">Par année scolaire</h6>
                                                        @foreach(Pay::getYears($s->user_id) as $py)
                                                            @if($py)
                                                                <a href="{{ route('payments.invoice', [Qs::hash($s->user_id), $py]) }}" class="dropdown-item">
                                                                    <i class="icon-calendar mr-2"></i> {{ $py }}
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-result">
                        <i class="icon-user-cancel"></i>
                        <h5>Aucun étudiant trouvé</h5>
                        <p>Il n'y a pas d'étudiants dans cette classe ou la classe n'existe pas.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Grouped Payment Modal -->
    <div class="modal fade" id="groupedPaymentModal" tabindex="-1" role="dialog" aria-labelledby="groupedPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupedPaymentModalLabel">
                        <i class="icon-coins mr-2"></i> Paiement groupé
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="grouped-payment-form">
                        @csrf
                        <input type="hidden" id="selected-students" name="student_ids">
                        
                        <div class="form-group">
                            <label for="payment_id" class="font-weight-semibold">
                                <i class="icon-file-text2 mr-2"></i> Motif de paiement
                            </label>
                            <select id="payment_id" name="payment_id" class="form-control form-control-custom" required>
                                <option value="">-- Sélectionner un motif de paiement --</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_method" class="font-weight-semibold">
                                <i class="icon-credit-card mr-2"></i> Mode de paiement
                            </label>
                            <select id="payment_method" name="payment_method" class="form-control form-control-custom" required>
                                <option value="">-- Sélectionner un mode de paiement --</option>
                                <option value="Cash">Cash</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="ADRA">ADRA</option>
                                <option value="TEAM3">TEAM3</option>
                                <option value="Virement Bancaire">Virement Bancaire</option>
                                <option value="Chèque">Chèque</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount_paid" class="font-weight-semibold">
                                <i class="icon-coins mr-2"></i> Montant payé
                            </label>
                            <input type="number" id="amount_paid" name="amount_paid" class="form-control form-control-custom" 
                                   step="0.01" min="0" placeholder="Entrez le montant payé" required>
                            <small class="form-text text-muted">
                                Entrez le montant total payé pour tous les élèves sélectionnés
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="observations" class="font-weight-semibold">
                                <i class="icon-note mr-2"></i> Observations (optionnel)
                            </label>
                            <textarea id="observations" name="observations" class="form-control form-control-custom" 
                                      rows="3" placeholder="Ajoutez des observations si nécessaire"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="icon-cross2 mr-2"></i> Annuler
                    </button>
                    <button type="button" id="submit-grouped-payment" class="btn btn-custom">
                        <i class="icon-checkmark4 mr-2"></i> Valider le paiement groupé
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Animation d'entrée pour les cartes
            $('.payment-card, .student-table-card').css('opacity', 0);
            
            setTimeout(function() {
                $('.payment-card').animate({
                    opacity: 1
                }, 500, function() {
                    if ($('.student-table-card').length) {
                        $('.student-table-card').animate({
                            opacity: 1
                        }, 500);
                    }
                });
            }, 300);
            
            // Amélioration du select
            $('#my_class_id').select2({
                placeholder: 'Choisir une classe',
                allowClear: true,
                width: '100%',
                dropdownCssClass: 'select2-blue'
            });
            
            // Effet de survol sur les lignes du tableau
            $('.student-table tbody tr').hover(
                function() {
                    $(this).find('.student-photo').css('transform', 'scale(1.2)');
                },
                function() {
                    $(this).find('.student-photo').css('transform', 'scale(1)');
                }
            );
            
            // Animation du bouton de soumission
            $('.submit-btn').on('mouseenter', function() {
                $(this).find('i').css('transform', 'translateX(5px)');
            }).on('mouseleave', function() {
                $(this).find('i').css('transform', 'translateX(0)');
            });
            
            // Animation du bouton d'action
            $('.action-btn').on('mouseenter', function() {
                $(this).find('i').css('transform', 'translateY(2px)');
            }).on('mouseleave', function() {
                $(this).find('i').css('transform', 'translateY(0)');
            });
            
            // Gestion améliorée des menus déroulants de paiement
            $(document).ready(function() {
                // Fonction pour positionner correctement le menu déroulant
                function positionPaymentMenu() {
                    $('.payment-actions-menu.show').each(function() {
                        var $menu = $(this);
                        var $button = $menu.prev('.action-btn');
                        var $cell = $menu.closest('.payment-actions-cell');
                        
                        // Obtenir les positions et dimensions
                        var buttonOffset = $button.offset();
                        var cellOffset = $cell.offset();
                        var buttonWidth = $button.outerWidth();
                        var menuWidth = $menu.outerWidth();
                        var windowWidth = $(window).width();
                        
                        // Calculer la position idéale
                        var leftPos = buttonOffset.left;
                        
                        // Ajuster si le menu dépasse à droite
                        if (leftPos + menuWidth > windowWidth - 20) {
                            leftPos = Math.max(20, windowWidth - menuWidth - 20);
                        }
                        
                        // Appliquer la position
                        $menu.css({
                            'position': 'fixed',
                            'left': leftPos,
                            'top': buttonOffset.top + $button.outerHeight() + 5,
                            'z-index': 9999
                        });
                    });
                }
                
                // Gérer l'ouverture du menu
                $(document).on('shown.bs.dropdown', '.payment-actions-group', function() {
                    positionPaymentMenu();
                });
                
                // Repositionner lors du redimensionnement de la fenêtre
                $(window).on('resize', function() {
                    if ($('.payment-actions-menu.show').length) {
                        positionPaymentMenu();
                    }
                });
                
                // Repositionner lors du défilement
                $(window).on('scroll', function() {
                    if ($('.payment-actions-menu.show').length) {
                        positionPaymentMenu();
                    }
                });
                
                // Fermer les menus lors du clic en dehors
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.payment-actions-group').length) {
                        $('.payment-actions-menu.show').removeClass('show');
                        $('.payment-actions-group .dropdown-toggle.show').removeClass('show');
                    }
                });
                
                // Empêcher la fermeture lors du clic sur le menu lui-même
                $(document).on('click', '.payment-actions-menu', function(e) {
                    e.stopPropagation();
                });
            });
            
            // Checkbox selection functionality
            let selectedStudents = [];
            
            // Select all checkbox
            $('#select-all').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.student-checkbox').prop('checked', isChecked);
                updateSelectedStudents();
            });
            
            // Individual student checkbox
            $(document).on('change', '.student-checkbox', function() {
                updateSelectedStudents();
                
                // Update select all checkbox state
                const totalCheckboxes = $('.student-checkbox').length;
                const checkedCheckboxes = $('.student-checkbox:checked').length;
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
            
            // Update selected students array
            function updateSelectedStudents() {
                selectedStudents = [];
                $('.student-checkbox:checked').each(function() {
                    selectedStudents.push($(this).data('student-id'));
                });
                
                // Enable/disable grouped payment button
                if (selectedStudents.length > 0) {
                    $('#grouped-payment-btn').show().prop('disabled', false);
                } else {
                    $('#grouped-payment-btn').prop('disabled', true);
                }
            }
            
            // Show grouped payment modal
            $('#grouped-payment-btn').on('click', function() {
                if (selectedStudents.length === 0) return;
                
                // Set selected students in hidden input
                $('#selected-students').val(selectedStudents.join(','));
                
                // Populate payment options based on selected class
                populatePaymentOptions();
                
                // Clear form fields
                $('#grouped-payment-form')[0].reset();
                $('#amount_paid').prop('readonly', false);
                
                // Show modal
                $('#groupedPaymentModal').modal('show');
            });
            
            // Populate payment options based on class
            function populatePaymentOptions() {
                const classId = {{ $selected && isset($my_class_id) ? $my_class_id : 'null' }};
                
                // Clear existing options
                $('#payment_id').empty().append('<option value="">-- Sélectionner un motif de paiement --</option>');
                
                // Only make AJAX call if classId is valid
                if (classId !== null) {
                    // Make AJAX call to get payments for the class
                    $.ajax({
                        url: '{{ route("payments.get_class_payments_ajax") }}',
                        method: 'GET',
                        data: {
                            class_id: classId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Populate payment options
                            $.each(response, function(index, payment) {
                                const formattedAmount = new Intl.NumberFormat('fr-FR').format(payment.amount) + ' Ar';
                                $('#payment_id').append(
                                    `<option value="${payment.id}" data-amount="${payment.amount}">${payment.title} (${formattedAmount})</option>`
                                );
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching payments:', error);
                            alert('Erreur lors du chargement des motifs de paiement.');
                        }
                    });
                }
            }
            
            // Handle payment selection change
            $('#payment_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const amount = selectedOption.data('amount');
                
                if (amount) {
                    const totalAmount = amount * selectedStudents.length;
                    $('#amount_paid').val(totalAmount).prop('readonly', true);
                } else {
                    $('#amount_paid').val('').prop('readonly', false);
                }
            });
            
            // Submit grouped payment
            $('#submit-grouped-payment').on('click', function() {
                // Validate form
                if (!$('#payment_id').val() || !$('#payment_method').val() || !$('#amount_paid').val()) {
                    alert('Veuillez remplir tous les champs obligatoires.');
                    return;
                }
                
                const formData = $('#grouped-payment-form').serialize();
                
                // Show loading state
                $(this).prop('disabled', true).html('<i class="icon-spinner4 spinner mr-2"></i> Traitement...');
                
                // Make AJAX call to process grouped payment
                $.ajax({
                    url: '{{ route("payments.process_grouped_payment") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Close modal
                            $('#groupedPaymentModal').modal('hide');
                            
                            // Show success message
                            alert(response.message);
                            
                            // Reset selections
                            $('.student-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            selectedStudents = [];
                            $('#grouped-payment-btn').prop('disabled', true).hide();
                            
                            // Reload the page to show updated payment status
                            location.reload();
                        } else {
                            alert('Erreur: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error processing grouped payment:', error);
                        alert('Une erreur est survenue lors du traitement du paiement groupé.');
                    },
                    complete: function() {
                        // Reset button
                        $('#submit-grouped-payment').prop('disabled', false).html('<i class="icon-checkmark4 mr-2"></i> Valider le paiement groupé');
                    }
                });
            });
            
            // Initialisation améliorée de DataTables avec une classe personnalisée
            if ($('.payments-datatable').length) {
                // Détruire l'instance existante si elle existe
                if ($.fn.DataTable.isDataTable('.payments-datatable')) {
                    $('.payments-datatable').DataTable().destroy();
                }
                
                // Initialiser avec notre configuration personnalisée
                var paymentsTable = $('.payments-datatable').DataTable({
                    buttons: {
                        dom: {
                            button: {
                                className: 'btn btn-light'
                            }
                        },
                        buttons: [
                            {
                                extend: 'copyHtml5',
                                text: '<i class="icon-copy3 mr-2"></i> Copier',
                                className: 'btn btn-outline-primary',
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                text: '<i class="icon-file-spreadsheet mr-2"></i> CSV',
                                className: 'btn btn-outline-primary',
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'excelHtml5',
                                text: '<i class="icon-file-excel mr-2"></i> Excel',
                                className: 'btn btn-outline-primary',
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="icon-file-pdf mr-2"></i> PDF',
                                className: 'btn btn-outline-primary',
                                orientation: 'landscape',
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="icon-printer mr-2"></i> Imprimer',
                                className: 'btn btn-outline-primary',
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            }
                        ]
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json",
                        buttons: {
                            copyTitle: 'Copié dans le presse-papiers',
                            copySuccess: {
                                _: '%d lignes copiées',
                                1: '1 ligne copiée'
                            }
                        }
                    },
                    responsive: true,
                    dom: '<"datatable-header d-flex justify-content-between align-items-center py-2"<"datatable-title"l><"datatable-buttons"B><"datatable-search"f>><"datatable-scroll-wrap"t><"datatable-footer d-flex justify-content-between align-items-center py-2"<"datatable-info"i><"datatable-pagination"p>>',
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                    autoWidth: false,
                    columnDefs: [
                        { orderable: false, targets: [0, 1, 6] }, // Désactiver le tri pour les colonnes checkbox, photo et action
                        { width: "5%", targets: 0 },
                        { width: "5%", targets: 1 },
                        { width: "10%", targets: 2 },
                        { width: "25%", targets: 3 },
                        { width: "15%", targets: 4 },
                        { width: "15%", targets: 5 },
                        { width: "25%", targets: 6 }
                    ],
                    drawCallback: function() {
                        // Réinitialiser les événements après chaque redraw
                        $('.payment-actions-menu.show').removeClass('show');
                        $('.payment-actions-group .dropdown-toggle.show').removeClass('show');
                        
                        // Appliquer des styles aux éléments de pagination
                        $('.dataTables_paginate .paginate_button').addClass('btn btn-sm');
                        $('.dataTables_paginate .paginate_button.current').addClass('btn-primary').removeClass('btn-sm');
                        $('.dataTables_paginate .paginate_button:not(.current)').addClass('btn-light');
                    }
                });
                
                // Ajouter des styles personnalisés aux éléments de DataTables
                $('.dataTables_length select').addClass('form-control-sm select-class-input');
                $('.dataTables_filter input').addClass('form-control-sm select-class-input').attr('placeholder', 'Rechercher...');
                
                // Réagir aux changements de page/filtre pour repositionner les menus
                paymentsTable.on('draw.dt page.dt search.dt', function() {
                    $('.payment-actions-menu.show').removeClass('show');
                    $('.payment-actions-group .dropdown-toggle.show').removeClass('show');
                });
            }
        });
    </script>
@endsection