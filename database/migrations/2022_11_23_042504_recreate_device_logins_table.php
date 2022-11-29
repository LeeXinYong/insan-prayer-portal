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
        Schema::dropIfExists('device_sessions');
        Schema::dropIfExists('device_logins');

        Schema::create('device_logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('user_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_model')->nullable();
            $table->string('location')->nullable();
            $table->integer('status')->default(0);
            $table->string('session_id')->nullable();
            $table->timestamp('session_expired_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_logins');

        Schema::create('device_logins', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('user_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('token')->nullable();
            $table->string('location')->nullable();
            $table->integer('status')->default(0)->comment("0 - Default, 1 - Trusted, 2 - Blocked");
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('device_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('device_login_id');
            $table->string('session_id')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->foreign('device_login_id')->references('id')->on('device_logins');
        });
    }
};
