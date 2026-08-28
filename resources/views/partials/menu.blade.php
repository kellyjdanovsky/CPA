@php
$usertype = Auth::user()->user_type;
$idteach = Auth::user()->id;
$liste_classe = App\Models\MyClass::orderBy('name')->get();
use App\Models\Subject as sub;
use App\Models\MyClass as kilasy;

if ($usertype == 'teacher') {
    $teacher = 'true';
    $subjects = sub::where('teacher_id', $idteach)->get();

    foreach ($subjects as $subject) {
        $classId = $subject->my_class_id;
        $class = kilasy::find($classId);
       // dd($class);
        if ($class) {
            $liste_classe[] = $class; // Ajoute la classe à la liste des classes
        }
    }

    //dd($liste_classe);
}
@endphp





<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="card-body">
                <div class="media">
                    <div class="mr-3">
                        <a href="{{ url('/') }}"><img src="{{ Auth::user()->photo }}" width="38" height="38" class="rounded-circle" alt="photo"></a>
                    </div>

                    <div class="media-body">
                        <div class="media-title font-weight-semibold">{{ Auth::user()->name }}</div>
                        <div class="font-size-xs opacity-50">
                            <i class="icon-user font-size-sm"></i> &nbsp;{{ ucwords(str_replace('_', ' ', Auth::user()->user_type)) }}
                        </div>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ url('/') }}" class="text-white"><i class="icon-cog3"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link">
                        <i class="icon-home4"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                {{--Academics--}}


                {{--Administrative--}}
                @if(Qs::userIsAdministrative())
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.create', 'payments.invoice', 'payments.receipts', 'payments.edit', 'payments.manage', 'payments.show', 'payments.adra_team3', 'payments.adra_team3.filter', 'payments.adra_team3.print_receipt']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-office"></i> <span> Administration Financiere</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Administrative">

                            {{--Payments--}}
                            @if(Qs::userIsTeamAccount())
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.create', 'payments.edit', 'payments.manage', 'payments.show', 'payments.invoice']) ? 'nav-item-expanded' : '' }}">

                                <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.edit', 'payments.create', 'payments.manage', 'payments.show', 'payments.invoice']) ? 'active' : '' }}">Paiements</a>

                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ url('/payments/create') }}" class="nav-link">Créer Paiement</a></li>
                                    <li class="nav-item"><a href="{{ url('/payments') }}" class="nav-link">Gerer Paiement</a></li>
                                    {{--PARTIE CAISSE - SUPPRIMÉE--}}
                                    <li class="nav-item"><a href="{{ url('/payments/manage') }}" class="nav-link">Paiement Eleves</a></li>
                                    <li class="nav-item"><a href="{{ url('/payments/verified') }}" class="nav-link">Verifications</a></li>
                                    <li class="nav-item"><a href="{{ url('/payments/journal') }}" class="nav-link">Journal des Paiements</a></li>


                                </ul>

                            </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{--Manage Students--}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.create', 'students.list', 'students.edit', 'students.show', 'students.promotion', 'students.promotion_manage', 'students.graduated']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-users"></i> <span> Eleves</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Manage Students">
                            {{--Admit Student--}}
                            @if(Qs::userIsTeamSA())
                                <li class="nav-item">
                                    <a href="{{ url('/students/create') }}"
                                       class="nav-link">Inscription</a>
                                </li>
                            @endif

                            {{--Student Information--}}
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.list', 'students.edit', 'students.show', 'students.list_all']) ? 'nav-item-expanded' : '' }}">
                                <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.list', 'students.edit', 'students.show', 'students.list_all']) ? 'active' : '' }}"> Information des Eleves</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('students.list_all') }}" class="nav-link {{ (Route::currentRouteName() == 'students.list_all') ? 'active' : '' }}"><i class="icon-list-unordered"></i> Liste complète des étudiants</a></li>
                                    <li class="nav-item"><hr class="my-1"></li>
                                    @foreach($liste_classe as $c)
                                        <li class="nav-item"><a href="{{ url('/students/list/'.$c->id) }}" class="nav-link ">{{ $c->name }}</a></li>
                                    @endforeach
                                </ul>
                            </li>

                            @if(Qs::userIsTeamSA())

                            {{--Student Promotion--}}
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.promotion', 'students.promotion_manage']) ? 'nav-item-expanded' : '' }}"><a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion', 'students.promotion_manage']) ? 'active' : '' }}">Gerer l'admission</a>
                            <ul class="nav nav-group-sub">
                                <li class="nav-item"><a href="{{ url('/students/promotion') }}" class="nav-link">Admission Eleves</a></li>
                                <li class="nav-item"><a href="{{ url('/students/promotion/manage') }}" class="nav-link">Liste d'admission</a></li>
                            </ul>
                            </li>

                            {{--Student Graduated--}}

                                @endif

                            {{--PASCOMA - Attestations d'Assurance--}}
                            <li class="nav-item">
                                <a href="{{ route('pascoma.index') }}" class="nav-link {{ Route::is('pascoma.*') ? 'active' : '' }}">
                                    <i class="icon-clipboard3"></i> PASCOMA
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif

                {{-- Gestion des Présences --}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ Route::is('attendance.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('attendance.*') ? 'active' : '' }}">
                            <i class="icon-clipboard2"></i> <span>Présences</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('attendance.index') }}" class="nav-link {{ Route::is('attendance.index') ? 'active' : '' }}">
                                    <i class="icon-checkmark3"></i> Faire l'appel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('attendance.monthly-report') }}" class="nav-link {{ Route::is('attendance.monthly-report') ? 'active' : '' }}">
                                    <i class="icon-calendar3"></i> Rapport Mensuel
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Discipline & Comportement --}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ Route::is('discipline.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('discipline.*') ? 'active' : '' }}">
                            <i class="icon-balance"></i> <span>Discipline</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('discipline.index') }}" class="nav-link {{ Route::is('discipline.index') ? 'active' : '' }}">
                                    <i class="icon-list-unordered"></i> Registre
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('discipline.create') }}" class="nav-link {{ Route::is('discipline.create') ? 'active' : '' }}">
                                    <i class="icon-plus-circle2"></i> Enregistrer
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('discipline.class_report') }}" class="nav-link {{ Route::is('discipline.class_report') ? 'active' : '' }}">
                                    <i class="icon-stats-bars"></i> Rapport par Classe
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Calendrier Scolaire --}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ Route::is('calendar.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('calendar.*') ? 'active' : '' }}">
                            <i class="icon-calendar52"></i> <span>Calendrier</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('calendar.index') }}" class="nav-link {{ Route::is('calendar.index') ? 'active' : '' }}">
                                    <i class="icon-calendar3"></i> Vue Mensuelle
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('calendar.annual_view') }}" class="nav-link {{ Route::is('calendar.annual_view') ? 'active' : '' }}">
                                    <i class="icon-calendar22"></i> Vue Annuelle
                                </a>
                            </li>
                            @if(Qs::userIsTeamSA())
                            <li class="nav-item">
                                <a href="{{ route('calendar.create') }}" class="nav-link {{ Route::is('calendar.create') ? 'active' : '' }}">
                                    <i class="icon-plus-circle2"></i> Ajouter Événement
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Certificats & Attestations --}}
                @if(Qs::userIsTeamSA())
                    <li class="nav-item nav-item-submenu {{ Route::is('certificates.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('certificates.*') ? 'active' : '' }}">
                            <i class="icon-file-text2"></i> <span>Certificats</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('certificates.index') }}" class="nav-link {{ Route::is('certificates.index') ? 'active' : '' }}">
                                    <i class="icon-list-unordered"></i> Registre
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('certificates.create') }}" class="nav-link {{ Route::is('certificates.create') ? 'active' : '' }}">
                                    <i class="icon-plus-circle2"></i> Générer
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if(Qs::userIsTeamSA())
                    {{--Manage Users & Staff--}}
                    <li class="nav-item nav-item-submenu {{ Route::is('users.*') || Route::is('staff.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('users.*') || Route::is('staff.*') ? 'active' : '' }}">
                            <i class="icon-users4"></i> <span>Utilisateurs & Personnel</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ url('/users') }}" class="nav-link {{ Route::is('users.*') ? 'active' : '' }}">
                                    <i class="icon-user-check"></i> Comptes Utilisateurs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.index') }}" class="nav-link {{ Route::is('staff.*') ? 'active' : '' }}">
                                    <i class="icon-briefcase"></i> Personnel & Contrats
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{--Manage Classes--}}
                    <li class="nav-item">
                        <a href="{{ url('/classes') }}" class="nav-link"><i class="icon-windows2"></i> <span> Classes</span></a>
                    </li>

                    {{--Manage Sections--}}
                    <li class="nav-item">
                        <a href="{{ url('/sections') }}" class="nav-link"><i class="icon-fence"></i> <span>Section</span></a>
                    </li>

                    {{--Manage Subjects--}}
                    <li class="nav-item">
                        <a href="{{ url('/subjects') }}" class="nav-link"><i class="icon-pin"></i> <span>Matieres</span></a>
                    </li>

                    {{--Manage Sessions--}}
                    <li class="nav-item nav-item-submenu {{ Route::is('sessions.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('sessions.*') ? 'active' : '' }}">
                            <i class="icon-calendar"></i> <span>Années Scolaires</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('sessions.index') }}" class="nav-link {{ Route::is('sessions.index') ? 'active' : '' }}">
                                    Gestion des Sessions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sessions.closure.wizard') }}" class="nav-link {{ Route::is('sessions.closure.*') ? 'active' : '' }}">
                                    <i class="icon-lock5 text-warning"></i> Assistant de Clôture
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Bibliothèque --}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ Route::is('library.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('library.*') ? 'active' : '' }}">
                            <i class="icon-book"></i> <span>Bibliothèque</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('library.index') }}" class="nav-link {{ Route::is('library.index') ? 'active' : '' }}">
                                    <i class="icon-books"></i> Catalogue & Prêts
                                </a>
                            </li>
                            @if(Qs::userIsTeamSA())
                            <li class="nav-item">
                                <a href="{{ route('library.create') }}" class="nav-link {{ Route::is('library.create') ? 'active' : '' }}">
                                    <i class="icon-plus-circle2"></i> Ajouter un Livre
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Centre d'Impression & Rapports --}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ Route::is('print-center.*') || Route::is('reports.*') ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link {{ Route::is('print-center.*') || Route::is('reports.*') ? 'active' : '' }}">
                            <i class="icon-printer"></i> <span>Centre d'Impression</span>
                        </a>
                        <ul class="nav nav-group-sub">
                            <li class="nav-item">
                                <a href="{{ route('print-center.index') }}" class="nav-link {{ Route::is('print-center.*') ? 'active' : '' }}">
                                    <i class="icon-printer2"></i> Centre d'Impression
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reports.annual.index') }}" class="nav-link {{ Route::is('reports.annual.*') ? 'active' : '' }}">
                                    <i class="icon-stats-growth"></i> Rapport Annuel
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{--Exam--}}
                @if(Qs::userIsTeamSAT())
                <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['exams.index', 'exams.edit', 'grades.index', 'grades.edit', 'marks.index', 'marks.manage', 'marks.bulk', 'marks.tabulation', 'marks.show', 'marks.batch_fix',]) ? 'nav-item-expanded nav-item-open' : '' }} ">
                    <a href="#" class="nav-link"><i class="icon-books"></i> <span> Examens</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Manage Exams">
                        @if(Qs::userIsTeamSA())

                        {{--Exam list--}}
                            <li class="nav-item">
                                <a href="{{ url('/exams') }}"
                                   class="nav-link">Liste d'examens</a>
                            </li>

                            {{--Grades list--}}
                            <li class="nav-item">
                                    <a href="{{ url('/grades') }}"
                                       class="nav-link">Barem </a>
                            </li>

                            {{--Weighted Grades Tabulation--}}
                            <li class="nav-item">
                                <a href="{{ url('/marks/weighted-grades') }}" class="nav-link">Notes Pondérées</a>
                            </li>

                        @endif

                        @if(Qs::userIsTeamSAT())
                            {{--Marks Manage--}}
                            <li class="nav-item">
                                <a href="{{ url('/marks') }}"
                                   class="nav-link">Notes</a>
                            </li>

                            {{--Marksheet--}}
                            <li class="nav-item">
                                <a href="{{ url('/marks/bulk') }}" class="nav-link">Bulletin</a>
                            </li>

                            @endif

                    </ul>
                </li>
                @endif


                {{--End Exam--}}

                @include('pages.'.Qs::getUserType().'.menu')

                {{--Manage Account--}}
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link"><i class="icon-user"></i> <span>Mon compte</span></a>
                </li>

                </ul>
            </div>
        </div>
</div>
