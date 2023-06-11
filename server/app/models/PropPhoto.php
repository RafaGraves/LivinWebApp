<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropPhoto extends Model
{
    use HasFactory;

    protected $table = 'foto';

    protected $primaryKey = 'id_foto';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_foto',
        'id_propiedad',
        'id_categoria',
        'foto_nota'
    ];

    protected $attributes = [
        'foto_nota' => ''
    ];
}
