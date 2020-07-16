<?php

namespace App\Services;

use App\MgRolePrivilege;
use App\PcPrivilegesAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
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

}
