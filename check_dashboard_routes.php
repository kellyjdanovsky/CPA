<?php

/**
 * Script de vérification des routes du dashboard
 * À exécuter depuis la racine du projet : php check_dashboard_routes.php
 */

require_once 'vendor/autoload.php';

echo "🔍 Vérification des routes du dashboard moderne\n";
echo "===============================================\n\n";

try {
    // Initialiser Laravel
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel initialisé avec succès\n\n";
    
    // Routes à vérifier
    $routesToCheck = [
        'students.create' => 'Création d\'un nouvel élève',
        'payments.create' => 'Création d\'un nouveau paiement',
        'marks.index' => 'Gestion des notes',
        'users.index' => 'Gestion des utilisateurs',
        'home' => 'Page d\'accueil/dashboard',
        'dashboard' => 'Dashboard alternatif'
    ];
    
    echo "📋 Vérification des routes utilisées dans le dashboard :\n";
    echo str_repeat("-", 55) . "\n";
    
    $allRoutesExist = true;
    
    foreach ($routesToCheck as $routeName => $description) {
        try {
            // Vérifier si la route existe
            $url = route($routeName);
            echo "   ✅ $routeName : $description\n";
            echo "      🔗 URL: $url\n";
        } catch (\Exception $e) {
            echo "   ❌ $routeName : $description\n";
            echo "      ⚠️  Erreur: " . $e->getMessage() . "\n";
            $allRoutesExist = false;
        }
    }
    
    echo "\n";
    
    // Vérifier les routes avec paramètres optionnels
    echo "📋 Vérification des routes avec paramètres optionnels :\n";
    echo str_repeat("-", 55) . "\n";
    
    $optionalRoutes = [
        'payments.manage' => 'Gestion des paiements (avec classe optionnelle)',
        'marks.bulk' => 'Saisie en lot des notes',
        'marks.tabulation' => 'Tabulation des notes'
    ];
    
    foreach ($optionalRoutes as $routeName => $description) {
        try {
            // Essayer sans paramètres
            $url = route($routeName);
            echo "   ✅ $routeName : $description\n";
            echo "      🔗 URL: $url\n";
        } catch (\Exception $e) {
            echo "   ⚠️  $routeName : $description\n";
            echo "      📝 Note: Nécessite des paramètres - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    
    // Lister toutes les routes disponibles (optionnel)
    echo "📋 Quelques routes disponibles dans l'application :\n";
    echo str_repeat("-", 55) . "\n";
    
    $router = app('router');
    $routes = $router->getRoutes();
    $routeCount = 0;
    
    foreach ($routes as $route) {
        if ($routeCount < 10) { // Limiter l'affichage
            $name = $route->getName();
            $uri = $route->uri();
            $methods = implode('|', $route->methods());
            
            if ($name && !str_contains($name, 'generated::')) {
                echo "   🔗 $name ($methods) : /$uri\n";
                $routeCount++;
            }
        }
    }
    
    echo "   ... et " . (count($routes) - $routeCount) . " autres routes\n\n";
    
    // Résumé final
    if ($allRoutesExist) {
        echo "🎉 SUCCÈS : Toutes les routes du dashboard sont accessibles !\n";
        echo "✨ Le dashboard moderne peut être utilisé sans problème.\n\n";
        echo "🌐 Pour tester :\n";
        echo "   1. Ouvrez http://127.0.0.1:8000/ dans votre navigateur\n";
        echo "   2. Connectez-vous avec vos identifiants\n";
        echo "   3. Testez les actions rapides du dashboard\n";
        echo "   4. Vérifiez que tous les liens fonctionnent\n\n";
        echo "🎨 Fonctionnalités du dashboard moderne :\n";
        echo "   ✅ Header avec gradient et animations\n";
        echo "   ✅ Cartes de statistiques interactives\n";
        echo "   ✅ Actions rapides fonctionnelles\n";
        echo "   ✅ Design responsive\n";
        echo "   ✅ Thème sombre/clair\n";
    } else {
        echo "⚠️  ATTENTION : Certaines routes ne sont pas accessibles.\n";
        echo "🔧 Vérifiez les routes marquées comme problématiques ci-dessus.\n";
        echo "💡 Le dashboard peut fonctionner partiellement, mais certains liens peuvent ne pas marcher.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors de l'initialisation de Laravel :\n";
    echo "   " . $e->getMessage() . "\n";
    echo "🔧 Vérifiez que :\n";
    echo "   - Le serveur Laravel est démarré (php artisan serve)\n";
    echo "   - La base de données est accessible\n";
    echo "   - Les fichiers de configuration sont corrects\n";
}

echo "\n";
echo "📝 Note : Ce script vérifie uniquement l'existence des routes.\n";
echo "   Pour un test complet, connectez-vous à l'application et testez manuellement.\n";

?>
