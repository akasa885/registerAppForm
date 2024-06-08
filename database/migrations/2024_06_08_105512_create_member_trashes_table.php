<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberTrashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_trashes', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->foreignId('link_id')->constrained('links')->onDelete('cascade');
            $table->string('contact_number');
            $table->string('domisili')->nullable();
            $table->string('corporation')->nullable();
            $table->text('bukti_bayar')->nullable();
            $table->timestamp('deleted_time')->useCurrent()->comment('Waktu data dihapus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_trashes');
    }
}
