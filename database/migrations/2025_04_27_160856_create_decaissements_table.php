<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('decaissements')) {
            Schema::create('decaissements', function (Blueprint $table) {
                $table->id();
                $table->date('date_paiement');
                $table->decimal('montant', 15, 2);
                $table->string('motif');
                $table->text('description')->nullable();
                $table->string('beneficiaire');
                $table->string('methode_paiement')->default('espèces');
                $table->string('reference')->nullable();
                $table->string('piece')->nullable();
                $table->text('details_bancaires')->nullable();
                $table->enum('status', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
                $table->unsignedBigInteger('created_by');
                $table->string('year');
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('decaissements');
    }
}
