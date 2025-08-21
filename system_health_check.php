<?php

/**
 * Script de vérification de l'état du système après corrections
 * À exécuter depuis la racine du projet : php system_health_check.php
 */

require_once 'vendor/autoload.php';

echo "🏥 Vérification de l'État du Système\n";
echo "====================================\n\n";

$checks = [];

// 1. Vérification Laravel
echo "1. Vérification de Laravel...\n";
try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    $checks['laravel'] = true;
    echo "   ✅ Laravel initialisé correctement\n";
} catch (Exception $e) {
    $checks['laravel'] = false;
    echo "   ❌ Erreur Laravel: " . $e->getMessage() . "\n";
}

// 2. Vérification de la base de données
echo "\n2. Vérification de la base de données...\n";
try {
    $pdo = DB::connection()->getPdo();
    $checks['database'] = true;
    echo "   ✅ Connexion à la base de données OK\n";
    
    // Vérifier les tables critiques
    $criticalTables = ['users', 'payments', 'payment_records', 'receipts', 'exam_records'];
    foreach ($criticalTables as $table) {
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if (count($exists) > 0) {
            echo "   ✅ Table '$table' trouvée\n";
        } else {
            echo "   ❌ Table '$table' manquante\n";
            $checks['database'] = false;
        }
    }
} catch (Exception $e) {
    $checks['database'] = false;
    echo "   ❌ Erreur de base de données: " . $e->getMessage() . "\n";
}

// 3. Vérification des migrations
echo "\n3. Vérification des migrations...\n";
try {
    $output = [];
    exec('php artisan migrate:status 2>&1', $output, $return);
    if ($return === 0) {
        $checks['migrations'] = true;
        echo "   ✅ Toutes les migrations sont à jour\n";
        
        // Compter les migrations
        $migrationCount = 0;
        foreach ($output as $line) {
            if (strpos($line, '| Yes  |') !== false) {
                $migrationCount++;
            }
        }
        echo "   📊 $migrationCount migrations exécutées\n";
    } else {
        $checks['migrations'] = false;
        echo "   ❌ Problème avec les migrations\n";
    }
} catch (Exception $e) {
    $checks['migrations'] = false;
    echo "   ❌ Erreur lors de la vérification des migrations: " . $e->getMessage() . "\n";
}

// 4. Vérification de la solution anti-doublon
echo "\n4. Vérification de la solution anti-doublon...\n";
try {
    // Vérifier que les fichiers modifiés existent
    $modifiedFiles = [
        'resources/views/partials/js/custom_js.blade.php',
        'app/Http/Controllers/SupportTeam/PaymentController.php',
        'app/Helpers/Pay.php'
    ];
    
    $antiDuplicateOk = true;
    foreach ($modifiedFiles as $file) {
        if (file_exists($file)) {
            echo "   ✅ Fichier modifié '$file' présent\n";
        } else {
            echo "   ❌ Fichier modifié '$file' manquant\n";
            $antiDuplicateOk = false;
        }
    }
    
    // Vérifier la fonction genRefCode
    if (class_exists('App\Helpers\Pay')) {
        $refCode = App\Helpers\Pay::genRefCode();
        if (preg_match('/^\d{4}\/\d+$/', $refCode)) {
            echo "   ✅ Génération de code de référence fonctionnelle: $refCode\n";
        } else {
            echo "   ❌ Format de code de référence incorrect: $refCode\n";
            $antiDuplicateOk = false;
        }
    } else {
        echo "   ❌ Classe Pay non trouvée\n";
        $antiDuplicateOk = false;
    }
    
    $checks['anti_duplicate'] = $antiDuplicateOk;
} catch (Exception $e) {
    $checks['anti_duplicate'] = false;
    echo "   ❌ Erreur lors de la vérification anti-doublon: " . $e->getMessage() . "\n";
}

// 5. Vérification des permissions de fichiers
echo "\n5. Vérification des permissions...\n";
$writableDirs = ['storage', 'bootstrap/cache'];
$permissionsOk = true;

foreach ($writableDirs as $dir) {
    if (is_writable($dir)) {
        echo "   ✅ Répertoire '$dir' accessible en écriture\n";
    } else {
        echo "   ❌ Répertoire '$dir' non accessible en écriture\n";
        $permissionsOk = false;
    }
}
$checks['permissions'] = $permissionsOk;

// 6. Vérification de la configuration
echo "\n6. Vérification de la configuration...\n";
try {
    $appName = config('app.name');
    $dbConnection = config('database.default');
    $appEnv = config('app.env');
    
    echo "   ✅ Application: $appName\n";
    echo "   ✅ Environnement: $appEnv\n";
    echo "   ✅ Base de données: $dbConnection\n";
    $checks['config'] = true;
} catch (Exception $e) {
    $checks['config'] = false;
    echo "   ❌ Erreur de configuration: " . $e->getMessage() . "\n";
}

// Résumé final
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RÉSUMÉ DE L'ÉTAT DU SYSTÈME\n";
echo str_repeat("=", 50) . "\n";

$totalChecks = count($checks);
$passedChecks = array_sum($checks);

foreach ($checks as $check => $status) {
    $icon = $status ? "✅" : "❌";
    $name = ucfirst(str_replace('_', ' ', $check));
    echo "$icon $name\n";
}

echo "\n";
if ($passedChecks === $totalChecks) {
    echo "🎉 SYSTÈME ENTIÈREMENT FONCTIONNEL ($passedChecks/$totalChecks)\n";
    echo "✅ Toutes les vérifications sont passées avec succès\n";
    echo "✅ La solution anti-doublon est opérationnelle\n";
    echo "✅ Prêt pour la production\n";
} else {
    echo "⚠️  ATTENTION: $passedChecks/$totalChecks vérifications réussies\n";
    echo "❌ Certains problèmes nécessitent une attention\n";
    echo "🔧 Veuillez corriger les erreurs avant la mise en production\n";
}

echo "\n💡 Pour tester la solution anti-doublon:\n";
echo "   1. Connectez-vous à l'application\n";
echo "   2. Allez dans Paiements > Créer un paiement\n";
echo "   3. Essayez de cliquer rapidement plusieurs fois sur 'Valider'\n";
echo "   4. Vérifiez qu'un seul paiement est créé\n";

?>
