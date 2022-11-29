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
    public function up(): void
    {
        Schema::create('localized_fields', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->string('language_code')->nullable();
            $table->string('field_name')->nullable();
            $table->json('field_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('localized_fields');
    }
};
