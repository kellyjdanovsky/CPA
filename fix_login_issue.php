<?php

/**
 * Script de correction des problèmes de login après tinker
 * À exécuter depuis la racine du projet : php fix_login_issue.php
 */

echo "🔧 Correction des problèmes de login...\n";

// Nettoyer le cache
echo "1. Nettoyage du cache...\n";
exec('php artisan cache:clear', $output1, $return1);
if ($return1 === 0) {
    echo "   ✅ Cache nettoyé\n";
} else {
    echo "   ❌ Erreur lors du nettoyage du cache\n";
}

// Nettoyer les sessions
echo "2. Nettoyage des sessions...\n";
exec('php artisan session:flush', $output2, $return2);
if ($return2 === 0) {
    echo "   ✅ Sessions nettoyées\n";
} else {
    echo "   ⚠️ Commande session:flush non disponible, nettoyage manuel...\n";
    
    // Nettoyage manuel des sessions
    $sessionPath = 'storage/framework/sessions';
    if (is_dir($sessionPath)) {
        $files = glob($sessionPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✅ Sessions nettoyées manuellement\n";
    }
}

// Nettoyer les vues compilées
echo "3. Nettoyage des vues compilées...\n";
exec('php artisan view:clear', $output3, $return3);
if ($return3 === 0) {
    echo "   ✅ Vues nettoyées\n";
} else {
    echo "   ❌ Erreur lors du nettoyage des vues\n";
}

// Nettoyer la configuration
echo "4. Nettoyage de la configuration...\n";
exec('php artisan config:clear', $output4, $return4);
if ($return4 === 0) {
    echo "   ✅ Configuration nettoyée\n";
} else {
    echo "   ❌ Erreur lors du nettoyage de la configuration\n";
}

// Nettoyer les routes
echo "5. Nettoyage des routes...\n";
exec('php artisan route:clear', $output5, $return5);
if ($return5 === 0) {
    echo "   ✅ Routes nettoyées\n";
} else {
    echo "   ❌ Erreur lors du nettoyage des routes\n";
}

// Optimiser l'autoloader
echo "6. Optimisation de l'autoloader...\n";
exec('composer dump-autoload', $output6, $return6);
if ($return6 === 0) {
    echo "   ✅ Autoloader optimisé\n";
} else {
    echo "   ❌ Erreur lors de l'optimisation de l'autoloader\n";
}

echo "\n🎉 Nettoyage terminé !\n";
echo "💡 Conseils :\n";
echo "   - Fermez tous les onglets du navigateur\n";
echo "   - Videz le cache du navigateur\n";
echo "   - Redémarrez le serveur web si nécessaire\n";
echo "   - Essayez de vous reconnecter\n";

?>
