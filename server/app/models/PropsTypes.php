<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropsTypes extends Model
{
    use HasFactory;

    protected $table = 'prop_tipos';

    protected $primaryKey = 'id_tipo_propiedad';

    public $timestamps = false;

    protected $fillable = [
        'id_tipo_propiedad',
        'tipo_nombre',
        'tipo_nota'
    ];

    protected $attributes = [
        'tipo_nombre' => '',
        'tipo_nota' => ''
    ];
}
