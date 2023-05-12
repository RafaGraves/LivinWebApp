<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'usr_det';

    protected $primaryKey = 'id_usr2';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $attributes = [
        'usr2_cel2' => '',
        'usr2_mail2' => '',
        'usr2_foto' => ''
    ];
}
