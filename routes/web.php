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
Route::get('/contrasena/recuperar', ['as' => 'password.request', 'uses' => 'Auth\ResetPasswordController@showLinkRequestForm']);
Route::get('/contrasena/recuperar/{token}', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);

Route::get('/vestuario', ['as' => 'home', 'uses' => 'HomeController@index']);

Route::get('/perfil/editar', ['as' => 'profile.edit', 'uses' => 'AccountSettingsController@index']);
Route::patch('/perfil', ['as' => 'profile', 'uses' => 'AccountSettingsController@update']);
Route::get('/perfil/contrasena', ['as' => 'profile.password', 'uses' => 'AccountSettingsController@editPassword']);
Route::patch('/perfil/contrasena', 'AccountSettingsController@updatePassword');

Route::get('/equipo', 'TeamController@index');
Route::post('/equipo', ['as' => 'team.store', 'uses' => 'TeamController@store']);
Route::patch('/equipo', ['as' => 'team', 'uses' => 'TeamController@update']);
Route::get('/equipo/crear', 'TeamController@create');
Route::get('/equipo/editar', ['as' => 'team.edit', 'uses' => 'TeamController@edit']);
Route::post('/equipo/estrategia', ['as' => 'team.strategy', 'uses' => 'TeamController@updateStrategy']);
Route::post('/equipo/formacion', ['as' => 'team.formation', 'uses' => 'TeamController@updateFormation']);

Route::get('/jugadores', ['as' => 'players', 'uses' => 'PlayerController@showListing']);

Route::get('/estrategia', ['as' => 'strategy', 'uses' => 'TeamController@showStrategy']);