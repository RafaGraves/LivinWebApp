<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'propiedades';

    protected $primaryKey = 'id_propiedad';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_propiedad',
        'id_usr',
        'prop_nota',
        'id_status',
        'id_uso',
        'id_tipo_propiedad',
        'prop_codigo',
        'prop_autoriza',
        'prop_precio',
        'id_moneda'
    ];

    protected $attributes = [
        'id_status' => 10,
        'prop_codigo' => '',
        'prop_autoriza' => 0
    ];
}
