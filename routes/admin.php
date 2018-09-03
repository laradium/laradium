<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'namespace'  => 'Admin',
    'middleware' => ['web'],
], function () {
    Route::group(['middleware' => 'laradium'], function () {
        Route::get('/', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@index'
        ]);

        Route::get('dashboard', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@dashboard'
        ]);

        Route::post('logout', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\LoginController@logout'
        ]);

        Route::delete('resource/{id}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@resourceDelete'
        ]);
    });

    // Auth
    Route::get('login', [
        'uses' => '\Laradium\Laradium\Http\Controllers\Admin\LoginController@index'
    ]);

    Route::post('login', [
        'uses' => '\Laradium\Laradium\Http\Controllers\Admin\LoginController@login'
    ]);
});