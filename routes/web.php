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

Route::get('/perfil/editar', ['as' => 'profile.edit', 'uses' => 'AccountSettingsController@index']);
Route::patch('/perfil/editar', 'AccountSettingsController@update');
Route::get('/perfil/contrasena', ['as' => 'profile.password', 'uses' => 'AccountSettingsController@editPassword']);
Route::patch('/perfil/contrasena', 'AccountSettingsController@updatePassword');

Route::get('/vestuario', ['as' => 'home', 'uses' => 'HomeController@index']);

//Route::get('/equipo', 'TeamController@index');
Route::get('/equipo/crear', 'TeamController@create');
Route::post('/equipo', ['as' => 'team.store', 'uses' => 'TeamController@store']);