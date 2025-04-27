<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStatutToStatusInDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            if (Schema::hasColumn('decaissements', 'statut')) {
                $table->renameColumn('statut', 'status');
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
            if (Schema::hasColumn('decaissements', 'status')) {
                $table->renameColumn('status', 'statut');
            }
        });
    }
}
