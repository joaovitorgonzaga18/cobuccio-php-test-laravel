<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', 'App\Http\Controllers\Auth\AuthController@login')->name('login');
Route::middleware('auth:sanctum')->post('/logout', 'App\Http\Controllers\Auth\AuthController@logout')->name('logout');


Route::group(['prefix' => 'user'], function() {
    Route::post('/create', 'App\Http\Controllers\User\UserController@createUser');
    Route::get('/{id}', 'App\Http\Controllers\User\UserController@getUser');
    Route::get('/', 'App\Http\Controllers\User\UserController@getAll');
});

Route::group(['prefix' => 'transaction'], function() {
    Route::patch('/cancel/{id}', 'App\Http\Controllers\Transaction\TransactionController@cancelTransaction');
    Route::middleware('auth:sanctum')->post('/create', 'App\Http\Controllers\Transaction\TransactionController@createTransaction');
    Route::get('/', 'App\Http\Controllers\Transaction\TransactionController@getAll');
    Route::get('/{id}', 'App\Http\Controllers\Transaction\TransactionController@getTransaction');
    Route::get('/user_transactions/{user_id}', 'App\Http\Controllers\Transaction\TransactionController@getAllUserTransactions');
});
