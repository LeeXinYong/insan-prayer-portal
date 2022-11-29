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
        Schema::create('notification_identifiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('notifiable');
            $table->string('identifiable_type');
            $table->string('identifiable_id');
            $table->timestamps();

            $table->index(['identifiable_type', 'identifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_identifiers');
    }
};
