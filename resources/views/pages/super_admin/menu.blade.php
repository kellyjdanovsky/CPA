{{--Manage Settings--}}
<li class="nav-item">
    <a href="{{ route('settings') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['settings',]) ? 'active' : '' }}"><i class="icon-gear"></i> <span>Parametre</span></a>
</li>

{{--Sauvegardes & Base de données--}}
<li class="nav-item">
    <a href="{{ route('super_admin.backups') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['super_admin.backups',]) ? 'active' : '' }}"><i class="icon-database"></i> <span>Sauvegardes BD</span></a>
</li>

{{--Journal d'Activités & Audit--}}
<li class="nav-item">
    <a href="{{ route('activity-logs.index') }}" class="nav-link {{ Route::is('activity-logs.*') ? 'active' : '' }}"><i class="icon-history"></i> <span>Journal d'Activités</span></a>
</li>