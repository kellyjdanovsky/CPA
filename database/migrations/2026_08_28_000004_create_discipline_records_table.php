<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisciplineRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('student_id');
            $table->enum('type', ['incident', 'sanction', 'recompense']);
            $table->string('category'); 
            $table->text('description');
            $table->date('date_incident');
            $table->enum('severity', ['mineur', 'moyen', 'grave', 'tres_grave'])->nullable();
            $table->text('action_taken')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->unsignedInteger('recorded_by');
            $table->string('year');
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['student_id', 'year']);
            $table->index(['type', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discipline_records');
    }
}
