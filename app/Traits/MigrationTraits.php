<?php


namespace App\Traits;

use Closure;
use Illuminate\Database\Schema\Blueprint;

class MigrationTraits
{
    public static function migrationClosure(): Closure
    {
        return function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("title");
            $table->string("file_name");
            $table->string("file_path");
            $table->string("file_size");
            $table->string("thumbnail_name");
            $table->string("thumbnail_path");
            $table->string("thumbnail_size");
            $table->integer("order")->default(0);
            $table->integer("status")->default(1);
            $table->uuid("added_by");
            $table->string("added_ip");
            $table->uuid("updated_by");
            $table->string("updated_ip");
            $table->timestamps();
        };
    }
}
