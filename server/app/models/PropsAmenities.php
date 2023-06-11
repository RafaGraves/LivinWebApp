<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropsAmenities extends Model
{
    use HasFactory;

    protected $table = 'prop_ameni';

    protected $primaryKey = 'id_amenidad';


    public $timestamps = false;

    protected $fillable = [
        'id_amenidad',
        'ameni_nombre',
        'ameni_nota'
    ];

    protected $attributes = [
        'ameni_nombre' => '',
        'ameni_nota' => ''
    ];
}
