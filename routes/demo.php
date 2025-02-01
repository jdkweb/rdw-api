<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/'  . config('rdw-api.rdw_api_folder')], function () {
    Route::get('/'.config('rdw-api.rdw_api_demo_slug').'/change-language/{language}', function ($language) {
        return redirect('/' . config('rdw-api.rdw_api_folder') . '/' . config('rdw-api.rdw_api_demo_slug').'/'.$language);
    });

    Route::match(array('GET', 'POST'),
        '/'.config('rdw-api.rdw_api_demo_slug').'/{language?}',
        [\Jdkweb\Rdw\Demo\RdwApiDemo::class, 'showForm']);
});
