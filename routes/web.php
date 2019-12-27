<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth', 'web']], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // New Webhook
    Route::post('/dashboard/new', 'DashboardController@create');
    Route::get('/dashboard/new', 'DashboardController@new');

    // Update Webhook
    Route::patch('/dashboard/edit/{hook}', 'DashboardController@update');
    Route::get('/dashboard/edit/{hook}', 'DashboardController@show');

    // Update Runners
    Route::delete('/dashboard/edit/{hook}/{tracker}', 'DashboardController@deleteRunner');
    Route::put('/dashboard/edit/{hook}', 'DashboardController@addRunner');

    // Get Token
    Route::get('/token', 'TokenController@generateToken');
});

Route::get('/login', 'LoginController@redirectToProvider')->name('login');
Route::get('/login/callback', 'LoginController@handleProviderCallback');
