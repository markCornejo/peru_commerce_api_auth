<?php

namespace App\Http\Controllers\Master;

use App\UsUser;
use App\PcRole;
use App\Traits\ApiResponserGateway;
use App\Http\Requests\ManagerMasterRequest;
use App\Http\Resources\ManagerMaster as ManagerMasterResources;
use App\Http\Resources\ManagerMasterCollection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{

    use ApiResponserGateway;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usUser = UsUser::getFullDataUserRole();
        return $this->successResponse(true, new ManagerMasterCollection($usUser));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManagerMasterRequest $request)
    {
        $input = $request->all();
        $role = PcRole::verifyUserRole($input['user_id'], $input['role_id']); //verificar si existe el usuario y el rol registrados
        $role->pc_users_roles()->attach($input['user_id'], ['user_add' => Auth::id(), 'date_add' => Carbon::now()->format('Y-m-d H:i:s')]);

        return $this->successResponse(true, new ManagerMasterResources(UsUser::getDataUserRole($input['user_id'])));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UsUser  $usUser
     * @return \Illuminate\Http\Response
     */
    public function show(UsUser $usUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UsUser  $usUser
     * @return \Illuminate\Http\Response
     */
    public function update(ManagerMasterRequest $request, UsUser $usUser)
    {
        $user_id = $request->route('manager');
        $role = PcRole::whereHas('pc_users_roles', function($query) use ($user_id) {
            return $query->where('id', $user_id);
        })->firstOrFail();

        $role->pc_users_roles()->detach($user_id);
        $usUser->findOrFail($user_id)->pc_users_roles()->attach($request->role_id, ['user_add' => Auth::id(), 'date_add' => Carbon::now()->format('Y-m-d H:i:s')]);

        return $this->successResponse(true, new ManagerMasterResources(UsUser::getDataUserRole($user_id)));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UsUser  $usUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(UsUser $usUser)
    {
        //
    }
}
