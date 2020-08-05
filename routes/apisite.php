<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1/{lang}', 'middleware' => 'lang'], function () {

    // rutas libres
    Route::group(['prefix' => 'sites'], function () {

    });

    // rutas admin
    Route::group(['prefix' => 'admin'], function () {
        Route::apiResource('site', 'ApiSite\Admin\SiteController');
        // Route::apiResource('site.categorysubcategory', 'Admin\CategorySubcategoryController')->shallow();
    });


    Route::group(['prefix' => 'master'], function () {
        // Route::apiResource('salespackage', 'Master\SalesPackageController');
    });

});
