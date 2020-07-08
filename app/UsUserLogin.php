<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsUserLogin extends Model
{
    protected $table = 'us_userlogins';
    // protected $guard = 'api';

    public $timestamps = false;
}
