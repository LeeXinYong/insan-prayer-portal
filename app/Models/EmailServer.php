<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class EmailServer extends Model
{
    use HasFactory, Encryptable;

    protected $encryptable = [
        'mail_host',
        //'mail_port',
        'mail_username',
        'mail_password',
        //'mail_name',
        //'mail_address',
        //'mail_encryption',
        'mail_domain',
        'mail_secret',
    ];
}
