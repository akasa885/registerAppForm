<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\MailPayment;

class CreateMailPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id')->nullable();
            $table->text('information')->nullable();
            $table->enum('type', MailPayment::TYPE_INFORMATION)->nullable();
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
        Schema::dropIfExists('mail_payments');
    }
}
