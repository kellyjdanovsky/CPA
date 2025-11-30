<script>
// Gestion globale des erreurs de chargement de scripts
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'SCRIPT') {
        console.warn('Script non chargé:', e.target.src);
    }
}, true);
</script>

<!-- Theme JS files -->
<script src="{{ asset('global_assets/js/plugins/extensions/jquery_ui/interactions.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

{{--Forms--}}
<script src="{{ asset('global_assets/js/plugins/forms/wizards/steps.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/inputs/inputmask.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/forms/validation/validate.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/extensions/cookie.js') }}"></script>

{{--Notify--}}
<script type="text/javascript" src="{{ asset('global_assets/js/plugins/notifications/sweet_alert2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('global_assets/js/plugins/notifications/pnotify.min.js') }}"></script>

{{--DataTables--}}
<script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/tables/datatables/extensions/buttons.min.js') }}"></script>

{{--Date Pickers--}}
<script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/pickers/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('global_assets/js/plugins/pickers/pickadate/legacy.js') }}"></script>

{{--Uploaders--}}
<script src="{{ asset('global_assets/js/plugins/uploaders/fileinput/fileinput.min.js') }}"></script>

{{--Calendar--}}
<script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/fullcalendar.min.js') }}"></script>

<!-- Core App JS -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- Demo Pages (optionnel) -->
<script src="{{ asset('global_assets/js/demo_pages/form_wizard.js') }}" onerror="console.log('form_wizard.js non trouvé')"></script>
<script src="{{ asset('global_assets/js/demo_pages/form_select2.js') }}" onerror="console.log('form_select2.js non trouvé')"></script>
<script src="{{ asset('global_assets/js/demo_pages/datatables_extension_buttons_html5.js') }}" onerror="console.log('datatables buttons non trouvé')"></script>
<script src="{{ asset('global_assets/js/demo_pages/uploader_bootstrap.js') }}" onerror="console.log('uploader_bootstrap.js non trouvé')"></script>
<script src="{{ asset('global_assets/js/demo_pages/fullcalendar_basic.js') }}" onerror="console.log('fullcalendar_basic.js non trouvé')"></script>

<!-- Custom Scripts -->
<script src="{{ asset('assets/js/custom.js') }}"></script>

<!-- Modern UI Components -->
<script src="{{ asset('assets/js/modern-ui.js') }}"></script>

<!-- Theme Manager - Mode sombre/clair -->
<script src="{{ asset('assets/js/theme-manager.js') }}"></script>

<!-- Barème Manager - Gestion des remarques -->
<script src="{{ asset('assets/js/bareme-manager.js') }}"></script>

<!-- Phase 1: Dark Mode -->
<script src="{{ asset('assets/js/dark-mode.js') }}"></script>

<!-- Phase 1: Modern Notifications -->
<script src="{{ asset('assets/js/notifications.js') }}"></script>

<!-- Phase 2: Form Validation -->
<script src="{{ asset('assets/js/phase2-forms.js') }}"></script>

<!-- Phase 2: Global Search -->
<script src="{{ asset('assets/js/phase2-search.js') }}"></script>

    console.log('%c━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'color: #667eea; font-weight: bold;');
    
    // Phase 1
    if (typeof window.darkMode !== 'undefined') {
        console.log('%c✓ Dark Mode', 'color: #8b5cf6; font-weight: bold;');
    }
    if (typeof window.notify !== 'undefined') {
        console.log('%c✓ Modern Notifications', 'color: #10b981; font-weight: bold;');
    }
    
    // Phase 2
    if (typeof ModernFormValidator !== 'undefined') {
        console.log('%c✓ Form Validator', 'color: #f59e0b; font-weight: bold;');
    }
    if (typeof window.globalSearch !== 'undefined') {
        console.log('%c✓ Global Search', 'color: #06b6d4; font-weight: bold;');
    }
    
    // Phase 3
    if (typeof window.analytics !== 'undefined') {
        console.log('%c✓ Analytics Dashboard', 'color: #ec4899; font-weight: bold;');
    }
    if (typeof BulkActionsManager !== 'undefined') {
        console.log('%c✓ Bulk Actions', 'color: #8b5cf6; font-weight: bold;');
    }
    if (typeof Chart !== 'undefined') {
        console.log('%c✓ Chart.js', 'color: #f59e0b; font-weight: bold;');
    }
    
    // Legacy
    if (typeof ThemeManager !== 'undefined') {
        console.log('%c✓ Theme Manager', 'color: #6b7280; font-weight: bold;');
    }
    if (typeof BaremeManager !== 'undefined') {
        console.log('%c✓ Barème Manager', 'color: #6b7280; font-weight: bold;');
    }
    if (typeof jQuery !== 'undefined') {
        console.log('%c✓ jQuery ' + jQuery.fn.jquery, 'color: #3b82f6; font-weight: bold;');
    }
    
    console.log('%c━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'color: #667eea; font-weight: bold;');
    console.log('%c   Toutes les fonctionnalités sont prêtes!', 'color: #10b981; font-weight: bold;');
    console.log('%c━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'color: #667eea; font-weight: bold;');
    
    // Initialiser tooltips
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Initialiser popovers
    if (typeof $().popover === 'function') {
        $('[data-toggle="popover"]').popover();
    }
});
</script>

@include('partials.js.custom_js')