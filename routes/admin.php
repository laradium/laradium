<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'namespace'  => 'Admin',
    'middleware' => ['web'],
], function () {
    Route::group(['middleware' => 'aven'], function () {
        Route::get('/', [
            'uses' => '\Netcore\Aven\Http\Controllers\Admin\AdminController@index'
        ]);

        Route::get('dashboard', [
            'uses' => '\Netcore\Aven\Http\Controllers\Admin\AdminController@dashboard'
        ]);

        Route::post('logout', [
            'uses' => '\Netcore\Aven\Http\Controllers\Admin\LoginController@logout'
        ]);

        Route::delete('resource/{id}', [
            'uses' => '\Netcore\Aven\Http\Controllers\Admin\AdminController@resourceDelete'
        ]);
    });

    // Auth
    Route::get('login', [
        'uses' => '\Netcore\Aven\Http\Controllers\Admin\LoginController@index'
    ]);

    Route::post('login', [
        'uses' => '\Netcore\Aven\Http\Controllers\Admin\LoginController@login'
    ]);
});