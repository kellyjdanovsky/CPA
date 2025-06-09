<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecisionFieldsToExamRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_records', function (Blueprint $table) {
            $table->string('decision')->nullable()->after('pos');
            $table->unsignedBigInteger('next_class_id')->nullable()->after('decision');
            $table->text('observations')->nullable()->after('next_class_id');
            
            // Ajouter une clé étrangère pour next_class_id
            $table->foreign('next_class_id')->references('id')->on('my_classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_records', function (Blueprint $table) {
            $table->dropForeign(['next_class_id']);
            $table->dropColumn(['decision', 'next_class_id', 'observations']);
        });
    }
}
