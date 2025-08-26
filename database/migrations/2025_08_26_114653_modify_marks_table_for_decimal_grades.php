<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMarksTableForDecimalGrades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marks', function (Blueprint $table) {
            // Change integer columns to decimal(5,2) to support values like 28.50
            $table->decimal('t1', 5, 2)->nullable()->change();
            $table->decimal('t2', 5, 2)->nullable()->change();
            $table->decimal('t3', 5, 2)->nullable()->change();
            $table->decimal('t4', 5, 2)->nullable()->change();
            $table->decimal('tca', 5, 2)->nullable()->change();
            $table->decimal('exm', 5, 2)->nullable()->change();
            $table->decimal('tex1', 5, 2)->nullable()->change();
            $table->decimal('tex2', 5, 2)->nullable()->change();
            $table->decimal('tex3', 5, 2)->nullable()->change();
            $table->decimal('cum', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marks', function (Blueprint $table) {
            // Revert back to integer columns
            $table->integer('t1')->nullable()->change();
            $table->integer('t2')->nullable()->change();
            $table->integer('t3')->nullable()->change();
            $table->integer('t4')->nullable()->change();
            $table->integer('tca')->nullable()->change();
            $table->integer('exm')->nullable()->change();
            $table->integer('tex1')->nullable()->change();
            $table->integer('tex2')->nullable()->change();
            $table->integer('tex3')->nullable()->change();
            $table->integer('cum')->nullable()->change();
        });
    }
}
