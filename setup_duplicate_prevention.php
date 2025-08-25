<?php

/**
 * Configuration Script for Duplicate Prevention System
 * 
 * This script helps configure and validate the duplicate prevention system
 * Run with: php setup_duplicate_prevention.php
 */

echo "🛠️  Configuration du Système de Protection contre les Doublons\n";
echo str_repeat('=', 60) . "\n\n";

class DuplicatePreventionSetup
{
    private $errors = [];
    private $warnings = [];
    private $success = [];

    public function run()
    {
        $this->checkRequirements();
        $this->checkDatabase();
        $this->checkFiles();
        $this->setupRecommendations();
        $this->displaySummary();
    }

    private function checkRequirements()
    {
        echo "1. 🔍 Vérification des prérequis...\n";

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
            $this->success[] = "PHP version: " . PHP_VERSION;
        } else {
            $this->errors[] = "PHP version trop ancienne. Requis: 7.2+, Actuel: " . PHP_VERSION;
        }

        // Check Laravel
        if (file_exists(__DIR__ . '/artisan')) {
            $this->success[] = "Laravel détecté";
        } else {
            $this->errors[] = "Laravel non détecté (artisan manquant)";
        }

        // Check vendor directory
        if (file_exists(__DIR__ . '/vendor')) {
            $this->success[] = "Dependencies Composer installées";
        } else {
            $this->errors[] = "Dossier vendor manquant. Exécutez 'composer install'";
        }

        echo "\n";
    }

    private function checkDatabase()
    {
        echo "2. 🗄️  Vérification de la base de données...\n";

        // Check if migrations exist
        $migrationFiles = [
            'database/migrations/2025_08_25_000001_add_unique_constraints_for_duplicate_prevention.php',
            'database/migrations/2025_08_25_000002_add_uuid_columns_for_idempotency.php'
        ];

        foreach ($migrationFiles as $file) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $this->success[] = "Migration trouvée: " . basename($file);
            } else {
                $this->errors[] = "Migration manquante: " . basename($file);
            }
        }

        echo "\n";
    }

    private function checkFiles()
    {
        echo "3. 📁 Vérification des fichiers du système...\n";

        $requiredFiles = [
            'app/Traits/DuplicateDetection.php' => 'Trait de détection des doublons',
            'app/Services/TransactionLockService.php' => 'Service de verrous de transaction',
            'app/Services/DuplicateLoggerService.php' => 'Service de journalisation',
            'app/Http/Middleware/PreventDuplicateRequests.php' => 'Middleware de prévention',
            'app/Http/Controllers/SuperAdmin/DuplicateManagementController.php' => 'Contrôleur d\'administration',
            'app/Console/Commands/DuplicateCleanupCommand.php' => 'Commande de nettoyage',
            'public/js/duplicate-prevention.js' => 'Script JavaScript frontend',
            'resources/js/mixins/duplicate-prevention.js' => 'Mixin Vue.js'
        ];

        foreach ($requiredFiles as $file => $description) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $this->success[] = "$description";
            } else {
                $this->errors[] = "Fichier manquant: $file ($description)";
            }
        }

        // Check if middleware is registered
        $kernelFile = __DIR__ . '/app/Http/Kernel.php';
        if (file_exists($kernelFile)) {
            $kernelContent = file_get_contents($kernelFile);
            if (strpos($kernelContent, 'PreventDuplicateRequests') !== false) {
                $this->success[] = "Middleware enregistré dans Kernel.php";
            } else {
                $this->warnings[] = "Middleware non enregistré dans Kernel.php";
            }
        }

        // Check routes
        $routesFile = __DIR__ . '/routes/web.php';
        if (file_exists($routesFile)) {
            $routesContent = file_get_contents($routesFile);
            if (strpos($routesContent, 'duplicate_management') !== false) {
                $this->success[] = "Routes d'administration ajoutées";
            } else {
                $this->warnings[] = "Routes d'administration manquantes";
            }
        }

        echo "\n";
    }

    private function setupRecommendations()
    {
        echo "4. 💡 Recommandations de configuration...\n";

        $recommendations = [
            "Exécuter les migrations:" => "php artisan migrate",
            "Tester le système:" => "php test_duplicate_prevention.php",
            "Configurer le nettoyage automatique:" => "Ajouter la commande au scheduler",
            "Inclure les scripts JavaScript:" => "Ajouter duplicate-prevention.js au layout",
            "Configurer les logs:" => "Vérifier la configuration des logs Laravel",
            "Tester l'interface admin:" => "Accéder à /super_admin/duplicate_management/dashboard"
        ];

        foreach ($recommendations as $task => $action) {
            echo "  📋 $task\n     → $action\n";
        }

        echo "\n";
    }

    private function displaySummary()
    {
        echo str_repeat('=', 60) . "\n";
        echo "📊 RÉSUMÉ DE LA CONFIGURATION\n";
        echo str_repeat('=', 60) . "\n\n";

        if (!empty($this->success)) {
            echo "✅ Éléments configurés correctement (" . count($this->success) . "):\n";
            foreach ($this->success as $item) {
                echo "  ✓ $item\n";
            }
            echo "\n";
        }

        if (!empty($this->warnings)) {
            echo "⚠️  Avertissements (" . count($this->warnings) . "):\n";
            foreach ($this->warnings as $item) {
                echo "  ⚠ $item\n";
            }
            echo "\n";
        }

        if (!empty($this->errors)) {
            echo "❌ Erreurs à corriger (" . count($this->errors) . "):\n";
            foreach ($this->errors as $item) {
                echo "  ✗ $item\n";
            }
            echo "\n";
        }

        if (empty($this->errors)) {
            echo "🎉 Configuration prête!\n";
            echo "Le système de protection contre les doublons peut être utilisé.\n\n";
            
            echo "Prochaines étapes recommandées:\n";
            echo "1. php artisan migrate\n";
            echo "2. php test_duplicate_prevention.php\n";
            echo "3. Tester l'interface admin\n";
            echo "4. Configurer le monitoring\n\n";
        } else {
            echo "⚠️  Configuration incomplète\n";
            echo "Veuillez corriger les erreurs ci-dessus avant de continuer.\n\n";
        }
    }
}

// Exécuter la configuration
try {
    $setup = new DuplicatePreventionSetup();
    $setup->run();
} catch (Exception $e) {
    echo "❌ Erreur lors de la configuration: " . $e->getMessage() . "\n";
    exit(1);
}

echo "✅ Configuration terminée!\n";