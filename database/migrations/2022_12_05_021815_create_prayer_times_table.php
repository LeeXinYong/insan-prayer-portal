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
        Schema::create('prayer_times', function (Blueprint $table) {
            $table->id('prayer_id');
            $table->integer('state_id');
            $table->string('zone_id');
            $table->string('hijri_date');
            $table->string('gregorian_date');
            $table->string('day');
            $table->string('imsak');
            $table->string('fajr');
            $table->string('syuruk');
            $table->string('dhuhr');
            $table->string('asr');
            $table->string('maghrib');
            $table->string('isha');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prayer_times');
    }
};