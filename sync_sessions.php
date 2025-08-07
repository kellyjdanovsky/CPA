<?php

/**
 * Script de synchronisation des sessions
 * À exécuter depuis la racine du projet : php sync_sessions.php
 */

require_once 'vendor/autoload.php';

echo "🔄 Synchronisation des sessions\n";
echo "===============================\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Récupérer les sessions actuelles
    $sessionSettings = \App\Helpers\Qs::getCurrentSession();
    $activeSession = \App\Models\Session::getActive();
    $sessionTable = $activeSession ? $activeSession->name : null;
    
    echo "📊 État actuel :\n";
    echo "   Settings current_session: {$sessionSettings}\n";
    echo "   Session active table: " . ($sessionTable ?: 'Aucune') . "\n\n";
    
    // Vérifier la synchronisation
    if ($sessionSettings === $sessionTable) {
        echo "✅ DÉJÀ SYNCHRONISÉES: Aucune action nécessaire\n";
        echo "🎯 Le dashboard affichera: {$sessionSettings}\n";
    } else {
        echo "⚠️  DIFFÉRENTES: Synchronisation nécessaire\n\n";
        
        echo "🔧 Options de synchronisation :\n";
        echo "   1. Utiliser la session des settings ({$sessionSettings}) - RECOMMANDÉ\n";
        echo "   2. Utiliser la session active de la table ({$sessionTable})\n";
        echo "   3. Laisser tel quel (le dashboard suivra les settings)\n\n";
        
        // Option recommandée : utiliser les settings comme référence
        echo "🎯 CHOIX AUTOMATIQUE: Utiliser la session des settings\n";
        echo "💡 Raison: Le système utilise les settings comme référence principale\n\n";
        
        // Mettre à jour la session active dans la table pour correspondre aux settings
        if ($sessionTable && $sessionTable !== $sessionSettings) {
            // Désactiver l'ancienne session
            if ($activeSession) {
                $activeSession->update(['is_active' => false]);
                echo "   ✅ Ancienne session ({$sessionTable}) désactivée\n";
            }
            
            // Chercher ou créer la session correspondant aux settings
            $newActiveSession = \App\Models\Session::where('name', $sessionSettings)->first();
            
            if ($newActiveSession) {
                $newActiveSession->update(['is_active' => true]);
                echo "   ✅ Session ({$sessionSettings}) activée dans la table\n";
            } else {
                // Créer la session si elle n'existe pas
                $newActiveSession = \App\Models\Session::create([
                    'name' => $sessionSettings,
                    'start_date' => now()->month >= 9 ? now()->setMonth(9)->setDay(1) : now()->subYear()->setMonth(9)->setDay(1),
                    'end_date' => now()->month >= 9 ? now()->addYear()->setMonth(6)->setDay(30) : now()->setMonth(6)->setDay(30),
                    'is_active' => true,
                    'description' => "Année scolaire {$sessionSettings} (Créée automatiquement)"
                ]);
                echo "   ✅ Session ({$sessionSettings}) créée et activée\n";
            }
        }
        
        echo "\n🎉 SYNCHRONISATION TERMINÉE !\n";
    }
    
    echo "\n📋 RÉSUMÉ FINAL :\n";
    echo str_repeat("-", 20) . "\n";
    
    // Vérifier l'état final
    $finalSessionSettings = \App\Helpers\Qs::getCurrentSession();
    $finalActiveSession = \App\Models\Session::getActive();
    $finalSessionTable = $finalActiveSession ? $finalActiveSession->name : 'Aucune';
    
    echo "Settings: {$finalSessionSettings}\n";
    echo "Table: {$finalSessionTable}\n";
    echo "Dashboard affichera: {$finalSessionSettings}\n";
    
    if ($finalSessionSettings === $finalSessionTable) {
        echo "✅ PARFAITEMENT SYNCHRONISÉES !\n";
    } else {
        echo "ℹ️  Le dashboard suit les settings (comportement normal)\n";
    }
    
    echo "\n🌐 Pour tester :\n";
    echo "   1. Ouvrez http://127.0.0.1:8000/\n";
    echo "   2. Vérifiez que toutes les années affichent: {$finalSessionSettings}\n";
    echo "   3. Utilisez le dropdown dans le header pour changer d'année\n";
    echo "   4. Ou modifiez dans les settings: http://127.0.0.1:8000/super_admin/settings\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

?>
