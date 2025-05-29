<?php

// Ce script vérifie si les utilisateurs existent dans la base de données

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Vérifier les types d'utilisateurs
$userTypes = DB::table('user_types')->get();
echo "Types d'utilisateurs disponibles:\n";
foreach ($userTypes as $type) {
    echo "ID: {$type->id}, Titre: {$type->title}, Nom: {$type->name}, Niveau: {$type->level}\n";
}

echo "\n";

// Vérifier les utilisateurs
$users = DB::table('users')->get();
echo "Utilisateurs disponibles:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Nom: {$user->name}, Nom d'utilisateur: {$user->username}, Email: {$user->email}, Type: {$user->user_type}\n";
}

echo "\n";
echo "Vous pouvez vous connecter avec l'un des utilisateurs ci-dessus.\n";
echo "Le mot de passe pour les utilisateurs créés récemment est 'admin123'.\n";