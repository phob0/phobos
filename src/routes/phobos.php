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

Route::middleware('auth:api')->namespace('Phobos\Framework\app\Http\Controllers')->group(function() {
    Route::get('me', 'AppController@me');

    Route::post('logout', 'Auth\LoginController@apiLogout');

    Route::post('upload', 'AppController@upload');

    Route::prefix('settings')->group(function() {
        Route::get('', 'SettingController@list');
        Route::get('{setting}', 'SettingController@item');
        Route::post('', 'SettingController@create');
        Route::put('{setting}', 'SettingController@update');
    });

    Route::prefix('users')->group(function() {
        Route::get('', 'UserController@list');
        Route::get('{user}', 'UserController@item');
        Route::post('', 'UserController@create');
        Route::put('{user}', 'UserController@update');
        Route::delete('{user}', 'UserController@destroy');
    });
});

Route::get('config', 'AppController@config');
Route::post('login', 'Auth\LoginController@apiLogin');
