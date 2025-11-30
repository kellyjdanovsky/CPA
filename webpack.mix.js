const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | CPA - Mix Asset Management (Optimisé Phase 1)
 |--------------------------------------------------------------------------
 |
 | Configuration optimisée pour consolider et minifier tous les assets
 | Gains attendus: 40-60% de réduction du temps de chargement
 |
 */

// ========== CSS CONSOLIDATION ==========
// Styles principaux de l'application
mix.styles([
   'public/assets/css/bootstrap.min.css',
   'public/assets/css/bootstrap_limitless.min.css',
   'public/assets/css/layout.min.css',
   'public/assets/css/components.min.css',
   'public/assets/css/colors.min.css',
], 'public/dist/css/vendor.min.css');

// Styles modernes et personnalisés
mix.styles([
   'public/assets/css/modern-design-system.css',
   'public/assets/css/dashboard-pro.css',
   'public/assets/css/modern-components.css',
   'public/assets/css/modern-theme.css',
   'public/assets/css/dark-mode.css',
], 'public/dist/css/modern.min.css');

// Styles des fonctionnalités spécifiques
mix.styles([
   'public/assets/css/phase1-quickwins.css',
   'public/assets/css/phase2-datatables.css',
   'public/assets/css/phase2-forms.css',
   'public/assets/css/phase2-search.css',
   'public/assets/css/phase3-analytics.css',
], 'public/dist/css/features.min.css');

// Styles spécifiques
mix.styles([
   'public/assets/css/bareme.css',
   'public/assets/css/student_statistics.css',
   'public/assets/css/inline_editing.css',
   'public/assets/css/responsive.css',
], 'public/dist/css/modules.min.css');

// ========== JAVASCRIPT CONSOLIDATION ==========
// Libraires tierces
mix.scripts([
   'public/assets/js/jquery3.js',
], 'public/dist/js/vendor.min.js');

// Scripts modernes et UI
mix.scripts([
   'public/assets/js/modern-ui.js',
   'public/assets/js/dark-mode.js',
   'public/assets/js/theme-manager.js',
   'public/assets/js/notifications.js',
], 'public/dist/js/modern.min.js');

// Scripts des fonctionnalités
mix.scripts([
   'public/assets/js/phase2-forms.js',
   'public/assets/js/phase2-search.js',
   'public/assets/js/phase3-analytics.js',
   'public/assets/js/phase3-bulkactions.js',
], 'public/dist/js/features.min.js');

// Scripts des modules spécifiques
mix.scripts([
   'public/assets/js/bareme-manager.js',
   'public/assets/js/student_statistics.js',
   'public/assets/js/inline_editing.js',
   'public/assets/js/custom_datatables.js',
], 'public/dist/js/modules.min.js');

// ========== COMPILATION SASS (si utilisé) ==========
if (mix.inProduction()) {
   // En production: minification et versioning
   mix.version();
   mix.options({
      processCssUrls: false,
      postCss: [
         require('autoprefixer')({
            overrideBrowserslist: ['last 2 versions', 'ie >= 11']
         }),
         require('cssnano')({
            preset: ['default', {
               discardComments: { removeAll: true },
               normalizeWhitespace: true
            }]
         })
      ],
      terser: {
         terserOptions: {
            compress: {
               drop_console: true, // Enlever les console.log en prod
            }
         }
      }
   });
} else {
   // En développement: source maps pour debugging
   mix.sourceMaps();
}

// Désactiver les notifications de succès
mix.disableSuccessNotifications();

// Copier les assets non compilés (images, polices)
mix.copyDirectory('public/assets/images', 'public/dist/images');
mix.copyDirectory('public/assets/fonts', 'public/dist/fonts');
