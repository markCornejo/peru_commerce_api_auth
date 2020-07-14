<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
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
        'id_rol',
        'rol_name',
        'firstname',
        'privilege',
    ];


    /* ************************************************************************************************************************************/
    /* *********************************************************** SCOPE ******************************************************************/
    /* ************************************************************************************************************************************/

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

}
