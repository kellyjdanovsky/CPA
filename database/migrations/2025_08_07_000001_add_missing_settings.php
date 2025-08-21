<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMissingSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table settings existe
        if (!Schema::hasTable('settings')) {
            return;
        }

        // Settings manquants nécessaires pour le système
        $missingSettings = [
            [
                'type' => 'term_ends',
                'description' => now()->format('d/m/Y'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'term_begins',
                'description' => now()->subMonths(3)->format('d/m/Y'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'alt_email',
                'description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'email_host',
                'description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'email_pass',
                'description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'next_term_fees_j',
                'description' => '20000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'next_term_fees_pn',
                'description' => '25000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'next_term_fees_p',
                'description' => '25000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'next_term_fees_n',
                'description' => '25600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'next_term_fees_s',
                'description' => '15600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'next_term_fees_c',
                'description' => '1600',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insérer seulement les settings qui n'existent pas déjà
        foreach ($missingSettings as $setting) {
            $exists = DB::table('settings')->where('type', $setting['type'])->exists();
            if (!$exists) {
                DB::table('settings')->insert($setting);
                echo "✅ Setting ajouté: {$setting['type']}\n";
            } else {
                echo "ℹ️  Setting existe déjà: {$setting['type']}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer les settings ajoutés
        $settingTypes = [
            'term_ends', 'term_begins', 'alt_email', 'email_host', 'email_pass',
            'next_term_fees_j', 'next_term_fees_pn', 'next_term_fees_p',
            'next_term_fees_n', 'next_term_fees_s', 'next_term_fees_c'
        ];

        DB::table('settings')->whereIn('type', $settingTypes)->delete();
    }
}
