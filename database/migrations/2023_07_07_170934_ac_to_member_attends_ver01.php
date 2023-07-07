<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcToMemberAttendsVer01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_attends', function (Blueprint $table) {
            $table->after('member_id', function ($table) {
                $table->boolean('certificate')->default(false);
                $table->longText('payment_proof')->nullable();
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
        Schema::table('member_attends', function (Blueprint $table) {
            $table->dropColumn('certificate');
            $table->dropColumn('payment_proof');
        });
    }
}
