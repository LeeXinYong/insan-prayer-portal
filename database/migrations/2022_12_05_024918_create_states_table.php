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
		Schema::dropIfExists(config('world.migrations.states.table_name'));

        Schema::create('states', function (Blueprint $table) {
            $table->id('state_id');
            $table->string('name');
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
        Schema::dropIfExists('states');
        
        Schema::create(config('world.migrations.states.table_name'), function (Blueprint $table) {
			$table->id();
			$table->foreignId('country_id');
			$table->string('name');

			foreach (config('world.migrations.states.optional_fields') as $field => $value) {
				if ($value['required']) {
					$table->string($field, $value['length'] ?? null);
				}
			}
		});
    }
};
