<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\PcRole as PcRoles;
use App\PcRoleLg;

use App\Http\Resources\PcRole as PcRoleResources;
use App\Traits\ApiResponserGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    use ApiResponserGateway;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pcRoles = PcRoles::with("pc_role_lg")->get();
        return $this->successResponse(true, new PcRoleResources($pcRoles));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {

        $role = null;
        DB::transaction(function () use ($request, &$role) {

            $input = $request->all();
            $role = PcRoles::create($input['language_main']);
            if(@$input['language_secondary'] && count($input['language_secondary']) > 0){
                $role->pc_role_lg()->createMany($input['language_secondary']);
            }
        });

        return $this->successResponse(true, new PcRoleResources($role), '', 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PcRoles  $pcRoles
     * @return \Illuminate\Http\Response
     */
    public function show($lang, $role/*PcRoles $pcRoles*/)
    {
        PcRoles::findOrFail($role); // verificar la busqueda
        $pcRoles = PcRoles::selectFirst($role)->first(); // entregar los datos completos con su lenguaje
        return $this->successResponse(true, new PcRoleResources($pcRoles));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PcRoles  $pcRoles
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, PcRoles $pcRoles, $lang, $role)
    {
        $response = true;
        DB::transaction(function () use ($request, $pcRoles, $role, &$response) {
            $input = $request->all();
            $input = $pcRoles->defineDateUpd($input); // definir fecha y usuario que actualiza el registro

            $roless = $pcRoles->findOrFail($role)->fill($input['language_main'])->save();

            PcRoleLg::findOrFail($role)->delete();
            if(@$input['language_secondary'] && count($input['language_secondary']) > 0){
                $response = $pcRoles->findOrFail($role)->pc_role_lg()->createMany($input['language_secondary']);
            }
        });

        return $this->successResponse(true, []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PcRoles  $pcRoles
     * @return \Illuminate\Http\Response
     */
    public function destroy(PcRoles $pcRoles, $role)
    {
        $pcRoles->findOrFail($role)->fill(["status" => 2])->save();
        return $this->successResponse(true, []);
    }
}
