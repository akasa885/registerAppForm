<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcV04ToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->after('with_verification_certificate', function ($table) {
                $table->float('price_certificate', 12, 2)->nullable()->default(0);
                $table->boolean('is_using_payment_gateway')->nullable()->default(false);
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
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('price_certificate');
            $table->dropColumn('is_using_payment_gateway');
        });
    }
}
