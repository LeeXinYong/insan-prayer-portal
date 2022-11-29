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
        Schema::table("videos", function (Blueprint $table) {
            $table->string("video_type")->default("upload")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("videos", function (Blueprint $table) {
            $table->integer("video_type")->default(1)->change(); //1 = upload, 2 = youtube
        });
    }
};
