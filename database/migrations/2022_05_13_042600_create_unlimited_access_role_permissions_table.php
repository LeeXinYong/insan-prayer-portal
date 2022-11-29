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
        Schema::create('unlimited_access_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('unlimited_access_role_id');

            $table->foreign(['permission_id', 'role_id'], 'unlimited_access_role_permissions_permission_id')->references(['permission_id', 'role_id'])->on('role_has_permissions')->restrictOnDelete();
            $table->foreign('unlimited_access_role_id', 'unlimited_access_role_permissions_access_id')->references('id')->on('unlimited_access_roles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unlimited_access_role_permissions');
    }
};
