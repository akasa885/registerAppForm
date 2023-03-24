<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->string('welcome')->nullable();
            $table->string('site_icon')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('admin_email')->nullable();
            $table->string('admin_phone')->nullable();
            $table->tinyInteger('is_mail_editable')->default(0);
            $table->enum('mail_driver', SiteSetting::MAIL_DRIVER)->default('smtp');
            $table->string('mail_host')->nullable();
            $table->integer('mail_port')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->string('mail_encryption', 10)->nullable();
            $table->string('mail_username',100)->nullable();
            $table->string('mail_password')->nullable();
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
        Schema::dropIfExists('site_settings');
    }
}
