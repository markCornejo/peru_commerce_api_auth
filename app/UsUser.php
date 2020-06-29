<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class UsUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'us_users';
    // protected $guard = 'api';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', "lastname", 'email', 'password', "phone", "address", "birthdate", "genere", "photo", "remember_token", "date_add", "date_upd", "user_upd", "pc_countries_id"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    */
}
