<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->uuid('uuid')->unique();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('name');
            $table->text('short_description');
            $table->float('gross_total', 12, 2);
            $table->float('discount', 12, 2)->default(0);
            $table->float('tax', 12, 2)->default(0);
            $table->float('net_total', 12, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'decline', 'cancel', 'void'])->default('pending');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->string('snap_token_midtrans', 128)->nullable();
            $table->datetime('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
