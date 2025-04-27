<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearToDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            if (!Schema::hasColumn('decaissements', 'year')) {
                $table->string('year')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('decaissements', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('observations');
                $table->foreign('created_by')->references('id')->on('users');
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
            $columns = ['year', 'created_by'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('decaissements', $column)) {
                    if ($column === 'created_by') {
                        $table->dropForeign(['created_by']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
}
