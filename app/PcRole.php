<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PcRole extends Model
{
    //
    protected $table = 'pc_roles';
    // protected $guard = 'api';

    public $timestamps = false;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', "description", 'status', 'date_add', "date_upd", "user_add"
    ];

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return static
    */
    public static function create(array $attributes = [])
    {
        $model = new static($attributes);
        $model = self::defineDateAdd($model);
        $model->save();
        return $model;
    }


    /**
     * One and Many with model PcRoleLg
     *
     * @return void
     */
    public function pc_role_lg() {
        return $this->hasMany('App\PcRoleLg', 'pc_roles_id');
    }


    /**
     * Agregar fecha y usuario a la tabla
     *
     * @param  App\PcRole $array
     * @return App\PcRole
     */
    public static function defineDateAdd($model) {
        $model->date_add = Carbon::now()->format('Y-m-d H:i:s');
        if(@Auth::id()) {
            $model->user_add = @Auth::id();
        }
        return $model;
    }


    /**
     * Agregar fecha y usuario para update/crear
     *
     * @param  array $model
     * @return array
     */
    public static function defineDateUpd(array $model) {
        $model['date_upd'] = Carbon::now()->format('Y-m-d H:i:s');
        if(@Auth::id()) {
            $model['user_upd'] = @Auth::id();
        }
        return $model;
    }


    /**
     * Obtener un rol y sus traducciones
     *
     * @param  App\PcRole $query
     * @param  int $role_id
     * @return App\PcRole
     */
    public function scopeSelectFirst($query, int $role_id) {
        return $query->where("id", $role_id)
                     ->with('pc_role_lg')
                     ;
    }




}
