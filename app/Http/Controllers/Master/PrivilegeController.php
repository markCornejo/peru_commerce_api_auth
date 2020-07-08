<?php

namespace App\Http\Controllers\Master;

use App\Traits\ApiResponserGateway;
use App\PcPrivilegesAction;
use App\PcRole;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivilegeMaster as PrivilegeMasterResources;
use Illuminate\Http\Request;
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

        return $role_privilege;

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
        $array_action_name = Arr::pluck($request->all(), ['pc_privileges_action_name']);
        PcRole::findOrFail($pc_role_id)->pc_privileges_actions()->detach();
        $privileges = PcRole::findOrFail($pc_role_id)->pc_privileges_actions()->attach($array_action_name);
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
