<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('me', function (Request $request) {
        return \App\Models\User::find($request->user()->id);
    });

    //Webhooks
    Route::get('webhooks', 'WebhookController@index');
    Route::get('/webhooks/{hook}', 'WebhookController@show');
    Route::post('webhooks', 'WebhookController@create');

    //Trackers00
    Route::get('trackers', 'TrackerController@index');
    Route::get('/trackers/{tracker}', 'TrackerController@show');

    Route::post('trackers', 'TrackerController@create');
});
