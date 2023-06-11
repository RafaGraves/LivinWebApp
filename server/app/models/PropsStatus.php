<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropsStatus extends Model
{
    use HasFactory;

    protected $table = 'prop_status';

    protected $primaryKey = 'id_status';


    public $timestamps = false;

    protected $fillable = [
        'id_status',
        'status_nombre',
        'status_nota'
    ];

    protected $attributes = [
        'status_nombre' => '',
        'status_nota' => ''
    ];
}
