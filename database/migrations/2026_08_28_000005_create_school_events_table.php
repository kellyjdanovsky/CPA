<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('event_type', ['cours', 'examen', 'vacances', 'fete', 'reunion', 'conseil', 'pedagogique', 'autre']);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('all_day')->default(true);
            $table->unsignedInteger('class_id')->nullable(); 
            $table->unsignedInteger('created_by');
            $table->string('year');
            $table->timestamps();
            
            $table->foreign('class_id')->references('id')->on('my_classes')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['start_date', 'end_date']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_events');
    }
}
