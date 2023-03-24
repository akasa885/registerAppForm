<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcToLinksV01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->longText('registration_info')->nullable()->after('description');
            $table->after('link_type', function ($table) {
                $table->boolean('has_member_limit')->default(0);
                $table->integer('member_limit')->nullable();
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
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('registration_info');
            $table->dropColumn('has_member_limit');
            $table->dropColumn('member_limit');
        });
    }
}
