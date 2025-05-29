<?php

// Script pour créer la table decaissements avec SQL brut
// Exécuter avec: php create_table_sql.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Vérifier si la table existe
    $tableExists = DB::select("SHOW TABLES LIKE 'decaissements'");
    
    if (empty($tableExists)) {
        // Créer la table avec SQL brut
        $sql = "CREATE TABLE `decaissements` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `date_paiement` date NOT NULL,
            `montant` decimal(15,2) NOT NULL,
            `montant_lettres` varchar(255) DEFAULT NULL,
            `motif` varchar(255) NOT NULL,
            `description` text DEFAULT NULL,
            `beneficiaire` varchar(255) NOT NULL,
            `coordonnees` text DEFAULT NULL,
            `methode_paiement` varchar(255) DEFAULT 'espèces',
            `reference` varchar(255) DEFAULT NULL,
            `piece` varchar(255) DEFAULT NULL,
            `details_bancaires` text DEFAULT NULL,
            `projet_rubrique` varchar(255) DEFAULT NULL,
            `justificatif_present` tinyint(1) DEFAULT 0,
            `observations` text DEFAULT NULL,
            `status` enum('en_attente','approuve','rejete') DEFAULT 'en_attente',
            `created_by` bigint(20) UNSIGNED NOT NULL,
            `year` varchar(255) NOT NULL,
            `projet_id` bigint(20) UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `decaissements_created_by_foreign` (`created_by`),
            KEY `decaissements_projet_id_foreign` (`projet_id`),
            CONSTRAINT `decaissements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
            CONSTRAINT `decaissements_projet_id_foreign` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL
        )";
        
        DB::statement($sql);
        echo "Table 'decaissements' créée avec succès.\n";
    } else {
        echo "La table 'decaissements' existe déjà.\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}