<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'user'], function() {
    Route::post('/create', 'App\Http\Controllers\User\UserController@createUser');
    Route::get('/{id}', 'App\Http\Controllers\User\UserController@getUser');
    Route::get('/', 'App\Http\Controllers\User\UserController@getAll');
});

Route::group(['prefix' => 'transaction'], function() {
    
});
