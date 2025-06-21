<?php
/**
 * Script de diagnostic complet pour ADRA & TEAM 3
 * Exécuter avec: php diagnostic_adra_team3.php
 */

echo "=== DIAGNOSTIC COMPLET ADRA & TEAM 3 ===\n\n";

// Test 1: Vérifier les routes
echo "1. VÉRIFICATION DES ROUTES\n";
echo "==========================\n";

$routeCommands = [
    'php artisan route:list | grep adra',
    'php artisan route:cache',
    'php artisan route:clear'
];

foreach ($routeCommands as $command) {
    echo "Commande: $command\n";
    if (strpos($command, 'grep') !== false) {
        echo "   (Vérifier manuellement que les routes ADRA sont listées)\n";
    } else {
        echo "   (Exécuter cette commande pour nettoyer le cache)\n";
    }
}

// Test 2: Vérifier les fichiers
echo "\n2. VÉRIFICATION DES FICHIERS\n";
echo "=============================\n";

$files = [
    'routes/web.php' => 'Routes définies',
    'app/Http/Controllers/SupportTeam/PaymentController.php' => 'Contrôleur principal',
    'resources/views/pages/support_team/payments/adra_team3_filter.blade.php' => 'Vue principale'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $file - $description\n";
    } else {
        echo "❌ $file - MANQUANT!\n";
    }
}

// Test 3: Vérifier les méthodes du contrôleur
echo "\n3. VÉRIFICATION DES MÉTHODES\n";
echo "============================\n";

$controllerFile = 'app/Http/Controllers/SupportTeam/PaymentController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $methods = [
        'adraTeam3Filter' => 'Méthode principale',
        'getClassPayments' => 'Récupération des paiements AJAX',
        'getPaymentStudents' => 'Récupération des étudiants AJAX',
        'printAdraTeam3Receipt' => 'Impression des reçus'
    ];
    
    foreach ($methods as $method => $description) {
        if (strpos($content, "function $method") !== false) {
            echo "✅ $method() - $description\n";
        } else {
            echo "❌ $method() - MANQUANTE!\n";
        }
    }
} else {
    echo "❌ Contrôleur non trouvé!\n";
}

// Test 4: Vérifier la structure JavaScript
echo "\n4. VÉRIFICATION JAVASCRIPT\n";
echo "===========================\n";

$viewFile = 'resources/views/pages/support_team/payments/adra_team3_filter.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $jsFunctions = [
        'loadClassPayments' => 'Chargement des paiements',
        'loadStudentsWithPayment' => 'Chargement des étudiants',
        'formatCurrency' => 'Formatage des montants'
    ];
    
    foreach ($jsFunctions as $function => $description) {
        if (strpos($content, "function $function") !== false) {
            echo "✅ $function() - $description\n";
        } else {
            echo "❌ $function() - MANQUANTE!\n";
        }
    }
    
    // Vérifier les événements jQuery
    if (strpos($content, "$(document).ready") !== false) {
        echo "✅ $(document).ready - Initialisation jQuery\n";
    } else {
        echo "❌ $(document).ready - MANQUANT!\n";
    }
    
    if (strpos($content, ".on('change'") !== false) {
        echo "✅ Event handlers - Gestionnaires d'événements\n";
    } else {
        echo "❌ Event handlers - MANQUANTS!\n";
    }
} else {
    echo "❌ Vue non trouvée!\n";
}

// Test 5: URLs de test
echo "\n5. URLS DE TEST\n";
echo "================\n";

$testUrls = [
    '/payments/adra-team3/filter' => 'Page principale',
    '/payments/adra-team3/get-payments?class_id=1' => 'AJAX paiements',
    '/payments/adra-team3/get-students?class_id=1&payment_id=1' => 'AJAX étudiants'
];

foreach ($testUrls as $url => $description) {
    echo "🔗 $url\n";
    echo "   $description\n";
    echo "   Test: curl -s http://localhost:8000$url\n\n";
}

// Test 6: Commandes de dépannage
echo "6. COMMANDES DE DÉPANNAGE\n";
echo "==========================\n";

$commands = [
    'php artisan route:clear' => 'Nettoyer le cache des routes',
    'php artisan config:clear' => 'Nettoyer le cache de config',
    'php artisan view:clear' => 'Nettoyer le cache des vues',
    'php artisan cache:clear' => 'Nettoyer tous les caches',
    'composer dump-autoload' => 'Régénérer l\'autoload',
    'php artisan serve' => 'Redémarrer le serveur'
];

foreach ($commands as $command => $description) {
    echo "$command\n";
    echo "   → $description\n\n";
}

// Test 7: Vérification de la base de données
echo "7. VÉRIFICATION BASE DE DONNÉES\n";
echo "================================\n";

echo "Commandes à exécuter dans php artisan tinker:\n\n";

$tinkerCommands = [
    'App\\Models\\MyClass::all()' => 'Vérifier les classes',
    'App\\Models\\Payment::take(5)->get()' => 'Vérifier les paiements',
    'App\\Models\\User::whereIn("status", ["ADRA", "TEAM3"])->count()' => 'Compter les utilisateurs ADRA/TEAM3'
];

foreach ($tinkerCommands as $command => $description) {
    echo ">>> $command\n";
    echo "    # $description\n\n";
}

// Test 8: Checklist de validation
echo "8. CHECKLIST DE VALIDATION\n";
echo "===========================\n";

$checklist = [
    'Routes définies et accessibles',
    'Méthodes du contrôleur présentes',
    'Vue principale existe',
    'JavaScript correctement structuré',
    'jQuery et Select2 chargés',
    'Base de données contient des données',
    'Cache Laravel nettoyé',
    'Serveur redémarré'
];

foreach ($checklist as $item) {
    echo "☐ $item\n";
}

echo "\n=== ÉTAPES DE RÉSOLUTION ===\n";
echo "1. Exécuter toutes les commandes de dépannage\n";
echo "2. Tester les URLs manuellement\n";
echo "3. Vérifier la console du navigateur (F12)\n";
echo "4. Ouvrir test_javascript_adra_team3.html pour tester le JS\n";
echo "5. Vérifier les logs Laravel (storage/logs/laravel.log)\n";

echo "\n✅ Diagnostic terminé!\n";

?>
