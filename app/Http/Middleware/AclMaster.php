<?php

namespace App\Http\Middleware;

use App\MgRolePrivilege;
use App\PcPrivilegesAction;
use App\PcRole;
use App\UsUser;
use App\Services\MongoService;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AclMaster
{

    /**
     * _serviceMongo
     * Servicios, funciones para mongo
     */
    protected $_serviceMongo;

    public function __construct(MongoService $serviceMongo)
    {
        $this->_serviceMongo = $serviceMongo;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ACL
     * @return mixed
     */
    public function handle($request, Closure $next, $ACL)
    {

        return $this->withMongo($request, $next, $ACL);
        // return $this->noMongo($request, $next, $ACL);
    }

    /**
     * Funcion que usa mongodb como caché
     *
     */
    public function withMongo($request, Closure $next, $ACL)
    {
        $user_id = Auth::id();
        // $connectMongo = $this->_serviceMongo->CheckConnection();
        $connectMongo = true;

        if($connectMongo && MgRolePrivilege::verifyRolPrivilege($user_id, $ACL)) { // si connectMongo = true y está en cache mongo entonces
            return $next($request);
        } else {

            $rol = PcRole::whereHas('pc_users_roles', function(Builder $query) use ($user_id) {
                            $query->where('id', $user_id)
                                  ->whereNull('pc_users_roles.pc_sites_id'); // null para cuando es un usuario manager master/superadmin
                         })
                         ->first()
                         ;

            if(@$rol) {
                $rol_id = $rol->id;
                $privilege = PcPrivilegesAction::whereHas('pc_privileges_roles', function(Builder $query) use ($rol_id) {
                                                    $query->where('id', $rol_id);
                                                })
                                                ->where('action_name', $ACL)
                                                ->first();

                if($privilege) {

                    $this->_serviceMongo->RegisterCache($user_id, $rol_id, $rol);
                    return $next($request);
                }

            }
        }

        // abort(Response::HTTP_FORBIDDEN, "You have sent incorrect data.");
        throw new AuthorizationException();
    }


    /**
     * Funcion que no usa mongodb no usa caché
     *
     */
    public function noMongo($request, Closure $next, $ACL)
    {
        $user_id = Auth::id();
        $rol = PcRole::whereHas('pc_users_roles', function(Builder $query) use ($user_id) {
                        $query->where('id', $user_id)
                                ->whereNull('pc_users_roles.pc_sites_id'); // null para cuando es un usuario manager master/superadmin
                        })
                        ->first()
                        ;

        if(@$rol) {
            $rol_id = $rol->id;
            $privilege = PcPrivilegesAction::whereHas('pc_privileges_roles', function(Builder $query) use ($rol_id) {
                                                $query->where('id', $rol_id);
                                            })
                                            ->where('action_name', $ACL)
                                            ->first();

            if($privilege) {
                return $next($request);
            }

        }
        // abort(Response::HTTP_FORBIDDEN, "You have sent incorrect data.");
        throw new AuthorizationException();
    }

}
