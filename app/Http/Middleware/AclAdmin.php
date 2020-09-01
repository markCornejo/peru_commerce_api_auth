<?php

namespace App\Http\Middleware;

use App\MgRolePrivilege;
use App\PcPrivilegesAction;
use App\PcRole;
use App\Services\ApiSite\SiteService as ApiSiteSiteService;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AclAdmin
{

    /**
     * The service to consume the site service
     *
     * @var SiteService
     */
    public $_apiSiteSiteService;

    public function __construct(ApiSiteSiteService $apiSiteSiteService)
    {
        $this->_apiSiteSiteService = $apiSiteSiteService;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $ACL)
    {

        $user_id = Auth::id();
        $site = (int) $request->route('site');
        $lang = $request->route('lang');
        $connectMongo = true;
        // $package_id = 1; // obtener paquete de api-per-site. package_id en la tabla pc_sites
        $sitedata = $this->_apiSiteSiteService->simple($lang, $site);
        $package_id = json_decode($sitedata)->data->sales_packages_id;

        if($connectMongo && MgRolePrivilege::verifyRolPrivilege($user_id, $site, $ACL)) { // si connectMongo = true y está en cache mongo entonces. mongodb
            return $next($request);
        } else {

            $rol = PcRole::whereHas('pc_users_roles', function(Builder $query) use ($user_id, $site) {
                                $query->where('id', $user_id)
                                    ->where('pc_users_roles.pc_sites_id', $site); // null para cuando es un usuario manager master/superadmin
                            })
                            ->where('status', 1)
                            ->first();

            if(@$rol) { // si es manager

                $rol_id = $rol->id;
                $privilege = PcPrivilegesAction::whereHas('privileges_roles_admin', function(Builder $query) use ($rol_id, $package_id) {
                                                    $query->where('id', $rol_id)
                                                          ->where('pc_privileges_roles_admin.pc_sales_packages_id', $package_id); // se coloca el nombre de la tabla pc_privileges_roles_admin
                                                })
                                                ->where('action_name', $ACL)
                                                ->where('type_id', 1)
                                                ->where('status', 1)
                                                ->first();

                if($privilege) {

                    MgRolePrivilege::registerAclCache($user_id, $rol_id, $rol, $site); // registrar eb mongodb
                    return $next($request);
                }
            }
        }

        return $this->withMongoOfMaster($request, $next, 'master');

    }


    /**
     * Funcion que usa mongodb como caché y verificar el rol con los privilegios
     *
     */
    public function withMongoOfMaster($request, Closure $next, $ACL)
    {
        $user_id = Auth::id();
        // $connectMongo = $this->_serviceMongo->CheckConnection();
        $connectMongo = true;

        if($connectMongo && MgRolePrivilege::verifyRolPrivilege($user_id, 0, $ACL)) { // si connectMongo = true y está en cache mongo entonces. mongodb
            return $next($request);
        } else {

            $rol = PcRole::whereHas('pc_users_roles', function(Builder $query) use ($user_id) {
                            $query->where('id', $user_id)
                                  ->whereNull('pc_users_roles.pc_sites_id'); // null para cuando es un usuario manager master/superadmin
                         })
                         ->where('status', 1)
                         ->first()
                         ;

            if(@$rol) {
                $rol_id = $rol->id;
                $privilege = PcPrivilegesAction::whereHas('privileges_roles_master', function(Builder $query) use ($rol_id) {
                                                    $query->where('id', $rol_id);
                                                })
                                                ->where('action_name', $ACL)
                                                ->where('status', 1)
                                                ->first();

                if($privilege) {
                    MgRolePrivilege::registerAclCache($user_id, $rol_id, $rol, 0); // registrar eb mongodb
                    return $next($request);
                }
            }
        }

        // abort(Response::HTTP_FORBIDDEN, "You have sent incorrect data.");
        throw new AuthorizationException();
    }
}
