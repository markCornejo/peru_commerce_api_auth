<?php

namespace App\Http\Controllers\Master;

use App\Traits\ApiResponserGateway;
use App\PcPrivilegesAction;
use App\PcRole;
use App\Http\Requests\PrivilegeRequest;
use App\Http\Resources\PrivilegeMaster as PrivilegeMasterResources;

use App\Http\Controllers\Controller;
use App\MgRolePrivilege;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PrivilegeController extends Controller
{

    use ApiResponserGateway;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $pc_role_id = (int) $request->route('role');
        $role_privilege = PcRole::roleWithPrivilege($pc_role_id);
        return $this->successResponse(true, new PrivilegeMasterResources($role_privilege));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pc_role_id = (int) $request->route('role');
        $array_action_name = Arr::pluck($request->action_name, ['pc_privileges_action_name']);

        // esta validación es para idetificar si se escogio solo privilegios de store o sólo de master. No puede entremezclarse privilegios.
        $actname = array_search('store', $array_action_name); // Store es un tipo de privilegio. store es para privilegios del admin.
        if(@$actname >= 0 && $actname !== false) {
            if(count(@preg_grep('/^master.*/', $array_action_name)) > 0) { // si encuentra un resultado entonces generar un error. 'master' viene a ser una palabra registrada en la base de datos como privilegio de nivel 0. Los hijos de master deben contener en su nombre la palabra master. For example master.son.show
                // mostrar error
                return abort(Response::HTTP_FORBIDDEN, "You have sent incorrect data.");
            }
        }

        $actname = array_search('master', $array_action_name); // master es un tipo de privilegio. master es para privilegios de superadmin de todo el sistema.
        if(@$actname >= 0 && $actname !== false) {
            if(count(@preg_grep('/^store.*/', $array_action_name)) > 0) { // si encuentra un resultado entonces generar un error. 'store' viene a ser una palabra registrada en la base de datos como privilegio de nivel 0. Los hijos de store deben contener en su nombre la palabra store. For example store.son.show
                // mostrar error
                return abort(Response::HTTP_FORBIDDEN, "You have sent incorrect data.");
            }
        }

        DB::transaction(function () use ($pc_role_id, $array_action_name) {
            PcRole::findOrFail($pc_role_id)->pc_privileges_actions()->detach();
            PcRole::findOrFail($pc_role_id)->pc_privileges_actions()->attach($array_action_name);
            MgRolePrivilege::changeRolIdStatusAclCache($pc_role_id, 2); //actualizar caché mongodb
        });

        return $this->successResponse(true, new PrivilegeMasterResources(PcRole::with('pc_privileges_actions')->findOrFail($pc_role_id)));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PcPrivilegesAction  $pcPrivilegesAction
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, PcPrivilegesAction $pcPrivilegesAction)
    {
        //
        $pc_role_id = $request->route('privilege');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PcPrivilegesAction  $pcPrivilegesAction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PcPrivilegesAction $pcPrivilegesAction)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PcPrivilegesAction  $pcPrivilegesAction
     * @return \Illuminate\Http\Response
     */
    public function destroy(PcPrivilegesAction $pcPrivilegesAction)
    {
        //
    }
}
