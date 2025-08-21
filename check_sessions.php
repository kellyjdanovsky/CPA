<?php

require_once 'vendor/autoload.php';

echo "🔍 Vérification des sessions\n";
echo "============================\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Session depuis les settings
    $sessionSettings = \App\Helpers\Qs::getCurrentSession();
    echo "📊 Session settings: {$sessionSettings}\n";
    
    // Session active depuis la table
    $activeSession = \App\Models\Session::getActive();
    $sessionTable = $activeSession ? $activeSession->name : 'Aucune';
    echo "📅 Session active table: {$sessionTable}\n";
    
    // Vérifier la synchronisation
    if ($sessionSettings === $sessionTable) {
        echo "✅ SYNCHRONISÉES: Les sessions sont identiques\n";
    } else {
        echo "⚠️  DIFFÉRENTES: Les sessions ne correspondent pas\n";
        echo "💡 Recommandation: Synchroniser les sessions\n";
    }
    
    // Afficher le setting current_session
    $currentSessionSetting = \App\Models\Setting::where('type', 'current_session')->first();
    if ($currentSessionSetting) {
        echo "⚙️  Setting current_session: {$currentSessionSetting->description}\n";
    } else {
        echo "❌ Setting current_session manquant\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

?>
