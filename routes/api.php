<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth.api']], function() {
    Route::post('/logout', 'Auth\LoginController@apiLogout');

    Route::get('/me', 'UserController@index');

    Route::group(['prefix' => 'team'], function() {
        Route::post('/', 'TeamController@store');
        Route::get('/{team}', 'TeamController@show')->where('team', '[0-9]+');
    });

    Route::get('/user/{user}', 'UserController@show')->where('user', '[0-9]+');
});

Route::post('/register', 'Auth\RegisterController@apiRegister');
Route::post('/login', 'Auth\LoginController@apiLogin');