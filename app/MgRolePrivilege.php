<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Colleccion para registrar a los usuarios con roles y sus privilegios. Ayuda para hacer la consulta más rapida.
 */
class MgRolePrivilege extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'mg_role_privilege';

    const CREATED_AT = "date_add";
    const UPDATED_AT = "date_upd";

    protected $fillable = [
        'id_user',
        'firstname',
        'lastname',
        'id_role',
        'role_name',
        'firstname',
        'privilege',
    ];


    /* ************************************************************************************************************************************/
    /* *********************************************************** SCOPE ******************************************************************/
    /* ************************************************************************************************************************************/

    /**
     * Verifivar rol y privilegio
     *
     * @param  App\MgRolePrivilege $query
     * @param  int $user_id
     * @param  string $ACL
     * @return App\MgRolePrivilege
     */
    public function scopeVerifyRolPrivilege($query, int $user_id, $ACL) {
        return $query->where('id_user', $user_id)
                     ->where(
                        'privilege',
                        'elemMatch',
                        ['action_name' => $ACL]
                     )
                     ->count()
                     ;
    }

    /**
     * registrar usuario, rol y privilegios en cache mongodb
     *
     * @param  App\MgRolePrivilege $query
     * @param  int $user_id
     * @param  int $rol_id
     * @param  App\PcRole $rol
     * @return App\MgRolePrivilege
     */
    public function scopeRegisterAclCache($query, int $user_id, int $rol_id, $rol) {

        try{

            $user = Auth::user();
            $arrayMg["id_user"] = $user_id;
            $arrayMg["firstname"] = $user->firstname;
            $arrayMg["lastname"] = $user->lastname;
            $arrayMg['id_role'] = $rol_id;
            $arrayMg['role_name'] = $rol->name;
            $arrayMg['privilege'] = PcPrivilegesAction::whereHas('pc_privileges_roles', function(Builder $query) use ($rol_id) {
                                        $query->where('id', $rol_id);
                                    })->get()->toArray();
            $query = $query->create($arrayMg);

        } catch (\Exception $e) {
            // Log::info(" Mongo error App\Services\MongoService - RegisterCache  -- ".$e);
            abort(500, "Mongo error App\Services\MongoService - RegisterCache ". $e);

        }

        return $query;
    }



    /**
     * eliminar cache por rol segun estado
     *
     * @param  App\MgRolePrivilege $query
     * @param  int $role_id
     * @param  int $status
     * @return App\MgRolePrivilege
     */
    public function scopeChangeRolIdStatusAclCache($query, int $role_id, $status) {

        if($status !== null && @$status != "1") {
            try{
                $query = $query->where('id_role', $role_id)->delete();
            } catch (\Exception $e) {
                // Log::info(" Mongo error App\Services\MongoService - changeStatusCache  -- ".$e);
                abort(500, "Mongo error App\Services\MongoService - changeStatusCache ".$e);
            }
        }
        return $query;
    }

    /**
     * eliminar cache por usuario
     *
     * @param  App\MgRolePrivilege $query
     * @param  int $role_id
     * @param  int $status
     * @return App\MgRolePrivilege
     */
    /*
    public function scopeChangeUserIdAclCache($query, int $user_id) {

        if($status !== null && @$status != "1") {
            try{
                $query = $query->where('id_role', $role_id)->delete();
            } catch (\Exception $e) {
                // Log::info(" Mongo error App\Services\MongoService - changeStatusCache  -- ".$e);
                abort(500, "Mongo error App\Services\MongoService - changeStatusCache ".$e);
            }
        }
        return $query;
    }
    */

}
