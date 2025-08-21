<?php

/**
 * Script de test pour vérifier les données du dashboard
 * À exécuter depuis la racine du projet : php test_dashboard_data.php
 */

require_once 'vendor/autoload.php';

echo "🔍 Test des données du dashboard moderne\n";
echo "========================================\n\n";

try {
    // Initialiser Laravel
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel initialisé avec succès\n\n";
    
    // Test 1: Session active
    echo "📅 Test 1: Session active\n";
    echo str_repeat("-", 25) . "\n";
    
    $currentSession = \App\Models\Session::getActive();
    if ($currentSession) {
        echo "   ✅ Session active trouvée: {$currentSession->name}\n";
        echo "   📝 Description: {$currentSession->description}\n";
        echo "   📅 Période: {$currentSession->start_date} → {$currentSession->end_date}\n";
    } else {
        $fallbackSession = \App\Helpers\Qs::getCurrentSession();
        echo "   ⚠️  Aucune session active dans la table sessions\n";
        echo "   🔄 Session de fallback: {$fallbackSession}\n";
    }
    echo "\n";
    
    // Test 2: Nombre de classes
    echo "🏫 Test 2: Classes\n";
    echo str_repeat("-", 15) . "\n";
    
    $classes = \App\Models\MyClass::all();
    echo "   📊 Nombre total de classes: {$classes->count()}\n";
    
    if ($classes->count() > 0) {
        echo "   📋 Liste des classes:\n";
        foreach ($classes->take(5) as $class) {
            echo "      - {$class->name} (ID: {$class->id})\n";
        }
        if ($classes->count() > 5) {
            echo "      ... et " . ($classes->count() - 5) . " autres\n";
        }
    }
    echo "\n";
    
    // Test 3: Nombre d'enseignants
    echo "👨‍🏫 Test 3: Enseignants\n";
    echo str_repeat("-", 20) . "\n";
    
    $teachers = \App\User::where('user_type', 'teacher')->get();
    echo "   📊 Nombre d'enseignants: {$teachers->count()}\n";
    
    if ($teachers->count() > 0) {
        echo "   📋 Quelques enseignants:\n";
        foreach ($teachers->take(3) as $teacher) {
            echo "      - {$teacher->name} ({$teacher->email})\n";
        }
        if ($teachers->count() > 3) {
            echo "      ... et " . ($teachers->count() - 3) . " autres\n";
        }
    }
    echo "\n";
    
    // Test 4: Nombre d'élèves actifs
    echo "👨‍🎓 Test 4: Élèves actifs\n";
    echo str_repeat("-", 23) . "\n";
    
    $sessionName = $currentSession ? $currentSession->name : \App\Helpers\Qs::getCurrentSession();
    $activeStudents = \App\Models\StudentRecord::where('session', $sessionName)->get();
    echo "   📊 Nombre d'élèves actifs pour {$sessionName}: {$activeStudents->count()}\n";
    
    if ($activeStudents->count() > 0) {
        // Répartition par classe
        $studentsByClass = $activeStudents->groupBy('my_class_id');
        echo "   📋 Répartition par classe:\n";
        foreach ($studentsByClass->take(5) as $classId => $students) {
            $className = \App\Models\MyClass::find($classId)->name ?? "Classe {$classId}";
            echo "      - {$className}: {$students->count()} élèves\n";
        }
    }
    echo "\n";
    
    // Test 5: Statistiques de promotion
    echo "📈 Test 5: Statistiques de promotion\n";
    echo str_repeat("-", 35) . "\n";
    
    $examRecords = \App\Models\ExamRecord::where('year', $sessionName)->get();
    echo "   📊 Nombre d'enregistrements d'examen: {$examRecords->count()}\n";
    
    if ($examRecords->count() > 0) {
        $decisions = $examRecords->pluck('decision')->filter()->countBy();
        echo "   📋 Répartition des décisions:\n";
        foreach ($decisions as $decision => $count) {
            $percentage = round(($count / $examRecords->count()) * 100, 1);
            echo "      - {$decision}: {$count} ({$percentage}%)\n";
        }
        
        // Calculer le taux de réussite
        $successfulDecisions = ['Passant', 'Promu', 'Admis'];
        $successfulCount = $examRecords->whereIn('decision', $successfulDecisions)->count();
        $successRate = $examRecords->count() > 0 ? round(($successfulCount / $examRecords->count()) * 100, 1) : 0;
        echo "   🎯 Taux de réussite calculé: {$successRate}%\n";
    } else {
        echo "   ⚠️  Aucun enregistrement d'examen trouvé\n";
        echo "   💡 Le taux de réussite utilisera la valeur par défaut\n";
    }
    echo "\n";
    
    // Test 6: Vérification des relations
    echo "🔗 Test 6: Vérifications des relations\n";
    echo str_repeat("-", 37) . "\n";
    
    // Vérifier les relations StudentRecord -> User
    $studentsWithUsers = $activeStudents->filter(function($student) {
        return $student->user !== null;
    });
    echo "   👤 Élèves avec utilisateur lié: {$studentsWithUsers->count()}/{$activeStudents->count()}\n";
    
    // Vérifier les relations StudentRecord -> MyClass
    $studentsWithClasses = $activeStudents->filter(function($student) {
        return $student->my_class !== null;
    });
    echo "   🏫 Élèves avec classe liée: {$studentsWithClasses->count()}/{$activeStudents->count()}\n";
    echo "\n";
    
    // Résumé final
    echo "📊 RÉSUMÉ DES DONNÉES DU DASHBOARD\n";
    echo str_repeat("=", 35) . "\n";
    echo "Session courante: " . ($currentSession ? $currentSession->name : $sessionName) . "\n";
    echo "Classes: {$classes->count()}\n";
    echo "Enseignants: {$teachers->count()}\n";
    echo "Élèves actifs: {$activeStudents->count()}\n";
    echo "Enregistrements d'examen: {$examRecords->count()}\n";
    
    if ($examRecords->count() > 0) {
        $successfulCount = $examRecords->whereIn('decision', ['Passant', 'Promu', 'Admis'])->count();
        $successRate = round(($successfulCount / $examRecords->count()) * 100, 1);
        echo "Taux de réussite: {$successRate}%\n";
    } else {
        echo "Taux de réussite: Données insuffisantes\n";
    }
    
    echo "\n🎉 Test terminé avec succès !\n";
    echo "💡 Ces données seront affichées dans le dashboard moderne.\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors du test :\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n🔧 Vérifiez que :\n";
    echo "   - Laravel est correctement configuré\n";
    echo "   - La base de données est accessible\n";
    echo "   - Les migrations ont été exécutées\n";
}

?>
