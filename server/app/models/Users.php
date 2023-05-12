<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    protected $table = 'usr';

    protected $primaryKey = 'id_usr';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $attributes = [
        'usr_verificado' => 0,
        'usr_entradas' => 0,
        'id_tipo_usr' => 3 // Default: Vendedor-Comprador
    ];
}
