<?php

namespace App\Http\Controllers\Master;

use App\PcRole;
use App\MgRolePrivilege;
use App\Traits\ApiResponserGateway;
use App\Http\Requests\PrivilegePackageMasterRequest;
use App\Http\Resources\PrivilegePackageMaster as PrivilegePackageMasterResources;
use App\Services\ApiSite\PackageService as ApiSitePackageService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PrivilegePackageController extends Controller
{

    use ApiResponserGateway;

     /*
     * The service to consume the site service
     *
     * @var SiteService
     */
    protected $_apiSitePackageService;

    public function __construct(ApiSitePackageService $apiSitePackageService)
    {
        $this->_apiSitePackageService = $apiSitePackageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pc_role_id = (int) $request->route('role');
        $role_privilege = PcRole::roleWithPrivilege($pc_role_id, 1);
        return $this->successResponse(true, new PrivilegePackageMasterResources($role_privilege));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PrivilegePackageMasterRequest $request)
    {
        //
        $pc_role_id = (int) $request->route('role');
        $lang = $request->route('lang');
        $package_id = (int) $request->route('package');
        $array_action_name = Arr::pluck($request->privileges_packages, ['pc_privileges_action_name']);

        PcRole::where('type_id', 1)->findOrFail($pc_role_id); // verificar que sea tipo 1, admin

        $this->_apiSitePackageService->show($lang, $package_id); // package, verificar con el api-per-site

        // esta validación es para idetificar si se escogio solo privilegios de store o sólo de master. No puede entremezclarse privilegios.
        $actname = array_search('store', $array_action_name); // Store es un tipo de privilegio. store es para privilegios del admin.
        if(@$actname >= 0 && $actname !== false) {
            if(count(@preg_grep('/^master.*/', $array_action_name)) > 0) { // si encuentra un resultado entonces generar un error. 'master' viene a ser una palabra registrada en la base de datos como privilegio de nivel 0. Los hijos de master deben contener en su nombre la palabra master. For example master.son.show
                // mostrar error
                return abort(Response::HTTP_FORBIDDEN, "You have sent incorrect data.");
            }
        }

        DB::transaction(function () use ($pc_role_id, $array_action_name, $package_id) {
            PcRole::findOrFail($pc_role_id)->privileges_roles_admin()->detach();
            PcRole::findOrFail($pc_role_id)->privileges_roles_admin()->attach($array_action_name, ["pc_sales_packages_id" => $package_id]);
            MgRolePrivilege::changeRolIdStatusAclCache($pc_role_id, 2); // actualizar caché mongodb
        });

        return $this->successResponse(true, [], '', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PcRole  $pcRole
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        /*
        $privilegepackage = $request->route('privilegepackage');
        $privilege = PrivilegesActionPackage::where('pc_privileges_action_name', $privilegepackage)->get();
        if(count($privilege) == "0") abort(404);
        return $this->successResponse(true, new PrivilegePackageMasterResources($privilege));
        */
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PcRole  $pcRole
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PcRole $pcRole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PcRole  $pcRole
     * @return \Illuminate\Http\Response
     */
    public function destroy(PcRole $pcRole)
    {
        //
    }
}
