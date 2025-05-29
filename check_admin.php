<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Vérifier l'utilisateur admin
$admin = DB::table('users')->where('username', 'admin')->first();

if ($admin) {
    echo "Utilisateur admin trouvé :\n";
    echo "ID: {$admin->id}\n";
    echo "Nom: {$admin->name}\n";
    echo "Nom d'utilisateur: {$admin->username}\n";
    echo "Email: {$admin->email}\n";
    echo "Type: {$admin->user_type}\n";
    echo "Statut: {$admin->status}\n";
    
    // Vérifier si le mot de passe correspond à 'cpa'
    echo "\nVérification du mot de passe 'cpa': ";
    if (Hash::check('cpa', $admin->password)) {
        echo "CORRECT\n";
    } else {
        echo "INCORRECT\n";
    }
} else {
    echo "Aucun utilisateur admin trouvé.\n";
}