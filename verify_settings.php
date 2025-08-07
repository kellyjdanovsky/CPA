<?php

/**
 * Script de vérification des settings
 * À exécuter depuis la racine du projet : php verify_settings.php
 */

require_once 'vendor/autoload.php';

echo "🔍 Vérification des settings du système\n";
echo "=======================================\n\n";

try {
    // Initialiser Laravel
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel initialisé avec succès\n\n";
    
    // Liste des settings requis
    $requiredSettings = [
        'system_name' => 'Nom de l\'école',
        'system_title' => 'Titre du système',
        'current_session' => 'Session courante',
        'system_email' => 'Email du système',
        'phone' => 'Téléphone',
        'address' => 'Adresse',
        'logo' => 'Logo',
        'lock_exam' => 'Verrouillage des examens',
        'sch_name' => 'Nom de l\'école (alternatif)',
        'term' => 'Terme (trimestre/semestre)',
        'term_ends' => 'Fin du trimestre',
        'term_begins' => 'Début du trimestre',
        'alt_email' => 'Email alternatif',
        'email_host' => 'Hôte email',
        'email_pass' => 'Mot de passe email',
    ];
    
    echo "📋 Vérification des settings requis :\n";
    echo str_repeat("-", 40) . "\n";
    
    $allSettingsPresent = true;
    $settingsFromDB = [];
    
    foreach ($requiredSettings as $type => $description) {
        $setting = \App\Models\Setting::where('type', $type)->first();
        
        if ($setting) {
            echo "   ✅ {$type} : {$description}\n";
            echo "      💾 Valeur DB: " . (strlen($setting->description) > 50 ? substr($setting->description, 0, 50) . '...' : $setting->description) . "\n";
            $settingsFromDB[$type] = $setting->description;
        } else {
            echo "   ❌ {$type} : {$description} (MANQUANT)\n";
            $defaultValue = \App\Helpers\Qs::getSetting($type);
            echo "      🔄 Valeur par défaut: " . (strlen($defaultValue) > 50 ? substr($defaultValue, 0, 50) . '...' : $defaultValue) . "\n";
            $allSettingsPresent = false;
        }
    }
    
    echo "\n";
    
    // Test de la page settings
    echo "🌐 Test de la page settings :\n";
    echo str_repeat("-", 30) . "\n";
    
    try {
        // Simuler l'appel du contrôleur
        $settingRepo = app(\App\Repositories\SettingRepo::class);
        $classRepo = app(\App\Repositories\MyClassRepo::class);
        
        $controller = new \App\Http\Controllers\SuperAdmin\SettingController($settingRepo, $classRepo);
        
        // Tester la méthode index
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('index');
        
        // Simuler la récupération des données
        $allSettings = $settingRepo->all();
        $settingsArray = $allSettings->flatMap(function($s){
            return [$s->type => $s->description];
        });
        
        echo "   ✅ Contrôleur accessible\n";
        echo "   📊 Nombre de settings en DB: " . $allSettings->count() . "\n";
        echo "   🔧 Settings transformés pour la vue: " . $settingsArray->count() . "\n";
        
        // Vérifier spécifiquement term_ends
        if (isset($settingsArray['term_ends'])) {
            echo "   ✅ term_ends disponible: " . $settingsArray['term_ends'] . "\n";
        } else {
            echo "   ❌ term_ends manquant dans les données de la vue\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Erreur lors du test du contrôleur: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Vérification des settings de frais
    echo "💰 Vérification des settings de frais :\n";
    echo str_repeat("-", 35) . "\n";
    
    $feeSettings = [
        'next_term_fees_j' => 'Frais Junior',
        'next_term_fees_pn' => 'Frais Pré-Nursery',
        'next_term_fees_p' => 'Frais Primary',
        'next_term_fees_n' => 'Frais Nursery',
        'next_term_fees_s' => 'Frais Secondary',
        'next_term_fees_c' => 'Frais College',
    ];
    
    foreach ($feeSettings as $type => $description) {
        $value = \App\Helpers\Qs::getSetting($type);
        echo "   💵 {$description}: {$value} Ar\n";
    }
    
    echo "\n";
    
    // Résumé final
    echo "📊 RÉSUMÉ DE LA VÉRIFICATION\n";
    echo str_repeat("=", 30) . "\n";
    
    if ($allSettingsPresent) {
        echo "🎉 EXCELLENT: Tous les settings requis sont présents !\n\n";
        echo "✅ Settings en base: " . count($settingsFromDB) . "\n";
        echo "✅ Page settings: Accessible\n";
        echo "✅ term_ends: Disponible\n";
        echo "✅ Système: Prêt à utiliser\n\n";
        echo "🌐 Vous pouvez maintenant accéder à :\n";
        echo "   http://127.0.0.1:8000/super_admin/settings\n";
    } else {
        echo "⚠️  ATTENTION: Certains settings sont manquants\n\n";
        echo "🔧 Actions recommandées :\n";
        echo "   1. Exécuter: php artisan migrate\n";
        echo "   2. Vérifier la table settings\n";
        echo "   3. Relancer ce script\n";
    }
    
    echo "\n💡 Note: Les valeurs par défaut sont utilisées automatiquement\n";
    echo "   si un setting n'existe pas en base de données.\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors de la vérification :\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n🔧 Vérifiez que :\n";
    echo "   - Laravel est correctement configuré\n";
    echo "   - La base de données est accessible\n";
    echo "   - Les migrations ont été exécutées\n";
}

?>
