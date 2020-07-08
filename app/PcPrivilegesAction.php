<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PcPrivilegesAction extends Model
{
    //
    protected $table = 'pc_privileges_actions';
    protected $primaryKey = 'action_name';
    protected $keyType = 'string';
    // protected $guard = 'api';
    public $timestamps = false;


    /**
     * Many to Many with pc_privileges_actions
     *
     *
     */
    public function pc_privileges_actions() {
        return $this->belongsToMany('App\PcRole', 'pc_privileges_roles', 'action_name', 'pc_roles_id');
    }


    /**
     * one to Many with pc_privileges_action_name es una tabla consigo misma
     *
     *
     */
    public function pc_privileges_action_name() {
        return $this->hasMany('App\PcPrivilegesAction', 'pc_privileges_action_name');
    }


}
