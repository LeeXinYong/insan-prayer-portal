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
        Schema::table("failed_job_webhooks", function (Blueprint $table) {
            $table->longText("endpoint")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("failed_job_webhooks", function (Blueprint $table) {
            $table->string("endpoint")->change();
        });
    }
};
