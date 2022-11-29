<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalizedField extends Model
{
    use HasFactory;

    protected $fillable = [
        "subject_type",
        "subject_id",
        "language_code",
        "field_name",
        "field_value",
    ];
}
