<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcToLinksV03 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->after('member_limit', function () use ($table) {
                $table->boolean('is_multiple_registrant_allowed')->default(false);
                $table->integer('sub_member_limit')->default(0);
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
            if (Schema::hasColumn('links', 'is_multiple_registrant_allowed')) {
                $table->dropColumn('is_multiple_registrant_allowed');
            }
            if (Schema::hasColumn('links', 'sub_member_limit')) {
                $table->dropColumn('sub_member_limit');
            }
        });
    }
}
