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
            '\Netcore\Aven\Http\Controllers\Admin\AdminController@resourceDelete'
        ]);

        // Translations

        Route::post('translations/import', [
            'uses' => '\Netcore\Aven\Http\Controllers\Admin\TranslationController@import',
            'as'   => 'translations.import'
        ]);

        Route::get('translations/export', [
            'uses' => '\Netcore\Aven\Http\Controllers\Admin\TranslationController@export',
            'as'   => 'translations.export'
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