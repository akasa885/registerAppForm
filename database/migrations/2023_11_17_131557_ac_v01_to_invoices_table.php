<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcV01ToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->after('id', function ($table) {
                $table->string('uuid', 36)->unique()->nullable();
            });

            $table->after('status', function ($table) {
                $table->boolean('is_automatic')->default(false);
                $table->string('payment_method', 50)->nullable()->comment('midtrans, bank_transfer, etc ...');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('uuid');
                $table->dropColumn('is_automatic');
                $table->dropColumn('payment_method');
        });
    }
}
