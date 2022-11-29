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
        Schema::create("changelogs", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("version");
            $table->string("type")->nullable();
            $table->longText("released_by");
            $table->date("released_at");
            $table->longText("description")->nullable();
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
        Schema::dropIfExists("changelogs");
    }
};
