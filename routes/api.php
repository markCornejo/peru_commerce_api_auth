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
Route::post('user/register', 'UsUserController@register');
Route::post('user/login', 'UsUserController@login');


Route::group(['middleware' => 'auth:api'], function () {
    Route::get('pruebas', 'PruebaController@prueba');
    Route::post('user/logout', 'UsUserController@logout');
});

