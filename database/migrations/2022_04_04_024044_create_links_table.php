<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Link;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('link_path');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('banner')->nullable();
            $table->date('active_from');
            $table->date('active_until')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->enum('link_type', Link::LINK_TYPE)->default(Link::LINK_TYPE[0]);
            $table->bigInteger('viewed_count')->default(0);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
}
