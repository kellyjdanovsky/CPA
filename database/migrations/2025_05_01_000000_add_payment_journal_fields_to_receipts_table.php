<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentJournalFieldsToReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Payment method details
            $table->string('payment_method')->nullable()->after('methode');
            $table->string('reference_number')->nullable()->after('payment_method');
            $table->text('observations')->nullable()->after('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('reference_number');
            $table->dropColumn('observations');
        });
    }
}
