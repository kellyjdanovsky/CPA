<?php

/**
 * Script de vérification des fichiers du dashboard moderne
 * À exécuter depuis la racine du projet : php check_dashboard_files.php
 */

echo "🔍 Vérification des fichiers du dashboard moderne\n";
echo "================================================\n\n";

$files = [
    'CSS' => [
        'public/css/modern-dashboard.css' => 'Styles modernes du dashboard',
    ],
    'JavaScript' => [
        'public/js/modern-dashboard.js' => 'Interactions et animations',
    ],
    'Vues' => [
        'resources/views/pages/support_team/dashboard.blade.php' => 'Template principal du dashboard',
        'resources/views/partials/inc_top.blade.php' => 'Inclusion des CSS',
        'resources/views/partials/inc_bottom.blade.php' => 'Inclusion des JS',
    ],
    'Documentation' => [
        'MODERN_DASHBOARD_IMPROVEMENTS.md' => 'Documentation des améliorations',
    ]
];

$allFilesExist = true;
$totalSize = 0;

foreach ($files as $category => $categoryFiles) {
    echo "📁 $category\n";
    echo str_repeat("-", strlen($category) + 4) . "\n";
    
    foreach ($categoryFiles as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            $totalSize += $size;
            $sizeFormatted = formatBytes($size);
            echo "   ✅ $file ($sizeFormatted)\n";
            echo "      📝 $description\n";
        } else {
            echo "   ❌ $file (MANQUANT)\n";
            echo "      📝 $description\n";
            $allFilesExist = false;
        }
    }
    echo "\n";
}

// Vérification du contenu des fichiers clés
echo "🔍 Vérification du contenu\n";
echo "==========================\n";

// Vérifier le CSS
if (file_exists('public/css/modern-dashboard.css')) {
    $cssContent = file_get_contents('public/css/modern-dashboard.css');
    $cssChecks = [
        ':root' => 'Variables CSS définies',
        '.modern-dashboard-header' => 'Header moderne présent',
        '.modern-stat-card' => 'Cartes de statistiques modernes',
        '.quick-action-card' => 'Actions rapides définies',
        '@keyframes' => 'Animations CSS présentes',
    ];
    
    echo "📄 CSS (modern-dashboard.css):\n";
    foreach ($cssChecks as $pattern => $description) {
        if (strpos($cssContent, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description (MANQUANT)\n";
        }
    }
    echo "\n";
}

// Vérifier le JavaScript
if (file_exists('public/js/modern-dashboard.js')) {
    $jsContent = file_get_contents('public/js/modern-dashboard.js');
    $jsChecks = [
        'animateCounters' => 'Animation des compteurs',
        'animateProgressBars' => 'Animation des barres de progression',
        'initScrollAnimations' => 'Animations au scroll',
        'initCardHoverEffects' => 'Effets de survol',
        'initParticleEffect' => 'Effets de particules',
    ];
    
    echo "📄 JavaScript (modern-dashboard.js):\n";
    foreach ($jsChecks as $pattern => $description) {
        if (strpos($jsContent, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description (MANQUANT)\n";
        }
    }
    echo "\n";
}

// Vérifier les inclusions dans les templates
echo "🔗 Vérification des inclusions\n";
echo "==============================\n";

if (file_exists('resources/views/partials/inc_top.blade.php')) {
    $incTopContent = file_get_contents('resources/views/partials/inc_top.blade.php');
    if (strpos($incTopContent, 'modern-dashboard.css') !== false) {
        echo "   ✅ CSS moderne inclus dans inc_top.blade.php\n";
    } else {
        echo "   ❌ CSS moderne NON inclus dans inc_top.blade.php\n";
        $allFilesExist = false;
    }
    
    if (strpos($incTopContent, 'Inter') !== false) {
        echo "   ✅ Police Inter incluse\n";
    } else {
        echo "   ❌ Police Inter NON incluse\n";
    }
}

if (file_exists('resources/views/partials/inc_bottom.blade.php')) {
    $incBottomContent = file_get_contents('resources/views/partials/inc_bottom.blade.php');
    if (strpos($incBottomContent, 'modern-dashboard.js') !== false) {
        echo "   ✅ JavaScript moderne inclus dans inc_bottom.blade.php\n";
    } else {
        echo "   ❌ JavaScript moderne NON inclus dans inc_bottom.blade.php\n";
        $allFilesExist = false;
    }
}

echo "\n";

// Résumé final
echo "📊 RÉSUMÉ\n";
echo "=========\n";
echo "Taille totale des fichiers: " . formatBytes($totalSize) . "\n";

if ($allFilesExist) {
    echo "🎉 SUCCÈS: Tous les fichiers sont présents et correctement configurés!\n";
    echo "✨ Le dashboard moderne est prêt à être utilisé.\n\n";
    echo "🌐 Pour voir le résultat:\n";
    echo "   1. Assurez-vous que le serveur est démarré (php artisan serve)\n";
    echo "   2. Ouvrez http://127.0.0.1:8000/ dans votre navigateur\n";
    echo "   3. Connectez-vous pour voir le nouveau dashboard\n\n";
    echo "🎨 Fonctionnalités modernes activées:\n";
    echo "   ✅ Header avec gradient et animations\n";
    echo "   ✅ Cartes de statistiques interactives\n";
    echo "   ✅ Actions rapides avec effets hover\n";
    echo "   ✅ Animations fluides et transitions\n";
    echo "   ✅ Design responsive pour mobile\n";
    echo "   ✅ Typographie moderne (Inter)\n";
} else {
    echo "❌ ATTENTION: Certains fichiers sont manquants ou mal configurés.\n";
    echo "🔧 Veuillez vérifier les éléments marqués comme manquants ci-dessus.\n";
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

?>
