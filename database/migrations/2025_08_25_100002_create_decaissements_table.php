<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDecaissementsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('decaissements')) {
            Schema::create('decaissements', function (Blueprint $table) {
                $table->id();
                $table->date('date_decaissement');
                $table->string('reference_op')->unique();
                $table->string('beneficiaire');
                $table->decimal('montant', 10, 2);
                $table->text('montant_lettres');
                $table->text('motif');
                $table->string('mode_paiement');
                $table->string('projet_rubrique')->nullable();
                $table->string('piece_justificative_path')->nullable();
                $table->string('piece_justificative_nom')->nullable();
                $table->boolean('piece_justificative_valide')->default(false);
                $table->enum('statut', ['EN_ATTENTE', 'APPROUVE', 'PAYE', 'ANNULE'])->default('EN_ATTENTE');
                $table->unsignedInteger('created_by');
                $table->unsignedInteger('approved_by')->nullable();
                $table->unsignedInteger('paid_by')->nullable();
                $table->string('year');
                $table->text('observations')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['date_decaissement', 'year']);
                $table->index(['statut', 'year']);
                $table->index(['beneficiaire', 'year']);
                $table->index(['projet_rubrique', 'year']);
                $table->index(['mode_paiement', 'year']);

                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('decaissements');
    }
}