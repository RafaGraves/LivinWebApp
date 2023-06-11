<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'session';

    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'token',
        'usr_id',
        'ip',
        'userAgent',
        'csrf',
        'expire'
    ];

    protected $attributes = [
        'token' => '',
        'usr_id' => '',
        'ip' => '',
        'userAgent' => '',
        'csrf' => '',
        'active' => 1
    ];
}
