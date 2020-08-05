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
     * Many to Many with pc_privileges_roles_master
     *
     *
     */
    public function privileges_roles_master() {
        return $this->belongsToMany('App\PcRole', 'pc_privileges_roles_master', 'pc_privileges_action_name', 'pc_roles_id');
    }

    /**
     * Many to Many with pc_privileges_roles_admin
     *
     *
     */
    public function privileges_roles_admin() {
        return $this->belongsToMany('App\PcRole', 'pc_privileges_roles_admin', 'pc_privileges_action_name', 'pc_roles_id');
    }

    /**
     * one to Many with pc_privileges_action_name es una tabla consigo misma
     *
     *
     */
    public function pc_privileges_action_name() {
        return $this->hasMany('App\PcPrivilegesAction', 'pc_privileges_action_name');
    }

    /**
     * one to Many with pc_privileges_actions_packages relacionada con los paquetes
     *
     *
     */
    public function privileges_action_package() {
        return $this->hasMany('App\PrivilegesActionPackage', 'pc_privileges_action_name');
    }


}
