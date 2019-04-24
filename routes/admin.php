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

        Route::get('/attachments', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AttachmentController@attachments',
            'as'   => 'attachment.get'
        ]);

        Route::post('/attachments', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AttachmentController@store',
            'as'   => 'attachment.store'
        ]);

        Route::post('/attachments/upload', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AttachmentController@upload',
            'as'   => 'attachment.upload'
        ]);

        Route::delete('/attachments/{attachment}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AttachmentController@destroy',
            'as'   => 'attachment.destroy'
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

        Route::get('resource/file/{url}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\Admin\AdminController@getFile',
            'as'   => 'resource.get-file'
        ]);

        Route::delete('resource/{model}/{id}/file/{file}', [
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