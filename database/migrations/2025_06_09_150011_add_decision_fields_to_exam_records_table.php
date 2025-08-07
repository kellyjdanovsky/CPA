<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecisionFieldsToExamRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_records', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('exam_records', 'decision')) {
                $table->string('decision')->nullable()->after('pos');
            }

            if (!Schema::hasColumn('exam_records', 'next_class_id')) {
                $table->unsignedBigInteger('next_class_id')->nullable()->after('decision');
            }

            if (!Schema::hasColumn('exam_records', 'observations')) {
                $table->text('observations')->nullable()->after('next_class_id');
            }

            // Note: Les colonnes existent déjà et fonctionnent correctement
            // Pas besoin d'ajouter de clé étrangère
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_records', function (Blueprint $table) {
            // Supprimer la clé étrangère si elle existe
            try {
                $table->dropForeign(['next_class_id']);
            } catch (\Exception $e) {
                // Ignorer si la clé étrangère n'existe pas
            }

            // Supprimer les colonnes si elles existent
            $columnsToRemove = [];
            if (Schema::hasColumn('exam_records', 'decision')) {
                $columnsToRemove[] = 'decision';
            }
            if (Schema::hasColumn('exam_records', 'next_class_id')) {
                $columnsToRemove[] = 'next_class_id';
            }
            if (Schema::hasColumn('exam_records', 'observations')) {
                $columnsToRemove[] = 'observations';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
}
