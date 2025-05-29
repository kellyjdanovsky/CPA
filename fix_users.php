<?php

// Ce script vérifie et corrige les problèmes potentiels avec les utilisateurs

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "Vérification des types d'utilisateurs...\n";

// Vérifier si les types d'utilisateurs existent
$superAdminType = DB::table('user_types')->where('title', 'super_admin')->first();
$adminType = DB::table('user_types')->where('title', 'admin')->first();

if (!$superAdminType) {
    echo "Création du type super_admin...\n";
    DB::table('user_types')->insert([
        'title' => 'super_admin',
        'name' => 'Super Administrator',
        'level' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}

if (!$adminType) {
    echo "Création du type admin...\n";
    DB::table('user_types')->insert([
        'title' => 'admin',
        'name' => 'Administrator',
        'level' => 2,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}

// Vérifier si les utilisateurs existent
$superAdmin = DB::table('users')->where('username', 'superadmin')->first();
$admin = DB::table('users')->where('username', 'admin')->first();

if (!$superAdmin) {
    echo "Création de l'utilisateur superadmin...\n";
    DB::table('users')->insert([
        'name' => 'Super Admin',
        'username' => 'superadmin',
        'email' => 'superadmin@example.com',
        'password' => Hash::make('password'),
        'user_type' => 'super_admin',
        'code' => strtoupper(Str::random(10)),
        'phone' => '123456789',
        'gender' => 'M',
        'photo' => 'https://via.placeholder.com/150',
        'address' => 'Adresse du Super Admin',
        'created_at' => now(),
        'updated_at' => now()
    ]);
} else {
    echo "Mise à jour de l'utilisateur superadmin...\n";
    DB::table('users')->where('username', 'superadmin')->update([
        'password' => Hash::make('password'),
        'user_type' => 'super_admin'
    ]);
}

if (!$admin) {
    echo "Création de l'utilisateur admin...\n";
    DB::table('users')->insert([
        'name' => 'Admin',
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'user_type' => 'admin',
        'code' => strtoupper(Str::random(10)),
        'phone' => '987654321',
        'gender' => 'M',
        'photo' => 'https://via.placeholder.com/150',
        'address' => 'Adresse de l\'Admin',
        'created_at' => now(),
        'updated_at' => now()
    ]);
} else {
    echo "Mise à jour de l'utilisateur admin...\n";
    DB::table('users')->where('username', 'admin')->update([
        'password' => Hash::make('password'),
        'user_type' => 'admin'
    ]);
}

echo "Vérification terminée.\n";

// Afficher les utilisateurs
$users = DB::table('users')->where('username', 'superadmin')->orWhere('username', 'admin')->get();
echo "Utilisateurs disponibles:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Nom: {$user->name}, Nom d'utilisateur: {$user->username}, Type: {$user->user_type}\n";
}

echo "\nVous pouvez maintenant vous connecter avec:\n";
echo "Superadmin - Nom d'utilisateur: superadmin, Mot de passe: password\n";
echo "Admin - Nom d'utilisateur: admin, Mot de passe: password\n";