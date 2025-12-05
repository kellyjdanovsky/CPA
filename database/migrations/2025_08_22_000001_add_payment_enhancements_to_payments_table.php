<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentEnhancementsToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'verification_code')) {
                $table->string('verification_code')->nullable();
            }
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->string('payment_type')->default('cash');
            }
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->timestamp('payment_date')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'payment_type', 'payment_date']);
        });
    }
}
