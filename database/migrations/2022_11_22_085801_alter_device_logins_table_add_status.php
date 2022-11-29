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
        Schema::table('device_logins', function (Blueprint $table) {
            $table->integer('status')->default(0)->comment("0 - Default, 1 - Trusted, 2 - Blocked")->after('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_logins', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
