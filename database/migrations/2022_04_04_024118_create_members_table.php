<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id')->nullable();
            $table->string('prefix')->nullable();
            $table->string('full_name');
            $table->string('suffix')->nullable();
            $table->string('email');
            $table->string('contact_number');
            $table->string('corporation');
            $table->string('bukti_bayar')->nullable();
            $table->timestamps();

            $table->foreign('link_id')->references('id')->on('links')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
