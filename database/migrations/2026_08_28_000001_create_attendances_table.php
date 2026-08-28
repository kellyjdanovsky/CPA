<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('my_class_id');
            $table->unsignedInteger('section_id');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'retard', 'excuse'])->default('present');
            $table->enum('period', ['matin', 'apres_midi', 'journee'])->default('journee');
            $table->unsignedInteger('subject_id')->nullable();
            $table->unsignedInteger('marked_by');
            $table->text('observations')->nullable();
            $table->string('year');
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('my_class_id')->references('id')->on('my_classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['student_id', 'date', 'period']);
            $table->index(['my_class_id', 'date']);
            $table->index(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
