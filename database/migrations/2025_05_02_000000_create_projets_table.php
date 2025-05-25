<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('projets')) {
            Schema::create('projets', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->text('description')->nullable();
                $table->date('date_debut')->nullable();
                $table->date('date_fin')->nullable();
                $table->decimal('budget', 15, 2)->nullable();
                $table->string('statut')->default('actif');
                $table->timestamps();
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
        Schema::dropIfExists('projets');
    }
}
