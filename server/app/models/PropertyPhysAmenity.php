<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyPhysAmenity extends Model
{
    use HasFactory;

    protected $table = 'amenidades';

    protected $primaryKey = 'id_descripcion';

    public $incrementing = false;


    public $timestamps = false;

    protected $fillable = [
        'id_propiedad',
        'id_amenidad'
    ];

    protected $attributes = [
    ];
}
