<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecettesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if table already exists to avoid conflicts
        if (!Schema::hasTable('recettes')) {
            Schema::create('recettes', function (Blueprint $table) {
                $table->id();
                $table->date('date_recette');
                $table->unsignedInteger('student_id')->nullable();
                $table->string('beneficiaire_nom')->nullable();
                $table->unsignedInteger('class_id')->nullable();
                $table->unsignedInteger('payment_id')->nullable();
                $table->unsignedInteger('payment_record_id')->nullable();
                $table->unsignedBigInteger('encaissement_id')->nullable();
                $table->decimal('montant_encaisse', 10, 2);
                $table->string('mode_paiement');
                $table->string('reference_recette')->unique();
                $table->enum('type_recette', ['NORMAL', 'ADRA', 'TEAM3', 'DIVERS']);
                $table->text('description')->nullable();
                $table->unsignedInteger('created_by');
                $table->string('year');
                $table->text('observations')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Index
                $table->index(['date_recette', 'year']);
                $table->index(['student_id', 'year']);
                $table->index(['class_id', 'year']);
                $table->index(['type_recette', 'year']);
                $table->index(['mode_paiement', 'year']);
                $table->index('encaissement_id');

                // Foreign keys
                $table->foreign('student_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('class_id')->references('id')->on('my_classes')->onDelete('set null');
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
                $table->foreign('payment_record_id')->references('id')->on('payment_records')->onDelete('set null');
                $table->foreign('encaissement_id')->references('id')->on('encaissements')->onDelete('set null');
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
        Schema::dropIfExists('recettes');
    }
}