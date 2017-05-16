<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'Auth\LoginController@showLoginForm');
Route::group(['prefix' => 'contrasena'], function() {
    Route::get('recuperar', ['as' => 'password.request', 'uses' => 'Auth\ResetPasswordController@showLinkRequestForm']);
    Route::get('recuperar/{token}', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
});

Route::get('/vestuario', ['as' => 'home', 'uses' => 'HomeController@index']);

Route::group(['prefix' => 'perfil'], function() {
    Route::patch('/', ['as' => 'profile', 'uses' => 'AccountSettingsController@update']);
    Route::get('/editar', ['as' => 'profile.edit', 'uses' => 'AccountSettingsController@index']);
    Route::get('/contrasena', ['as' => 'profile.password', 'uses' => 'AccountSettingsController@editPassword']);
    Route::patch('/contrasena', 'AccountSettingsController@updatePassword');
});

Route::group(['prefix' => 'equipo'], function() {
    Route::get('/', 'TeamController@index');
    Route::post('/', ['as' => 'team.store', 'uses' => 'TeamController@store']);
    Route::patch('/', ['as' => 'team', 'uses' => 'TeamController@update']);
    Route::get('/crear', 'TeamController@create');
    Route::get('/editar', ['as' => 'team.edit', 'uses' => 'TeamController@edit']);
    Route::get('/estadisticas/{rival}', ['as' => 'team.statsVersus', 'uses' => 'TeamController@showStatsVersus']);
    Route::post('/estrategia', ['as' => 'team.strategy', 'uses' => 'TeamController@updateStrategy']);
    Route::post('/formacion', ['as' => 'team.formation', 'uses' => 'TeamController@updateFormation']);
});

Route::get('/jugadores', ['as' => 'players', 'uses' => 'PlayerController@showListing']);

Route::get('/estrategia', ['as' => 'strategy', 'uses' => 'TeamController@showStrategy']);

Route::get('/equipos', ['as' => 'teams', 'uses' => 'TeamController@showAll']);

Route::group(['prefix' => 'partido'], function() {
    Route::post('/jugar', ['as' => 'match.play', 'uses' => 'MatchController@play']);
    Route::get('/cargar', ['as' => 'match.load', 'uses' => 'MatchController@load']);
});