<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'namespace'  => 'Admin',
    'middleware' => ['web'],
], function () {
    Route::group(['middleware' => 'laradium'], function () {
        Route::get('/', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@index',
            'as'   => 'index'
        ]);

        Route::get('dashboard', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@dashboard',
            'as'   => 'dashboard'
        ]);

        Route::post('logout', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\LoginController@logout',
            'as'   => 'logout'
        ]);

        Route::delete('resource/{id}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@resourceDelete',
            'as'   => 'resource.destroy'
        ]);

        Route::delete('resource/{model}/{id}/file/{file}/{locale?}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@destroyFile',
            'as'   => 'resource.destroy-file'
        ]);
    });

    // Auth
    Route::get('login', [
        'uses' => '\Laradium\Laradium\Http\Controllers\Admin\LoginController@index',
        'as'   => 'login'
    ]);

    Route::post('login', [
        'uses' => '\Laradium\Laradium\Http\Controllers\Admin\LoginController@login',
        'as'   => 'login'
    ]);
});