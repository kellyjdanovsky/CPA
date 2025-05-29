<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Générer un code unique pour l'admin
$timestamp = time();
$adminCode = "ADMIN" . $timestamp;

// Vérifier si l'utilisateur admin existe
$admin = DB::table('users')->where('username', 'admin')->first();

if ($admin) {
    echo "L'utilisateur admin existe déjà avec l'ID: {$admin->id}\n";
    echo "Mise à jour du mot de passe...\n";
    
    // Mettre à jour le mot de passe
    DB::table('users')->where('username', 'admin')->update([
        'password' => Hash::make('cpa'),
        'status' => 'active',
        'updated_at' => now()
    ]);
    
    echo "Mot de passe mis à jour avec succès.\n";
} else {
    echo "Création d'un nouvel utilisateur admin...\n";
    
    // Créer un nouvel utilisateur admin
    DB::table('users')->insert([
        'name' => 'Administrator',
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('cpa'),
        'user_type' => 'super_admin',
        'code' => $adminCode,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "Utilisateur admin créé avec succès.\n";
}

// Afficher tous les utilisateurs
$users = DB::table('users')->get();

echo "\nListe des utilisateurs dans la base de données:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Nom: {$user->name}, Nom d'utilisateur: {$user->username}, Email: {$user->email}, Type: {$user->user_type}, Statut: {$user->status}\n";
}