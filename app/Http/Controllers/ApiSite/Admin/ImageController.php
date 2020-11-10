<?php

namespace App\Http\Controllers\ApiSite\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

// use App\Traits\ApiResponserGateway;
use App\Services\ApiSite\SiteService as ApiSiteSiteService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    use ApiResponser;
    // use ApiResponserGateway;

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
    public function index(Request $request)
    {
        //
        $lang = $request->route('lang');
        $site = $request->route('site');
        $take = $request->take_image;
        $skip = $request->skip_image;
        return $this->successResponse($this->_apiSiteSiteService->getImages($lang, $site, $skip, $take));

    }

    /**
     * Guardar imagen en Storage/public/tmp y enviar data al api-per-site
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lang = $request->route('lang');
        $site = $request->route('site');
        Storage::makeDirectory(config('services.image_tmp'));
        $tmp = $request->file('file')->storeAs(config('services.image_tmp'), $request->file('file')->getClientOriginalName());
        $name = $request->file('file')->getClientOriginalName();

        $data = [
            "url" => config('services.image.tmp.url').$name,
            "name" => $name,
            "orientation" => 'h',
            "user_add" => Auth::id()
        ];
        // $image = base64_encode(file_get_contents($request->file('file')->path()));
        return $this->successResponse($this->_apiSiteSiteService->setImages($lang, $site, $data));
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
    public function destroy(Request $request, $id)
    {
        //
        $lang = $request->route('lang');
        $site = $request->route('site');
        $image = $request->route('image');
        Storage::makeDirectory(config('services.image_tmp'));
        $result = $this->_apiSiteSiteService->delImages($lang, $site, $image);
        if(@json_decode($result)->ok) {
            Storage::delete(config('services.image_tmp')."/".json_decode($result)->data);
        } else {
            abort(Response::HTTP_FORBIDDEN, "Problemas al eliminar imagen");
        }
        // Storage::delete('public/tmp/'.);
        return $this->successResponse($result);

    }


}
