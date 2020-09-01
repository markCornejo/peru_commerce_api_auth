<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1/{lang}', 'middleware' => 'lang'], function () {

    // rutas libres
    Route::group(['prefix' => 'sites'], function () {

    });

    // rutas admin
    Route::group(['prefix' => 'admin', 'middleware' => 'auth:api'], function () {
        Route::apiResource('sites', 'ApiSite\Admin\SiteController');
        // Route::apiResource('sites.images', 'ApiSite\Admin\ImageController')->shallow()->except(['show', 'update', 'destroy']);

        Route::group(['prefix' => 'sites/{site}'], function () {
            Route::apiResource('images', 'ApiSite\Admin\ImageController');
        });
        // Route::apiResource('site.categorysubcategory', 'Admin\CategorySubcategoryController')->shallow();
    });


    Route::group(['prefix' => 'master'], function () {
        // Route::apiResource('salespackage', 'Master\SalesPackageController');
    });

});
