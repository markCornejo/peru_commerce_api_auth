<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PcRoleLg extends Model
{
    //
    protected $table = 'pc_roles_lg';
    protected $primaryKey = 'pc_roles_id';
    // protected $guard = 'api';

    public $timestamps = false;


    protected $fillable = [
        'name', "description", 'language'
    ];
}
