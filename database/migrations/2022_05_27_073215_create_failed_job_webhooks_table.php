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
        Schema::create('failed_job_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('secret_key');
            $table->string('endpoint');
            $table->integer('status')->default(1);
            $table->datetime('last_called')->nullable();
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
        Schema::dropIfExists('failed_job_webhooks');
    }
};
