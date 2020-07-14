<?php

namespace App\Services;

use App\MgRolePrivilege;
use App\PcPrivilegesAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MongoService
{

    public function __construct()
    {

    }


    /**
     * Verificar la conexion con mongodb
     *
     * @return void
     */
    public function CheckConnection() {

        $connectMongo = true;

        try{
            $connection = DB::connection('mongodb');
            $dbs = $connection->getMongoClient()->listDatabases();
        } catch (\Exception $e) {
            Log::info(" Mongo db connection error App\Services\MongoService - CheckConnection -- ".$e);
            $connectMongo = false;
        }

        return $connectMongo;

    }

      /**
     * registrar usuario, rol y privilegios en cache mongodb
     *
     * @param  int $user_id
     * @param  int $rol_id
     * @param  App\PcRole $rol
     *
     */
    public function RegisterCache(int $user_id, int $rol_id, $rol) {

        try{

            $user = Auth::user();
            $arrayMg["id_user"] = $user_id;
            $arrayMg["firstname"] = $user->firstname;
            $arrayMg["lastname"] = $user->lastname;
            $arrayMg['id_rol'] = $rol_id;
            $arrayMg['rol_name'] = $rol->name;
            $arrayMg['privilege'] = PcPrivilegesAction::whereHas('pc_privileges_roles', function(Builder $query) use ($rol_id) {
                                        $query->where('id', $rol_id);
                                    })->get()->toArray();
            MgRolePrivilege::create($arrayMg);

        } catch (\Exception $e) {
            //var_dump("dentro de e ".$e);
            Log::info(" Mongo error App\Services\MongoService - RegisterCache  -- ".$e);
        }

    }

}
