<?php

Route::group([
    'middleware' => ['web'],
], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('resource/file/{url}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\ResourceController@getFile',
            'as'   => 'resource.get-file'
        ]);

        Route::delete('resource/file/{url}', [
            'uses' => '\Laradium\Laradium\Http\Controllers\ResourceController@destroyFile',
            'as'   => 'resource.destroy-file'
        ]);
    });
});