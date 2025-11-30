<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Student Records
        Schema::table('student_records', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('my_class_id');
            $table->index('section_id');
            $table->index('session');
            $table->index('grad');
            $table->index(['my_class_id', 'section_id', 'session']); // Pour les listes de classe
        });

        // 2. Payment Records
        Schema::table('payment_records', function (Blueprint $table) {
            $table->index('payment_id');
            $table->index('student_id');
            $table->index('year');
            $table->index('paid');
            $table->index(['student_id', 'payment_id']); // Pour vérifier si un élève a payé
        });

        // 3. Receipts
        Schema::table('receipts', function (Blueprint $table) {
            $table->index('pr_id');
            $table->index('year');
            $table->index('created_at'); // Pour les rapports journaliers
        });

        // 4. Marks
        Schema::table('marks', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('subject_id');
            $table->index('exam_id');
            $table->index('my_class_id');
            $table->index('section_id');
            $table->index('year');
            $table->index(['student_id', 'exam_id', 'year']); // Pour récupérer les notes d'un élève
            $table->index(['my_class_id', 'exam_id', 'subject_id', 'year']); // Pour les feuilles de notes par matière
        });

        // 5. Users
        Schema::table('users', function (Blueprint $table) {
            $table->index('user_type');
            $table->index('username');
            // email est déjà unique donc indexé
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer les index
        Schema::table('student_records', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['my_class_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['session']);
            $table->dropIndex(['grad']);
            $table->dropIndex(['my_class_id', 'section_id', 'session']);
        });

        Schema::table('payment_records', function (Blueprint $table) {
            $table->dropIndex(['payment_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['year']);
            $table->dropIndex(['paid']);
            $table->dropIndex(['student_id', 'payment_id']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex(['pr_id']);
            $table->dropIndex(['year']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['subject_id']);
            $table->dropIndex(['exam_id']);
            $table->dropIndex(['my_class_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['year']);
            $table->dropIndex(['student_id', 'exam_id', 'year']);
            $table->dropIndex(['my_class_id', 'exam_id', 'subject_id', 'year']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type']);
            $table->dropIndex(['username']);
        });
    }
}
