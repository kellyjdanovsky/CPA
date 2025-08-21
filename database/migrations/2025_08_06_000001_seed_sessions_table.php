<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SeedSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insérer les sessions par défaut
        DB::table('sessions')->insert([
            [
                'name' => '2023-2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-06-30',
                'is_active' => false,
                'description' => 'Année scolaire 2023-2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'is_active' => true,
                'description' => 'Année scolaire 2024-2025 (Active)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-06-30',
                'is_active' => false,
                'description' => 'Année scolaire 2025-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sessions')->whereIn('name', ['2023-2024', '2024-2025', '2025-2026'])->delete();
    }
}
