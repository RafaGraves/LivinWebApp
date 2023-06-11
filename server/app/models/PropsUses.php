<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropsUses extends Model
{
    use HasFactory;

    protected $table = 'prop_uso';

    protected $primaryKey = 'id_uso';


    public $timestamps = false;

    protected $fillable = [
        'id_uso',
        'uso_nombre',
        'uso_nota'
    ];

    protected $attributes = [
        'uso_nombre' => '',
        'uso_nota' => ''
    ];
}
