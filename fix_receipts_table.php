<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Vérifier si la colonne 'methode' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'methode')) {
    echo "La colonne 'methode' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'methode' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN methode VARCHAR(255) NULL AFTER year');
        echo "Colonne 'methode' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'methode' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'methode' existe déjà dans la table 'receipts'.\n";
}

// Vérifier si la colonne 'payment_method' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'payment_method')) {
    echo "La colonne 'payment_method' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'payment_method' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN payment_method VARCHAR(255) NULL AFTER methode');
        echo "Colonne 'payment_method' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'payment_method' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'payment_method' existe déjà dans la table 'receipts'.\n";
}

// Vérifier si la colonne 'reference_number' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'reference_number')) {
    echo "La colonne 'reference_number' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'reference_number' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN reference_number VARCHAR(255) NULL AFTER payment_method');
        echo "Colonne 'reference_number' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'reference_number' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'reference_number' existe déjà dans la table 'receipts'.\n";
}

// Vérifier si la colonne 'observations' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'observations')) {
    echo "La colonne 'observations' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'observations' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN observations TEXT NULL AFTER reference_number');
        echo "Colonne 'observations' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'observations' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'observations' existe déjà dans la table 'receipts'.\n";
}

// Vérifier si la colonne 'created_by' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'created_by')) {
    echo "La colonne 'created_by' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'created_by' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN created_by VARCHAR(255) NULL AFTER observations');
        echo "Colonne 'created_by' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'created_by' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'created_by' existe déjà dans la table 'receipts'.\n";
}

// Vérifier si la colonne 'amount' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'amount')) {
    echo "La colonne 'amount' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'amount' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN amount DECIMAL(15, 2) NULL AFTER created_by');
        echo "Colonne 'amount' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'amount' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'amount' existe déjà dans la table 'receipts'.\n";
}

// Vérifier si la colonne 'description' existe dans la table 'receipts'
if (!Schema::hasColumn('receipts', 'description')) {
    echo "La colonne 'description' n'existe pas dans la table 'receipts'. Ajout de la colonne...\n";
    
    try {
        // Ajouter la colonne 'description' à la table 'receipts'
        DB::statement('ALTER TABLE receipts ADD COLUMN description TEXT NULL AFTER amount');
        echo "Colonne 'description' ajoutée avec succès.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de l'ajout de la colonne 'description' : " . $e->getMessage() . "\n";
    }
} else {
    echo "La colonne 'description' existe déjà dans la table 'receipts'.\n";
}

echo "\nVérification de la structure de la table 'receipts' terminée.\n";

// Afficher la structure actuelle de la table 'receipts'
echo "\nStructure actuelle de la table 'receipts' :\n";
$columns = Schema::getColumnListing('receipts');
foreach ($columns as $column) {
    echo "- {$column}\n";
}