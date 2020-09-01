<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
    Rutas para login user table ususers
*/

Route::group(['prefix' => 'v1/{lang}', 'middleware' => 'lang'], function () {

    Route::post('login', 'UsUserController@login'); // login
    Route::post('login/check', 'UsUserController@check')->middleware('auth:api'); // check login

    Route::group(['prefix' => 'sites'], function () {

        // Route::post('{pc_sites_id}/users/register', 'UsUserController@register');
        Route::post('{pc_sites_id}/users', 'UsUserController@store'); // registro de usuario
        Route::post('{pc_sites_id}/users/login', 'UsUserController@login'); // login

        Route::group(['middleware' => 'auth:api'], function () {
            Route::put('{pc_sites_id}/users', 'UsUserController@update'); //actualizar datos del usuario
            Route::get('{pc_sites_id}/home', 'HomeController@prueba');
            Route::post('{pc_sites_id}/users/logout', 'UsUserController@logout');
        });

    });

    Route::group(['prefix' => 'admin', 'middleware' => 'auth:api'], function () {
        Route::apiResource('sites.managers', 'Admin\ManagerController')->shallow();
    });

    Route::group(['prefix' => 'master', 'middleware' => 'auth:api'], function () {
        Route::apiResource('roles', 'Master\RolesController');
        Route::apiResource('roles.privilege', 'Master\PrivilegeController')->shallow();
        Route::apiResource('roles.packages.privileges', 'Master\PrivilegePackageController')->shallow();
        Route::apiResource('manager', 'Master\ManagerController');
        // Route::apiResource('privilege', 'Master\PrivilegeController');
    });

});
