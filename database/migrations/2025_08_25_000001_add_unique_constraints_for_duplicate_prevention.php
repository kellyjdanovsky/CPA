<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueConstraintsForDuplicatePrevention extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo "🔒 Adding unique constraints for duplicate prevention...\n";
        
        // Add unique constraints for students
        Schema::table('student_records', function (Blueprint $table) {
            // Ensure one student record per user per session
            $table->unique(['user_id', 'session'], 'unique_student_session');
            
            // Ensure unique class assignment per student per session
            $table->unique(['user_id', 'my_class_id', 'session'], 'unique_student_class_session');
        });
        echo "✅ Student record constraints added\n";

        // Add unique constraints for payment records
        Schema::table('payment_records', function (Blueprint $table) {
            // Ensure one payment record per student per payment type per year
            $table->unique(['student_id', 'payment_id', 'year'], 'unique_student_payment_year');
        });
        echo "✅ Payment record constraints added\n";

        // Add unique constraints for marks
        Schema::table('marks', function (Blueprint $table) {
            // Ensure one mark record per student per subject per exam per year
            $table->unique(['student_id', 'subject_id', 'exam_id', 'year'], 'unique_student_subject_exam_year');
        });
        echo "✅ Mark record constraints added\n";

        // Add unique constraints for receipts
        Schema::table('receipts', function (Blueprint $table) {
            // Add reference_number column if not exists
            if (!Schema::hasColumn('receipts', 'reference_number')) {
                $table->string('reference_number', 100)->nullable();
            }
            
            // Make reference_number unique when not null
            $table->unique('reference_number', 'unique_receipt_reference');
            
            // Ensure unique transaction reference for payment tracking
            $table->unique(['pr_id', 'reference_number'], 'unique_payment_transaction');
        });
        echo "✅ Receipt constraints added\n";

        // Add unique constraints for exam records (bulletins)
        if (Schema::hasTable('exam_records')) {
            Schema::table('exam_records', function (Blueprint $table) {
                // Ensure one bulletin per student per exam per year
                $table->unique(['student_id', 'exam_id', 'year'], 'unique_student_exam_bulletin');
            });
            echo "✅ Exam record constraints added\n";
        }
        
        echo "🎉 All unique constraints added successfully!\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_records', function (Blueprint $table) {
            $table->dropUnique('unique_student_session');
            $table->dropUnique('unique_student_class_session');
        });

        Schema::table('payment_records', function (Blueprint $table) {
            $table->dropUnique('unique_student_payment_year');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->dropUnique('unique_student_subject_exam_year');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropUnique('unique_receipt_reference');
            $table->dropUnique('unique_payment_transaction');
        });

        if (Schema::hasTable('exam_records')) {
            Schema::table('exam_records', function (Blueprint $table) {
                $table->dropUnique('unique_student_exam_bulletin');
            });
        }
    }
}