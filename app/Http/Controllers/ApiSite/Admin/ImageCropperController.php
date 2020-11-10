<?php

namespace App\Http\Controllers\ApiSite\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Traits\ApiResponser;
use App\Services\ApiSite\SiteService as ApiSiteSiteService;

class ImageCropperController extends Controller
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
        // $this->middleware('ACL.admin:admin')->only(['index', 'store', 'show', 'update', 'destroy']);
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

    public function cut(Request $request) {

        $image = (int) $request->route('image');
        $site = (int) $request->route('site');
        $lang = $request->route('lang');
        $data = $request->all();

        return $this->successResponse($this->_apiSiteSiteService->cutImagesCropper($lang, $site, $image, $data));

    }
}
