<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignupMailConfirmation extends Model
{
    use HasFactory;

    protected $table = 'signup_mail';

    protected $primaryKey = 'url';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
