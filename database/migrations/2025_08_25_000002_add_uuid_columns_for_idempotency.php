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
            if (!Schema::hasColumn('student_records', 'operation_uuid')) {
                $table->uuid('operation_uuid')->nullable();
            }
        });

        Schema::table('payment_records', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_records', 'operation_uuid')) {
                $table->uuid('operation_uuid')->nullable();
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('receipts', 'operation_uuid')) {
                $table->uuid('operation_uuid')->nullable();
            }
        });

        Schema::table('marks', function (Blueprint $table) {
            if (!Schema::hasColumn('marks', 'operation_uuid')) {
                $table->uuid('operation_uuid')->nullable();
            }
        });

        Schema::table('exam_records', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_records', 'operation_uuid')) {
                $table->uuid('operation_uuid')->nullable();
            }
        });

        // Create a duplicate detection log table
        if (!Schema::hasTable('duplicate_detection_logs')) {
            Schema::create('duplicate_detection_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('table_name');
                $table->string('operation_type');
                $table->uuid('operation_uuid')->index();
                $table->json('data_fingerprint');
                $table->string('user_id')->nullable();
                $table->string('session_id')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamp('attempted_at');
                $table->string('status');
                $table->text('reason')->nullable();
                $table->timestamps();
                
                $table->index(['table_name', 'operation_type']);
                $table->index(['user_id', 'attempted_at']);
            });
        }

        // Create transaction locks table
        if (!Schema::hasTable('transaction_locks')) {
            Schema::create('transaction_locks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('lock_key')->unique();
                $table->string('user_id')->nullable();
                $table->string('session_id')->nullable();
                $table->timestamp('locked_at');
                $table->timestamp('expires_at');
                $table->string('operation_type');
                $table->json('lock_data')->nullable();
                $table->timestamps();
                
                $table->index(['lock_key', 'expires_at']);
                $table->index('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_records', function (Blueprint $table) {
            if (Schema::hasColumn('student_records', 'operation_uuid')) {
                $table->dropColumn('operation_uuid');
            }
        });

        Schema::table('payment_records', function (Blueprint $table) {
            if (Schema::hasColumn('payment_records', 'operation_uuid')) {
                $table->dropColumn('operation_uuid');
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'operation_uuid')) {
                $table->dropColumn('operation_uuid');
            }
        });

        Schema::table('marks', function (Blueprint $table) {
            if (Schema::hasColumn('marks', 'operation_uuid')) {
                $table->dropColumn('operation_uuid');
            }
        });

        Schema::table('exam_records', function (Blueprint $table) {
            if (Schema::hasColumn('exam_records', 'operation_uuid')) {
                $table->dropColumn('operation_uuid');
            }
        });

        Schema::dropIfExists('duplicate_detection_logs');
        Schema::dropIfExists('transaction_locks');
    }
}