<?php

namespace App\Http\Controllers\Admin;

use App\Traits\ApiResponserGateway;
use App\UsUser;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    use ApiResponserGateway;

    public function __construct()
    {
        $this->middleware('ACL.admin:store')->only(['index', 'store', 'show', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data = $request->all();
        $site = (int) $request->route('site');
        $user_id = $data['user_id'];
        $role_id = $data['role_id'];

        $user = new UsUser();
        $user->storeRoleAdmin($user_id, $role_id, $site);
        // $site // verificar site, api-per-site
        return $this->successResponse(true, [], "", 201);
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
    public function update(Request $request, UsUser $usUser)
    {
        //
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
