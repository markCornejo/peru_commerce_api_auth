<?php

namespace App\Http\Controllers\ApiSite\Admin;

use App\Traits\ApiResponser;
use App\Services\ApiSite\SiteService as ApiSiteSiteService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SiteController extends Controller
{

    use ApiResponser;

    /**
     * The service to consume the site service
     *
     * @var SiteService
     */
    public $_apiSiteSiteService;

    /**
     * create a new controller instance.
     *
     * @return void
     */
    public function __construct(ApiSiteSiteService $apiSiteSiteService)
    {
        $this->_apiSiteSiteService = $apiSiteSiteService;
        $this->middleware('ACL.admin:admin')->only(['index', 'store', 'show', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // $lang = $request->route('lang');
        // return $this->successResponse($this->_apiSiteService->getSiteIndex($lang));
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
        $lang = $request->route('lang');
        return $this->successResponse($this->_apiSiteSiteService->siteStore($lang, $request->all()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $lang = $request->route('lang');
        $site = $request->route('site');
        return $this->successResponse($this->_apiSiteSiteService->getSiteIndex($lang, $site));
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
        $lang = $request->route('lang');
        $site = $request->route('site');
        return $this->successResponse($this->_apiSiteSiteService->siteUpdate($lang, $site, $request->all()));
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
