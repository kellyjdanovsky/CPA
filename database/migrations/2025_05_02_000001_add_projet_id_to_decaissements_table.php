<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjetIdToDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            if (!Schema::hasColumn('decaissements', 'projet_id')) {
                $table->unsignedBigInteger('projet_id')->nullable()->after('created_by');
                $table->foreign('projet_id')->references('id')->on('projets')->onDelete('set null');
            }
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
            if (Schema::hasColumn('decaissements', 'projet_id')) {
                $table->dropForeign(['projet_id']);
                $table->dropColumn('projet_id');
            }
        });
    }
}
