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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable();
            $table->string('duration')->nullable()->default('00:00');
            $table->string('thumbnail_name');
            $table->string('thumbnail_path');
            $table->string('thumbnail_size');
            $table->string('youtube_url')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->string('youtube_thumbnail_link')->nullable();
            $table->integer('video_type')->default(1); //1 = upload, 2 = youtube
            $table->integer('order')->default(0);
            $table->integer('status')->default(1);
            $table->uuid('added_by');
            $table->string('added_ip');
            $table->uuid('updated_by');
            $table->string('updated_ip');
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
        Schema::dropIfExists('videos');
    }
};
