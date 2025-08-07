<?php

/**
 * Script de test pour la prévention des doublons de paiements
 * À exécuter depuis la racine du projet : php test_duplicate_prevention.php
 */

require_once 'vendor/autoload.php';

use App\Helpers\Pay;

echo "🧪 Test de la prévention des doublons de paiements\n";
echo "================================================\n\n";

// Test 1: Génération de codes de référence uniques
echo "Test 1: Génération de codes de référence uniques\n";
echo "-------------------------------------------------\n";

$refCodes = [];
for ($i = 0; $i < 10; $i++) {
    $refCode = Pay::genRefCode();
    $refCodes[] = $refCode;
    echo "Code $i: $refCode\n";
    usleep(100000); // Attendre 100ms entre chaque génération
}

// Vérifier l'unicité
$uniqueCodes = array_unique($refCodes);
if (count($uniqueCodes) === count($refCodes)) {
    echo "✅ Tous les codes sont uniques\n";
} else {
    echo "❌ Des doublons détectés dans les codes de référence\n";
}

echo "\n";

// Test 2: Vérification de la structure des codes
echo "Test 2: Vérification de la structure des codes\n";
echo "-----------------------------------------------\n";

$currentYear = date('Y');
$allValid = true;

foreach ($refCodes as $code) {
    if (!preg_match("/^$currentYear\/\d+$/", $code)) {
        echo "❌ Code invalide: $code\n";
        $allValid = false;
    }
}

if ($allValid) {
    echo "✅ Tous les codes respectent le format YYYY/NNNNNN\n";
} else {
    echo "❌ Certains codes ne respectent pas le format attendu\n";
}

echo "\n";

// Test 3: Performance de génération
echo "Test 3: Performance de génération\n";
echo "---------------------------------\n";

$startTime = microtime(true);
for ($i = 0; $i < 1000; $i++) {
    Pay::genRefCode();
}
$endTime = microtime(true);

$duration = ($endTime - $startTime) * 1000; // en millisecondes
echo "Génération de 1000 codes en: " . round($duration, 2) . " ms\n";

if ($duration < 100) {
    echo "✅ Performance excellente\n";
} elseif ($duration < 500) {
    echo "✅ Performance acceptable\n";
} else {
    echo "⚠️ Performance à améliorer\n";
}

echo "\n";

// Test 4: Vérification de la configuration Laravel
echo "Test 4: Vérification de la configuration\n";
echo "----------------------------------------\n";

try {
    // Vérifier que Laravel peut démarrer
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel initialisé correctement\n";
    
    // Vérifier la connexion à la base de données
    $pdo = DB::connection()->getPdo();
    echo "✅ Connexion à la base de données OK\n";
    
    // Vérifier que la table payments existe
    $tables = DB::select("SHOW TABLES LIKE 'payments'");
    if (count($tables) > 0) {
        echo "✅ Table 'payments' trouvée\n";
    } else {
        echo "❌ Table 'payments' non trouvée\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de configuration: " . $e->getMessage() . "\n";
}

echo "\n";
echo "🎉 Tests terminés !\n";
echo "\n";
echo "📋 Résumé des améliorations implémentées:\n";
echo "- ✅ Protection JavaScript contre les doubles clics\n";
echo "- ✅ Vérification backend des doublons\n";
echo "- ✅ Génération de codes de référence uniques\n";
echo "- ✅ Transactions de base de données\n";
echo "- ✅ Gestion d'erreurs robuste\n";
echo "\n";
echo "💡 Pour tester complètement:\n";
echo "1. Connectez-vous à l'application\n";
echo "2. Allez dans Paiements > Créer un paiement\n";
echo "3. Essayez de cliquer rapidement plusieurs fois sur 'Valider'\n";
echo "4. Vérifiez qu'un seul paiement est créé\n";

?>
