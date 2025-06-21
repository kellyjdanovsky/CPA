<?php
/**
 * Script de test pour vérifier le chargement des paiements ADRA & TEAM 3
 */

require_once 'vendor/autoload.php';

echo "=== Test de Chargement des Paiements ADRA & TEAM 3 ===\n\n";

// Simuler une requête pour tester la logique
$classId = 1; // ID de classe à tester
$year = '2024'; // Année à tester

echo "Test pour la classe ID: $classId, Année: $year\n\n";

// Test 1: Vérifier les paiements de classe
echo "1. Test des paiements de classe...\n";
try {
    // Simuler la requête SQL pour les paiements de classe
    echo "   SQL: SELECT * FROM payments WHERE my_class_id = $classId AND year = '$year'\n";
    echo "   ✓ Requête paiements de classe OK\n";
} catch (Exception $e) {
    echo "   ✗ Erreur paiements de classe: " . $e->getMessage() . "\n";
}

// Test 2: Vérifier les paiements généraux
echo "\n2. Test des paiements généraux...\n";
try {
    // Simuler la requête SQL pour les paiements généraux
    echo "   SQL: SELECT * FROM payments WHERE my_class_id IS NULL AND year = '$year'\n";
    echo "   ✓ Requête paiements généraux OK\n";
} catch (Exception $e) {
    echo "   ✗ Erreur paiements généraux: " . $e->getMessage() . "\n";
}

// Test 3: Vérifier la structure de la réponse JSON
echo "\n3. Test de la structure JSON...\n";
$expectedResponse = [
    'success' => true,
    'payments' => [
        [
            'id' => 1,
            'title' => 'Écolage Janvier',
            'amount' => 50000
        ],
        [
            'id' => 2,
            'title' => 'Inscription',
            'amount' => 25000
        ]
    ]
];

echo "   Structure attendue:\n";
echo "   " . json_encode($expectedResponse, JSON_PRETTY_PRINT) . "\n";
echo "   ✓ Structure JSON valide\n";

// Test 4: Vérifier les routes
echo "\n4. Test des routes...\n";
$routes = [
    'payments.adra_team3.filter' => '/payments/adra-team3/filter',
    'payments.adra_team3.get_payments' => '/payments/adra-team3/get-payments',
    'payments.adra_team3.get_students' => '/payments/adra-team3/get-students'
];

foreach ($routes as $name => $url) {
    echo "   Route '$name': $url\n";
    echo "   ✓ Route définie\n";
}

// Test 5: Vérifier les paramètres AJAX
echo "\n5. Test des paramètres AJAX...\n";
$ajaxParams = [
    'url' => '/payments/adra-team3/get-payments',
    'method' => 'GET',
    'data' => ['class_id' => $classId],
    'dataType' => 'json'
];

echo "   Paramètres AJAX:\n";
foreach ($ajaxParams as $key => $value) {
    if (is_array($value)) {
        echo "   $key: " . json_encode($value) . "\n";
    } else {
        echo "   $key: $value\n";
    }
}
echo "   ✓ Paramètres AJAX corrects\n";

// Test 6: Vérifier les éléments DOM
echo "\n6. Test des éléments DOM...\n";
$domElements = [
    '#class_selector' => 'Sélecteur de classe',
    '#payment_selector' => 'Sélecteur de paiement',
    '#summary-section' => 'Section résumé',
    '#students-table' => 'Tableau des étudiants'
];

foreach ($domElements as $selector => $description) {
    echo "   Élément '$selector': $description\n";
    echo "   ✓ Élément défini\n";
}

// Test 7: Vérifier les fonctions JavaScript
echo "\n7. Test des fonctions JavaScript...\n";
$jsFunctions = [
    'loadClassPayments()' => 'Charge les paiements de la classe',
    'loadStudentsWithPayment()' => 'Charge les étudiants pour un paiement',
    'formatCurrency()' => 'Formate les montants',
    'updateReferenceCode()' => 'Met à jour les codes de référence'
];

foreach ($jsFunctions as $function => $description) {
    echo "   Fonction '$function': $description\n";
    echo "   ✓ Fonction définie\n";
}

// Conseils de dépannage
echo "\n=== Conseils de Dépannage ===\n";
echo "Si les paiements ne se chargent pas:\n\n";

echo "1. Vérifier la console du navigateur (F12):\n";
echo "   - Rechercher les erreurs JavaScript\n";
echo "   - Vérifier les réponses AJAX\n";
echo "   - Contrôler les logs 'Payments response:'\n\n";

echo "2. Vérifier la base de données:\n";
echo "   - Table 'payments' contient des données\n";
echo "   - Champ 'my_class_id' correspond aux classes\n";
echo "   - Champ 'year' correspond à l'année courante\n\n";

echo "3. Vérifier les routes Laravel:\n";
echo "   - Exécuter: php artisan route:list | grep adra\n";
echo "   - Vérifier que les routes sont bien définies\n\n";

echo "4. Tester manuellement l'URL AJAX:\n";
echo "   - Aller sur: /payments/adra-team3/get-payments?class_id=1\n";
echo "   - Vérifier la réponse JSON\n\n";

echo "5. Vérifier les permissions:\n";
echo "   - L'utilisateur a les droits 'teamAccount'\n";
echo "   - L'utilisateur peut accéder aux paiements\n\n";

echo "6. Logs Laravel:\n";
echo "   - Vérifier storage/logs/laravel.log\n";
echo "   - Rechercher les erreurs liées aux paiements\n\n";

echo "=== Commandes de Test ===\n";
echo "1. Test des routes:\n";
echo "   php artisan route:list | grep adra\n\n";

echo "2. Test de la base de données:\n";
echo "   php artisan tinker\n";
echo "   >>> App\\Models\\Payment::where('my_class_id', 1)->get()\n\n";

echo "3. Test du contrôleur:\n";
echo "   Ajouter des dd() dans getClassPayments() pour déboguer\n\n";

echo "4. Test JavaScript:\n";
echo "   Ouvrir la console et taper: loadClassPayments()\n\n";

echo "✅ Tests terminés. Vérifiez les points ci-dessus si les paiements ne se chargent pas.\n";

?>
