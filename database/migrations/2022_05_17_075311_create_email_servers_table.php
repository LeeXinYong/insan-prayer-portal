<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_servers', function (Blueprint $table) {
            $table->id();
            $table->text('transport');
            $table->text('mail_host')->nullable();
            $table->text('mail_port')->nullable();
            $table->text('mail_encryption')->nullable();
            $table->text('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->text('mail_name');
            $table->text('mail_domain')->nullable();
            $table->text('mail_secret')->nullable();
            $table->text('mail_address');
            $table->text('mail_cc')->nullable();
            $table->uuid('added_by')->nullable();
            $table->string('added_ip')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->string('updated_ip')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_servers');
    }
};
