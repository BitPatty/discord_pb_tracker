<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('me', function (Request $request) {
        return \App\Models\User::find($request->user()->id);
    });

    Route::get('webhooks', 'WebhookController@index');
    Route::get('webhooks/{id}', 'WebhookController@show');
    Route::post('webhooks', 'WebhookController@create');

    Route::get('trackers', 'TrackerController@index');
    Route::get('trackers/{id}', 'TrackerController@show');
    Route::post('trackers', 'TrackerController@create');
});
