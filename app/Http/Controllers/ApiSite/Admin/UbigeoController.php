<?php

namespace App\Http\Controllers\ApiSite\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

use App\Services\ApiSite\UbigeoService as ApiSiteUbigeoService;

class UbigeoController extends Controller
{

    use ApiResponser;

    /**
     * The service to consume the site service
     *
     * @var SiteService
     */
    public $_apiSiteUbigeoService;

    /**
     * create a new controller instance.
     *
     * @return void
     */
    public function __construct(ApiSiteUbigeoService $apiSiteUbigeoService)
    {
        $this->_apiSiteUbigeoService = $apiSiteUbigeoService;
        // $this->middleware('ACL.admin:admin')->only(['index', 'store', 'show', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $lang = $request->route('lang');
        $site = $request->route('site');
        $cod = @$request->input('cod');
        return $this->successResponse($this->_apiSiteUbigeoService->ubigeoIndex($lang, $site, $cod));
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
