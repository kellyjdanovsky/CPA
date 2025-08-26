<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesForMarksTabulation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to marks table for frequently queried columns
        Schema::table('marks', function (Blueprint $table) {
            $table->index(['student_id', 'subject_id', 'exam_id', 'year'], 'idx_marks_student_subject_exam_year');
            $table->index(['exam_id', 'year'], 'idx_marks_exam_year');
            $table->index(['student_id', 'exam_id'], 'idx_marks_student_exam');
        });

        // Add indexes to exam_records table for frequently queried columns
        Schema::table('exam_records', function (Blueprint $table) {
            $table->index(['student_id', 'exam_id'], 'idx_exam_records_student_exam');
            $table->index(['exam_id', 'year'], 'idx_exam_records_exam_year');
            $table->index(['student_id'], 'idx_exam_records_student');
        });

        // Add indexes to student_records table for frequently queried columns
        Schema::table('student_records', function (Blueprint $table) {
            $table->index(['my_class_id', 'section_id', 'session'], 'idx_student_records_class_section_session');
            $table->index(['my_class_id'], 'idx_student_records_class');
            $table->index(['section_id'], 'idx_student_records_section');
            $table->index(['session'], 'idx_student_records_session');
        });

        // Add indexes to subjects table for frequently queried columns
        Schema::table('subjects', function (Blueprint $table) {
            $table->index(['my_class_id'], 'idx_subjects_class');
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
            $table->dropIndex('idx_marks_student_subject_exam_year');
            $table->dropIndex('idx_marks_exam_year');
            $table->dropIndex('idx_marks_student_exam');
        });

        Schema::table('exam_records', function (Blueprint $table) {
            $table->dropIndex('idx_exam_records_student_exam');
            $table->dropIndex('idx_exam_records_exam_year');
            $table->dropIndex('idx_exam_records_student');
        });

        Schema::table('student_records', function (Blueprint $table) {
            $table->dropIndex('idx_student_records_class_section_session');
            $table->dropIndex('idx_student_records_class');
            $table->dropIndex('idx_student_records_section');
            $table->dropIndex('idx_student_records_session');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_class');
        });
    }
}