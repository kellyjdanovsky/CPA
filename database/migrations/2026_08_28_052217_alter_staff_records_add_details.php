<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStaffRecordsAddDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_records', function (Blueprint $table) {
            $table->string('poste')->nullable();
            $table->string('departement')->nullable();
            $table->string('qualification')->nullable();
            $table->string('diplome')->nullable();
            $table->string('specialite')->nullable();
            $table->string('type_contrat')->default('CDI');
            $table->decimal('salaire', 12, 2)->nullable();
            $table->date('date_fin_contrat')->nullable();
            $table->integer('heures_semaine')->nullable();
            $table->text('observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_records', function (Blueprint $table) {
            $table->dropColumn([
                'poste', 'departement', 'qualification', 'diplome', 'specialite', 
                'type_contrat', 'salaire', 'date_fin_contrat', 'heures_semaine', 'observations'
            ]);
        });
    }
}
