<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InsertDefaultSettings extends Migration
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

        // Settings par défaut nécessaires pour le système
        $defaultSettings = [
            [
                'type' => 'system_name',
                'description' => 'Collège Privé Adventiste Avaratetezana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'system_title',
                'description' => 'CPA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'current_session',
                'description' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'system_email',
                'description' => 'admin@cpa-avaratetezana.mg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'phone',
                'description' => '+261 34 12 345 67',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'address',
                'description' => 'Avaratetezana, Antananarivo, Madagascar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'logo',
                'description' => '/images/logo_avar.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'lock_exam',
                'description' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'sch_name',
                'description' => 'Collège Privé Adventiste Avaratetezana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'term',
                'description' => 'Trimestre',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insérer seulement les settings qui n'existent pas déjà
        foreach ($defaultSettings as $setting) {
            $exists = DB::table('settings')->where('type', $setting['type'])->exists();
            if (!$exists) {
                DB::table('settings')->insert($setting);
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
        // Supprimer les settings par défaut
        $settingTypes = [
            'system_name', 'system_title', 'current_session', 'system_email',
            'phone', 'address', 'logo', 'lock_exam', 'sch_name', 'term'
        ];

        DB::table('settings')->whereIn('type', $settingTypes)->delete();
    }
}
