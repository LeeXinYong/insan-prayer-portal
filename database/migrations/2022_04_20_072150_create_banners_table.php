<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('banner_name')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('banner_size')->nullable();
            $table->string('url')->nullable();
            $table->integer('order')->default(0);
            $table->integer('status')->default(1);
            $table->uuid('added_by')->nullable();
            $table->string('added_ip')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->string('updated_ip')->nullable();
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
        Schema::dropIfExists('banners');
    }
};
