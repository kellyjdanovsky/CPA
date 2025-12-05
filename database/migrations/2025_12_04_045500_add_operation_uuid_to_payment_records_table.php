<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperationUuidToPaymentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_records', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_records', 'operation_uuid')) {
                $table->string('operation_uuid')->nullable()->after('ref_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_records', function (Blueprint $table) {
            if (Schema::hasColumn('payment_records', 'operation_uuid')) {
                $table->dropColumn('operation_uuid');
            }
        });
    }
}
