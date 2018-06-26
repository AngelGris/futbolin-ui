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

Route::group(['middleware' => ['auth.api', 'liveMatch']], function() {
    Route::get('/credits/items', 'CreditController@index');

    Route::post('/logout', 'Auth\LoginController@apiLogout');

    Route::group(['prefix' => 'market'], function() {
        Route::get('/', 'MarketController@listing');
        Route::get('/transactions', 'MarketController@transactions');
    });

    Route::group(['prefix' => 'match'], function() {
        Route::post('/', 'MatchController@play');
        Route::get('/', 'MatchController@load');
    });

    Route::group(['prefix' => 'me'], function() {
        Route::get('/', 'UserController@index');
        Route::patch('/', 'AccountSettingsController@update');
        Route::get('/notifications', 'NotificationController@index');
        Route::get('/notification/{notification}', 'NotificationController@show')->where('notification', '[0-9]+');
    });

    Route::patch('/password', 'AccountSettingsController@updatePassword');

    Route::group(['prefix' => 'player'], function() {
        Route::post('/offer', 'PlayerController@offer');
        Route::get('/{player}', 'PlayerController@index')->where('player', '[0-9]+');
        Route::post('/{player}/free', 'PlayerController@free')->where('player', '[0-9]+');
        Route::post('/{player}/transferable', 'PlayerController@startSelling')->where('player', '[0-9]+');
        Route::patch('/{player}/value', 'PlayerController@updateValue')->where('player', '[0-9]+');
    });
    Route::get('/players', 'PlayerController@showListing');

    Route::group(['prefix' => 'shopping'], function() {
        Route::post('/buy', 'ShoppingController@buy');
        Route::get('/items', 'ShoppingController@index');
    });

    Route::get('/strategies', 'StrategyController@index');

    Route::group(['prefix' => 'team'], function() {
        Route::post('/', 'TeamController@store');
        Route::patch('/', 'TeamController@update');
        Route::get('/{team}', 'TeamController@show')->where('team', '[0-9]+');
        Route::patch('/formation', 'TeamController@updateFormation');
        Route::patch('/strategy', 'TeamController@updateStrategy');
        Route::post('/train', 'TeamController@train');
    });
    Route::get('/teams', 'TeamController@showAll');

    Route::get('/tournament/{tournament?}', 'TournamentController@index')->where('tournament', '[0-9]+');

    Route::get('/user/{user}', 'UserController@show')->where('user', '[0-9]+');
});

Route::post('/register', 'Auth\RegisterController@apiRegister');
Route::post('/login', 'Auth\LoginController@apiLogin');
Route::get('/match/live', 'MatchController@showLiveApi');