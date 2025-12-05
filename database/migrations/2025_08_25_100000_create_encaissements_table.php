<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if table already exists to avoid conflicts
        if (!Schema::hasTable('encaissements')) {
            Schema::create('encaissements', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('student_id');
                $table->unsignedInteger('payment_id');
                $table->unsignedInteger('payment_record_id');
                $table->unsignedInteger('class_id');
                $table->enum('type_encaissement', ['ADRA', 'TEAM3']);
                $table->decimal('montant_original', 10, 2);
                $table->decimal('pourcentage_pris_en_charge', 5, 2);
                $table->decimal('montant_encaisse', 10, 2);
                $table->date('date_encaissement');
                $table->string('reference_encaissement')->unique();
                $table->unsignedInteger('created_by');
                $table->string('year');
                $table->text('observations')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Index pour optimiser les requêtes
                $table->index(['student_id', 'year']);
                $table->index(['payment_id', 'year']);
                $table->index(['class_id', 'year']);
                $table->index(['type_encaissement', 'year']);
                $table->index('date_encaissement');

                // Contraintes de clés étrangères
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
                $table->foreign('payment_record_id')->references('id')->on('payment_records')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('my_classes')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encaissements');
    }
}