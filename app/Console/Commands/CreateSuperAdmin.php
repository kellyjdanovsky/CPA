<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:superadmin {--username=superadmin} {--password=password} {--email=superadmin@example.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée un utilisateur super_admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->option('username');
        $password = $this->option('password');
        $email = $this->option('email');

        // Vérifier si le type d'utilisateur super_admin existe
        $superAdminType = DB::table('user_types')->where('title', 'super_admin')->first();
        if (!$superAdminType) {
            $this->info('Création du type super_admin...');
            DB::table('user_types')->insert([
                'title' => 'super_admin',
                'name' => 'Super Administrator',
                'level' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = DB::table('users')->where('username', $username)->first();
        if ($existingUser) {
            $this->info("L'utilisateur {$username} existe déjà. Mise à jour du mot de passe...");
            DB::table('users')->where('username', $username)->update([
                'password' => Hash::make($password),
                'user_type' => 'super_admin'
            ]);
        } else {
            $this->info("Création de l'utilisateur {$username}...");
            $userId = DB::table('users')->insertGetId([
                'name' => 'Super Admin',
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'user_type' => 'super_admin',
                'code' => strtoupper(Str::random(10)),
                'phone' => '123456789',
                'gender' => 'M',
                'photo' => 'https://via.placeholder.com/150',
                'address' => 'Adresse du Super Admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->info("Utilisateur créé avec l'ID: {$userId}");
        }

        $this->info("Vous pouvez maintenant vous connecter avec:");
        $this->info("Nom d'utilisateur: {$username}");
        $this->info("Mot de passe: {$password}");

        return 0;
    }
}