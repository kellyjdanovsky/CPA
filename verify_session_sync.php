<?php

/**
 * Script de vérification de la synchronisation des sessions
 * À exécuter depuis la racine du projet : php verify_session_sync.php
 */

require_once 'vendor/autoload.php';

echo "🔍 Vérification de la synchronisation des sessions\n";
echo "=================================================\n\n";

try {
    // Initialiser Laravel
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel initialisé avec succès\n\n";
    
    // Test 1: Session active dans la table sessions
    echo "📅 Test 1: Session active dans la table sessions\n";
    echo str_repeat("-", 45) . "\n";
    
    $activeSession = \App\Models\Session::getActive();
    if ($activeSession) {
        echo "   ✅ Session active trouvée: {$activeSession->name}\n";
        echo "   📝 Description: {$activeSession->description}\n";
        echo "   📅 Période: {$activeSession->start_date} → {$activeSession->end_date}\n";
        $sessionFromTable = $activeSession->name;
    } else {
        echo "   ⚠️  Aucune session active dans la table sessions\n";
        $sessionFromTable = null;
    }
    echo "\n";
    
    // Test 2: Session depuis les settings
    echo "⚙️  Test 2: Session depuis les settings\n";
    echo str_repeat("-", 35) . "\n";
    
    $sessionFromSettings = \App\Helpers\Qs::getCurrentSession();
    echo "   📊 Session depuis settings: {$sessionFromSettings}\n";
    echo "\n";
    
    // Test 3: Comparaison des sessions
    echo "🔄 Test 3: Comparaison des sessions\n";
    echo str_repeat("-", 35) . "\n";
    
    if ($sessionFromTable && $sessionFromSettings) {
        if ($sessionFromTable === $sessionFromSettings) {
            echo "   ✅ PARFAIT: Les sessions sont synchronisées !\n";
            echo "      Session table: {$sessionFromTable}\n";
            echo "      Session settings: {$sessionFromSettings}\n";
        } else {
            echo "   ⚠️  ATTENTION: Les sessions ne sont pas synchronisées\n";
            echo "      Session table: {$sessionFromTable}\n";
            echo "      Session settings: {$sessionFromSettings}\n";
            echo "   💡 Recommandation: Mettre à jour les settings pour correspondre à la table\n";
        }
    } else {
        echo "   ℹ️  Une des sources de session est manquante\n";
        if ($sessionFromTable) {
            echo "      ✅ Session table: {$sessionFromTable}\n";
        }
        if ($sessionFromSettings) {
            echo "      ✅ Session settings: {$sessionFromSettings}\n";
        }
    }
    echo "\n";
    
    // Test 4: Vérification du dashboard
    echo "🎨 Test 4: Variables passées au dashboard\n";
    echo str_repeat("-", 40) . "\n";
    
    // Simuler l'appel du contrôleur
    $userRepo = app(\App\Repositories\UserRepo::class);
    $classRepo = app(\App\Repositories\MyClassRepo::class);
    $studentRepo = app(\App\Repositories\StudentRepo::class);
    
    $controller = new \App\Http\Controllers\HomeController($userRepo, $classRepo, $studentRepo);
    
    // Utiliser la réflexion pour accéder aux méthodes privées
    $reflection = new ReflectionClass($controller);
    
    // Simuler les données du dashboard
    $currentSession = $activeSession ? $activeSession->name : $sessionFromSettings;
    echo "   📊 Session qui sera affichée: {$currentSession}\n";
    
    // Vérifier les calculs
    $classes = \App\Models\MyClass::all();
    $teachers = \App\User::where('user_type', 'teacher')->get();
    $activeStudents = \App\Models\StudentRecord::where('session', $currentSession)->get();
    $examRecords = \App\Models\ExamRecord::where('year', $currentSession)->get();
    
    echo "   🏫 Nombre de classes: {$classes->count()}\n";
    echo "   👨‍🏫 Nombre d'enseignants: {$teachers->count()}\n";
    echo "   👨‍🎓 Nombre d'élèves actifs: {$activeStudents->count()}\n";
    echo "   📊 Enregistrements d'examen: {$examRecords->count()}\n";
    
    // Calculer le taux de réussite
    if ($examRecords->count() > 0) {
        $successfulDecisions = ['Passant', 'Promu', 'Admis'];
        $successfulCount = $examRecords->whereIn('decision', $successfulDecisions)->count();
        $successRate = round(($successfulCount / $examRecords->count()) * 100, 1);
        echo "   📈 Taux de réussite: {$successRate}%\n";
    } else {
        echo "   📈 Taux de réussite: Valeur par défaut (85%)\n";
    }
    echo "\n";
    
    // Test 5: Calcul du pourcentage d'avancement
    echo "📊 Test 5: Calcul du pourcentage d'avancement\n";
    echo str_repeat("-", 45) . "\n";
    
    $currentDate = now();
    $startDate = $currentDate->month >= 9 ? 
        $currentDate->copy()->setMonth(9)->setDay(1) : 
        $currentDate->copy()->subYear()->setMonth(9)->setDay(1);
    $endDate = $currentDate->month >= 9 ? 
        $currentDate->copy()->addYear()->setMonth(6)->setDay(30) : 
        $currentDate->copy()->setMonth(6)->setDay(30);
    
    $totalDays = $startDate->diffInDays($endDate);
    $elapsedDays = $startDate->diffInDays($currentDate);
    $progressPercent = $totalDays > 0 ? min(round(($elapsedDays / $totalDays) * 100), 100) : 0;
    
    echo "   📅 Date actuelle: {$currentDate->format('d/m/Y')}\n";
    echo "   🏁 Début année scolaire: {$startDate->format('d/m/Y')}\n";
    echo "   🎯 Fin année scolaire: {$endDate->format('d/m/Y')}\n";
    echo "   📊 Jours total: {$totalDays}\n";
    echo "   ⏱️  Jours écoulés: {$elapsedDays}\n";
    echo "   📈 Pourcentage d'avancement: {$progressPercent}%\n";
    echo "\n";
    
    // Résumé final
    echo "📋 RÉSUMÉ DE LA VÉRIFICATION\n";
    echo str_repeat("=", 30) . "\n";
    
    if ($sessionFromTable && $sessionFromSettings && $sessionFromTable === $sessionFromSettings) {
        echo "🎉 EXCELLENT: Tout est parfaitement synchronisé !\n\n";
        echo "✅ Session active: {$currentSession}\n";
        echo "✅ Synchronisation: Parfaite\n";
        echo "✅ Dashboard: Affichera la bonne session\n";
        echo "✅ Calculs: Basés sur les bonnes données\n";
        echo "✅ Avancement: {$progressPercent}% de l'année écoulé\n\n";
        echo "🌐 Le dashboard affichera partout: \"{$currentSession}\"\n";
    } else {
        echo "⚠️  ATTENTION: Synchronisation à vérifier\n\n";
        echo "📊 Session qui sera affichée: {$currentSession}\n";
        echo "💡 Recommandations:\n";
        echo "   1. Vérifier les settings de session\n";
        echo "   2. S'assurer qu'une session est marquée comme active\n";
        echo "   3. Tester le dashboard pour confirmer l'affichage\n";
    }
    
    echo "\n🎯 Pour tester: Ouvrez http://127.0.0.1:8000/ et vérifiez que toutes les mentions d'année scolaire affichent: \"{$currentSession}\"\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors de la vérification :\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

?>
