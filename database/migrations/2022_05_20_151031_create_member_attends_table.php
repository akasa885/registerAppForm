<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberAttendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_attends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attend_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->timestamps();

            $table->foreign('attend_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_attends');
    }
}
