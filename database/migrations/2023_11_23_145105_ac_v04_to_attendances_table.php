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
                $table->text('payment_information')->nullable();
                $table->string('category')->nullable();
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
            if (Schema::hasColumn('attendances', 'price_certificate')) {
                $table->dropColumn('price_certificate');
            }
            if (Schema::hasColumn('attendances', 'is_using_payment_gateway')) {
                $table->dropColumn('is_using_payment_gateway');
            }
            if (Schema::hasColumn('attendances', 'payment_information')) {
                $table->dropColumn('payment_information');
            }
            if (Schema::hasColumn('attendances', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
}
