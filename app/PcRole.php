<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * One and Many with model PcRoleLg
     *
     *
     */
    public function pc_role_lg() {
        return $this->hasMany('App\PcRoleLg', 'pc_roles_id');
    }

    /**
     * Many to Many with pc_users_roles
     *
     *
     */
    public function pc_users_roles() {
        return $this->belongsToMany('App\UsUser', 'pc_users_roles', 'pc_roles_id', 'us_users_id');
    }

    /**
     * Many to Many with pc_privileges_actions
     *
     *
     */
    public function pc_privileges_actions() {
        return $this->belongsToMany('App\PcPrivilegesAction', 'pc_privileges_roles', 'pc_roles_id', 'pc_privileges_action_name');
    }

    /* ************************************************************************************************************************************/
    /* ************************************************************************************************************************************/
    /* ************************************************************************************************************************************/

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

    /* ************************************************************************************************************************************/
    /* ************************************************************************************************************************************/
    /* ************************************************************************************************************************************/

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


     /**
     * scope es un a method de Model Eloquent
     * Verificar si el us_users_id y pc_roles_id existe en la tabla pc_users_roles
     *
     * @param  App\PcRole $query
     * @param  int $user_id
     * @return App\PcRole
     */
    public function scopeVerifyUserRole($query, $user_id, $role_id) {
        $result = $query// ->where('id', $user_id)
                     ->whereHas('pc_users_roles', function($query) use ($user_id) {
                        return $query->where('id', $user_id);
                     })
                     ;

        if(!$result->exists()) { // si no existe
            return self::findOrFail($role_id);
        }

        return abort(Response::HTTP_FORBIDDEN, "This user has previously been registered.");
    }


    /**
     * Se obtiene el rol con los privilegios que posee y con los que no posee
     * el nuevo campo check_rol indica si el rol posee el privilegio
     *
     * @param  App\PcRole $query
     * @param  int $pc_role_id
     * @return App\PcRole
     */
    public function scopeRoleWithPrivilege($query, int $pc_role_id) {

        // $result = $query->witch
        $result1 = $query->with('pc_privileges_actions')->findOrFail($pc_role_id);
        $array_action_name = Arr::pluck($result1->pc_privileges_actions, ['action_name']);

        $sql = "CASE action_name ";
        foreach($array_action_name as $key => $value) {
            $sql = $sql . "WHEN '$value' THEN true ";
        }
        $sql = $sql . "ELSE false END as check_rol"; // check_rol. Si es true es un rol que le pertenece, si es false el privilegio no le pertenece

        $qu4 = function($query) use ($sql) {
            return $query->select('*', DB::raw($sql))
                         ->with('pc_privileges_action_name');
        };

        $qu3 = function($query) use ($qu4, $sql) {
            return $query->select('*', DB::raw($sql))
                         ->with(['pc_privileges_action_name' => $qu4]);
        };

        $qu2 = function($query) use ($qu3, $sql) {
            return $query->select('*', DB::raw($sql))
                         ->with(['pc_privileges_action_name' => $qu3]);
        };

        $qu1 = function($query) use ($qu2, $sql) {
            return $query->select('*', DB::raw($sql))
                         ->with(['pc_privileges_action_name' => $qu2]);
        };

        $qu = function($query) use ($qu1, $sql) {
            return $query->select('*', DB::raw($sql))
                         ->with(['pc_privileges_action_name' => $qu1]);
        };

        $result2 = PcPrivilegesAction::select('*', DB::raw($sql))
                                     ->whereIn('action_name', ['store', 'master'])
                                     ->with(['pc_privileges_action_name' => $qu])
                                     ->get();


        $role = self::findOrFail($pc_role_id);
        $collection = collect([$role]);
        $newResult = $collection->map( function($item) use ($result2) {
            $item->pc_privileges_actions = $result2->toArray();
            return $item;
        });

        return $newResult->first();

    }




}
