<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {

    Route::get('callback', 'Auth\AuthController@handleCallback');

    Route::group(['middleware' => 'jwt'], function(){

        Route::post('logout', 'Auth\AuthController@logout');
        Route::post('me', 'Auth\AuthController@me');
        Route::get('refresh', 'Auth\AuthController@refresh');
    });

});

Route::group(['namespace' => 'Api'], function() {
    Route::group(['middleware' => 'jwt'], function(){

        Route::group(['prefix' => '/events',], function () {

            Route::get('', 'EventsController@index');
            Route::get('{id}', 'EventsController@show');
            Route::post('', 'EventsController@store');
            Route::put('{id}', 'EventsController@update');
            Route::delete('{id}', 'EventsController@delete');

        });

        Route::group(['prefix' => '/questions',], function () {

            Route::get('', 'QuestionsController@index');
            Route::get('events/{id}', 'QuestionsController@get_event_questions');
            Route::get('{id}', 'QuestionsController@show');
            Route::post('', 'QuestionsController@store');
            Route::put('{id}', 'QuestionsController@update');
            Route::delete('{id}', 'QuestionsController@delete');

        });

        Route::group(['prefix' => '/customers',], function () {

            Route::get('', 'CustomersController@index');
            Route::get('events/{id}', 'CustomersController@get_event_customers');
            Route::get('{id}', 'CustomersController@show');
            Route::post('', 'CustomersController@store');
            Route::put('{id}', 'CustomersController@update');
            Route::delete('{id}', 'CustomersController@delete');

        });


    });
});
