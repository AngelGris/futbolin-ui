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

Route::group(['domain' => 'admin.{domain}',  'middleware' => ['auth', 'admin']], function() {
    Route::get('/', ['as' => 'admin', 'uses' => 'Admin\HomeController@showIndex']);

    Route::get('/contrasena', ['as' => 'admin.password', 'uses' => 'Admin\HomeController@editPassword']);
    Route::patch('/contrasena', 'Admin\HomeController@updatePassword');

    Route::get('/equipos', ['as' => 'admin.teams', 'uses' => 'Admin\TeamController@index']);
    Route::get('/equipo/{team}', ['as' => 'admin.team', 'uses' => 'Admin\TeamController@show'])->where('team', '[0-9]+');

    Route::get('/partidos', ['as' => 'admin.matches', 'uses' => 'Admin\MatchController@index']);
    Route::get('/partido', ['as' => 'admin.match', 'uses' => 'Admin\MatchController@show']);
    Route::get('/partido/log/{match}', ['as' => 'admin.match.log', 'uses' => 'Admin\MatchController@showLog'])->where('match', '[0-9]+');

    Route::get('/usuarios', ['as' => 'admin.users', 'uses' => 'Admin\UserController@index']);
    Route::get('/usuario/{user}', ['as' => 'admin.user', 'uses' => 'Admin\UserController@show'])->where('user', '[0-9]+');

    Route::get('/torneos', ['as' => 'admin.tournaments', 'uses' => 'Admin\TournamentController@index']);
    Route::group(['prefix' => 'torneo'], function() {
        Route::get('/{tournament}', ['as' => 'admin.tournament', 'uses' => 'Admin\TournamentController@show'])->where('tournament', '[0-9]+');
        Route::get('/crear', ['as' => 'admin.tournament.create', 'uses' => 'Admin\TournamentController@create']);
        Route::post('/', ['as' => 'admin.tournament.store', 'uses' => 'Admin\TournamentController@store']);
    });

    Route::get('/categoria/{category}', 'Admin\TournamentCategoryController@index')->where('category', '[0-9]+');

    Route::get('/mensajes', ['as' => 'admin.messages', 'uses' => 'Admin\MessageController@index']);
    Route::group(['prefix' => 'mensaje'], function() {
        Route::get('/crear', ['as' => 'admin.message.create', 'uses' => 'Admin\MessageController@create']);
        Route::post('/', ['as' => 'admin.message.store', 'uses' => 'Admin\MessageController@store']);
        Route::get('/editar/{message}', ['as' => 'admin.message.edit', 'uses' => 'Admin\MessageController@edit'])->where('message', '[0-9]+');
        Route::get('/{message}', ['as' => 'admin.message', 'uses' => 'Admin\MessageController@show'])->where('message', '[0-9]+');
        Route::patch('/{message}', ['as' => 'admin.message.save', 'uses' => 'Admin\MessageController@save'])->where('message', '[0-9]+');
        Route::delete('/{message}', ['as' => 'admin.message.delete', 'uses' => 'Admin\MessageController@delete'])->where('message', '[0-9]+');
    });
});

Auth::routes();

Route::get('/', 'Auth\LoginController@showLoginForm');
Route::group(['prefix' => 'contrasena'], function() {
    Route::get('recuperar', ['as' => 'password.request', 'uses' => 'Auth\ResetPasswordController@showLinkRequestForm']);
    Route::post('recuperar', 'Auth\ResetPasswordController@reset');
    Route::get('recuperar/{token}', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
});

Route::group(['middleware' => 'auth'], function() {
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
        Route::get('/{team}', ['as' => 'team.show', 'uses' => 'TeamController@show'])->where('team', '[0-9]+');
        Route::get('/crear', 'TeamController@create');
        Route::get('/editar', ['as' => 'team.edit', 'uses' => 'TeamController@edit']);
        Route::get('/estadisticas/{rival}', ['as' => 'team.statsVersus', 'uses' => 'TeamController@showStatsVersus'])->where('rival', '[0-9]+');
        Route::post('/estrategia', ['as' => 'team.strategy', 'uses' => 'TeamController@updateStrategy']);
        Route::post('/formacion', ['as' => 'team.formation', 'uses' => 'TeamController@updateFormation']);
    });

    Route::get('/jugadores', ['as' => 'players', 'uses' => 'PlayerController@showListing']);

    Route::get('/jugador/{player}', ['as' => 'player', 'uses' => 'PlayerController@index'])->where('player', '[0-9]+');

    Route::get('/estrategia', ['as' => 'strategy', 'uses' => 'TeamController@showStrategy']);

    Route::get('/amistosos', ['as' => 'teams', 'uses' => 'TeamController@showAll']);

    Route::get('/equipos', function(){
        return Redirect::to('/amistosos', 301);
    });

    Route::group(['prefix' => 'partido'], function() {
        Route::post('/jugar', ['as' => 'match.play', 'uses' => 'MatchController@play']);
        Route::get('/cargar', ['as' => 'match.load', 'uses' => 'MatchController@load']);
    });

    Route::get('/torneos', ['as' => 'tournaments', 'uses' => 'TournamentController@index']);

    Route::get('/mensaje-admin/{message}', 'Admin\MessageController@showPublic')->where('message', '[0-9]+');
});