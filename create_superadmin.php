<?php

// Ce script crée un utilisateur super_admin

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Supprimer les utilisateurs existants avec ces noms d'utilisateur
DB::table('users')->where('username', 'superadmin')->orWhere('username', 'admin')->delete();

// Créer un nouvel utilisateur super_admin
$superAdminId = DB::table('users')->insertGetId([
    'name' => 'Super Admin',
    'username' => 'superadmin',
    'email' => 'superadmin@example.com',
    'password' => Hash::make('password'),
    'user_type' => 'super_admin',
    'code' => 'SUPERADMIN',
    'phone' => '123456789',
    'gender' => 'M',
    'photo' => 'https://via.placeholder.com/150',
    'address' => 'Adresse du Super Admin',
    'created_at' => now(),
    'updated_at' => now()
]);

// Créer un nouvel utilisateur admin
$adminId = DB::table('users')->insertGetId([
    'name' => 'Admin',
    'username' => 'admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'user_type' => 'admin',
    'code' => 'ADMINUSER',
    'phone' => '987654321',
    'gender' => 'M',
    'photo' => 'https://via.placeholder.com/150',
    'address' => 'Adresse de l\'Admin',
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Utilisateurs créés avec succès:\n";
echo "Super Admin (ID: {$superAdminId}) - Nom d'utilisateur: superadmin, Mot de passe: password\n";
echo "Admin (ID: {$adminId}) - Nom d'utilisateur: admin, Mot de passe: password\n";