<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth', 'web']], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard')->middleware('auth');
    Route::get('/token', 'TokenController@generateToken');
});

Route::get('/login', 'LoginController@redirectToProvider')->name('login');
Route::get('/login/callback', 'LoginController@handleProviderCallback');
