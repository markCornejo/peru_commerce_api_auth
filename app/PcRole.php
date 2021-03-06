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
     * Many to Many with pc_privileges_roles_master
     *
     *
     */
    public function privileges_roles_master() {
        return $this->belongsToMany('App\PcPrivilegesAction', 'pc_privileges_roles_master', 'pc_roles_id', 'pc_privileges_action_name');
    }

    /**
     * Many to Many with pc_privileges_roles_master
     *
     *
     */
    public function privileges_roles_admin() {
        return $this->belongsToMany('App\PcPrivilegesAction', 'pc_privileges_roles_admin', 'pc_roles_id', 'pc_privileges_action_name');
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
    /* *********************************************************** SCOPE ******************************************************************/
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
                     ->where('status', 0)
                     ->orWhere('status', 1)
                     ->with('pc_role_lg')
                     ;
    }


     /**
     * scope es un a method de Model Eloquent
     * Verificar si el user_id y role_id existe en la tabla pc_users_roles
     *
     * @param  App\PcRole $query
     * @param  int $user_id
     * @param  int $role_id
     * @return App\PcRole
     */
    public function scopeVerifyUserRole($query, int $user_id, int $role_id) {

        $result = $query// ->where('id', $user_id)
                     ->whereHas('pc_users_roles', function($query) use ($user_id) {
                        return $query->where('id', $user_id);
                     })
                     ;
        // $result = PcPrivilegesAction::where('us_users_id', $user_id)->whereNull('')

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
     * @param  int $type_id // 1 = admin, 2 = master
     * @return App\PcRole
     */
    public function scopeRoleWithPrivilege($query, int $pc_role_id, int $type_id) {

        if($type_id === 1) {
            $table_type = "privileges_roles_admin";
        } else {
            $table_type = "privileges_roles_master";
        }

        // $result = $query->witch
        $result1 = $query->with($table_type)->findOrFail($pc_role_id);
        $array_action_name = Arr::pluck($result1->$table_type, ['action_name']);

        $sql = "CASE action_name WHEN 'ejemploFirst' THEN false ";
        foreach($array_action_name as $key => $value) {
            $sql = $sql . "WHEN '$value' THEN true ";
        }
        $sql = $sql . "ELSE false END as check_rol"; // check_rol. Si es true es un rol que le pertenece, si es false el privilegio no le pertenece.

        $qu4 = function($query) use ($sql) {
            return $query->select('action_name', 'label', 'class_icon', 'type_id', 'checkeable', DB::raw($sql), 'pc_privileges_action_name')
                         ->with('pc_privileges_action_name');
        };

        $qu3 = function($query) use ($qu4, $sql) {
            return $query->select('action_name', 'label', 'class_icon', 'type_id', 'checkeable', DB::raw($sql), 'pc_privileges_action_name')
                         ->with(['pc_privileges_action_name' => $qu4]);
        };

        $qu2 = function($query) use ($qu3, $sql) {
            return $query->select('action_name', 'label', 'class_icon', 'type_id', 'checkeable', DB::raw($sql), 'pc_privileges_action_name')
                         ->with(['pc_privileges_action_name' => $qu3]);
        };

        $qu1 = function($query) use ($qu2, $sql) {
            return $query->select('action_name', 'label', 'class_icon', 'type_id', 'checkeable', DB::raw($sql), 'pc_privileges_action_name')
                         ->with(['pc_privileges_action_name' => $qu2]);
        };

        $qu = function($query) use ($qu1, $sql) {
            return $query->select('action_name', 'label', 'class_icon', 'type_id', 'checkeable', DB::raw($sql), 'pc_privileges_action_name')
                         ->with(['pc_privileges_action_name' => $qu1]);
        };

        $result2 = PcPrivilegesAction::select('action_name', 'label', 'class_icon', 'type_id', 'checkeable', DB::raw($sql), 'pc_privileges_action_name')
                                     ->whereIn('action_name', ['store', 'master'])
                                     ->where('type_id', $type_id)
                                     ->where('status', 1)
                                     ->with(['pc_privileges_action_name' => $qu])
                                     ->get();


        $role = self::select('id', 'name', 'description', 'status')->where('status', 1)->findOrFail($pc_role_id);
        $collection = collect([$role]);
        $newResult = $collection->map( function($item) use ($result2) {
            $item->pc_privileges_actions = $result2->toArray();
            return $item;
        });

        return $newResult->first();
    }

}
