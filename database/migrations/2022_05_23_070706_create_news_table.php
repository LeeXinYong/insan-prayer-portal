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
        Schema::create("news", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("url_content_flag")->default("url");
            $table->longText("url")->nullable();
            $table->longText("content")->nullable();
            $table->string("thumbnail_name")->nullable();
            $table->string("thumbnail_path")->nullable();
            $table->string("thumbnail_size")->nullable();
            $table->timestamp("published_at")->nullable();
            $table->integer("order")->default(0);
            $table->integer("status")->default(1);
            $table->uuid("added_by")->nullable();
            $table->string("added_ip")->nullable();
            $table->uuid("updated_by")->nullable();
            $table->string("updated_ip")->nullable();
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
        Schema::dropIfExists("news");
    }
};
