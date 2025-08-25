<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUuidColumnsForIdempotency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add UUID columns for idempotency across critical tables
        
        Schema::table('student_records', function (Blueprint $table) {
            $table->uuid('operation_uuid')->nullable()->unique();
            $table->index('operation_uuid');
        });

        Schema::table('payment_records', function (Blueprint $table) {
            $table->uuid('operation_uuid')->nullable()->unique();
            $table->index('operation_uuid');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->uuid('operation_uuid')->nullable()->unique();
            $table->index('operation_uuid');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->uuid('operation_uuid')->nullable()->unique();
            $table->index('operation_uuid');
        });

        Schema::table('exam_records', function (Blueprint $table) {
            $table->uuid('operation_uuid')->nullable()->unique();
            $table->index('operation_uuid');
        });

        // Create a duplicate detection log table
        Schema::create('duplicate_detection_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name');
            $table->string('operation_type'); // insert, update, delete
            $table->uuid('operation_uuid')->index();
            $table->json('data_fingerprint'); // Hash of critical data
            $table->string('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('attempted_at');
            $table->string('status'); // blocked, allowed, duplicate_detected
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index(['table_name', 'operation_type']);
            $table->index(['user_id', 'attempted_at']);
        });

        // Create transaction locks table for preventing concurrent operations
        Schema::create('transaction_locks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lock_key')->unique(); // Unique identifier for the resource being locked
            $table->string('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('locked_at');
            $table->timestamp('expires_at');
            $table->string('operation_type');
            $table->json('lock_data')->nullable(); // Additional data about what's being locked
            $table->timestamps();
            
            $table->index(['lock_key', 'expires_at']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_records', function (Blueprint $table) {
            $table->dropColumn('operation_uuid');
        });

        Schema::table('payment_records', function (Blueprint $table) {
            $table->dropColumn('operation_uuid');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('operation_uuid');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->dropColumn('operation_uuid');
        });

        Schema::table('exam_records', function (Blueprint $table) {
            $table->dropColumn('operation_uuid');
        });

        Schema::dropIfExists('duplicate_detection_logs');
        Schema::dropIfExists('transaction_locks');
    }
}