<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class UserLog extends Model
{
    use HasFactory;


    protected $table = 'bitacora_usr';

    protected $primaryKey = 'id_bitacora';

    public $timestamps = false;

    protected $attributes = [
        'id_usr' => '',
        'bit_entrada' => '',
        'bit_salida' => '1000-01-01 00:00:00'
    ];
}
