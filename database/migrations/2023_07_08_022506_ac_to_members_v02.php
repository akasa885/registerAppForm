<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcToMembersV02 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            // nullable question1 as text after corporation column
            $table->text('question1')->nullable()->after('corporation');
            // nullable question2 as text after question1 column
            $table->text('question2')->nullable()->after('question1');
            // nullable question3 as text after question2 column
            $table->text('question3')->nullable()->after('question2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            // drop question1 column
            $table->dropColumn('question1');
            // drop question2 column
            $table->dropColumn('question2');
            // drop question3 column
            $table->dropColumn('question3');
        });
    }
}
