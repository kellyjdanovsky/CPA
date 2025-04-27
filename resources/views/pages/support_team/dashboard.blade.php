@extends('layouts.master')
@section('page_title', 'Mon tableau de bord')
@section('content')

    @if(Qs::userIsTeamSA())
    <div class="row fade-in">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-card bg-primary has-bg-image">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 icon-box">
                            <i class="icon-users4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white">{{ $total_active_students }}</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Total élèves</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-card bg-danger has-bg-image">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 icon-box">
                            <i class="icon-users2"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white">{{ $users->where('user_type', 'teacher')->count() }}</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Total Enseignants</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3 mb-3">
            <a href="{{ route('financial_dashboard.index') }}" class="card dashboard-card bg-success has-bg-image">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 icon-box">
                            <i class="icon-stats-bars2"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white">Finances</h3>
                            <span class="text-uppercase font-size-xs font-weight-bold text-white-50">Tableau de bord financier</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                            <div class="progress-bar bg-white" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

       {{-- Display Student Count Per Class --}}
       <div class="card fade-in mt-3">
           <div class="card-header bg-white">
               <h5 class="card-title">Nombre d'élèves par classe</h5>
               <div class="header-elements">
                   <div class="list-icons">
                       <a class="list-icons-item" data-action="collapse"></a>
                   </div>
               </div>
           </div>
           <div class="card-body">
               <div class="row">
                   @foreach($classes as $class)
                   <div class="col-sm-6 col-md-4 col-xl-3 mb-3">
                       <div class="card dashboard-card bg-teal has-bg-image">
                           <div class="card-body">
                               <div class="d-flex align-items-center">
                                   <div class="mr-3 icon-box">
                                       <i class="icon-users2"></i>
                                   </div>
                                   <div>
                                       <h3 class="mb-0 text-white">{{ $class_student_counts[$class->id] }}</h3>
                                       <span class="text-uppercase font-size-xs font-weight-bold text-white-50">{{ $class->name }}</span>
                                   </div>
                               </div>
                               <div class="mt-3">
                                   <div class="progress bg-white bg-opacity-25" style="height: 4px;">
                                       <div class="progress-bar bg-white" style="width: {{ min(100, ($class_student_counts[$class->id] / max(1, $total_active_students)) * 100) }}%"></div>
                                   </div>
                                   <div class="mt-1 text-right">
                                       <small class="text-white-50">{{ number_format(($class_student_counts[$class->id] / max(1, $total_active_students)) * 100, 1) }}% du total</small>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
                   @endforeach
               </div>
           </div>
       </div>
       @endif

    {{--Events Calendar Begins--}}
    <div class="card fade-in mt-3">
        <div class="card-header bg-white">
            <h5 class="card-title">Calendrier des événements</h5>
            <div class="header-elements">
                <div class="list-icons">
                    {!! Qs::getPanelOptions() !!}
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="fullcalendar-basic"></div>
        </div>
    </div>
    {{--Events Calendar Ends--}}
    @endsection
