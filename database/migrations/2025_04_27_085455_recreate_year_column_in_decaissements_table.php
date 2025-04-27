<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateYearColumnInDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            // Supprimer la colonne 'year' si elle existe
            if (Schema::hasColumn('decaissements', 'year')) {
                $table->dropColumn('year');
            }
        });

        // Ajouter la colonne 'year' à nouveau
        Schema::table('decaissements', function (Blueprint $table) {
            $table->string('year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            if (Schema::hasColumn('decaissements', 'year')) {
                $table->dropColumn('year');
            }
        });
    }
}
