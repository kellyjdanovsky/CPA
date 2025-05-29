<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Afficher les types de classe existants
$existingTypes = DB::table('class_types')->get();

echo "Types de classe existants :\n";
foreach ($existingTypes as $type) {
    echo "ID: {$type->id}, Nom: {$type->name}\n";
}

// Vérifier si les types de classe à ajouter existent déjà
$newTypes = ['Préscolaire', 'Niveau 1', 'Niveau 2'];
$typesToAdd = [];

foreach ($newTypes as $typeName) {
    $exists = false;
    foreach ($existingTypes as $existingType) {
        if (strtolower($existingType->name) === strtolower($typeName)) {
            $exists = true;
            echo "\nLe type de classe '{$typeName}' existe déjà avec l'ID: {$existingType->id}\n";
            break;
        }
    }
    
    if (!$exists) {
        $typesToAdd[] = $typeName;
    }
}

// Ajouter les nouveaux types de classe
if (count($typesToAdd) > 0) {
    echo "\nAjout des nouveaux types de classe :\n";
    
    foreach ($typesToAdd as $typeName) {
        // Générer un code unique pour le type de classe
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $typeName), 0, 3)) . rand(100, 999);
        
        DB::table('class_types')->insert([
            'name' => $typeName,
            'code' => $code,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "Type de classe '{$typeName}' ajouté avec succès avec le code '{$code}'.\n";
    }
} else {
    echo "\nAucun nouveau type de classe à ajouter.\n";
}

// Afficher tous les types de classe après l'ajout
$allTypes = DB::table('class_types')->get();

echo "\nListe complète des types de classe :\n";
foreach ($allTypes as $type) {
    echo "ID: {$type->id}, Nom: {$type->name}\n";
}