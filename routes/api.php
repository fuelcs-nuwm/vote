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

            Route::get('active', 'EventsController@get_active_event_questions');

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

        Route::group(['prefix' => '/groups',], function () {

            Route::get('', 'GroupController@index');
            Route::get('events/{id}', 'GroupController@get_event_group');
            Route::get('{id}', 'GroupController@show');
            Route::post('', 'GroupController@store');
            Route::put('{id}', 'GroupController@update');
            Route::delete('{id}', 'GroupController@delete');

        });

        Route::group(['prefix' => '/customers',], function () {

            Route::get('', 'CustomersController@index');
            Route::get('events/{id}', 'CustomersController@get_event_customers');
            Route::get('{id}', 'CustomersController@show');
            Route::post('', 'CustomersController@store');
            Route::put('{id}', 'CustomersController@update');
            Route::delete('{id}', 'CustomersController@delete');

        });

        Route::group(['prefix' => '/vote',], function () {

            Route::post('new-vote', 'VoteController@startNewVote');;
            Route::get('get-active-vote', 'VoteController@getActiveVote');

            Route::group(['prefix' => '/answers',], function () {
                Route::get('{vote_id}', 'VoteAnswersController@get_event_users');
                Route::post('', 'VoteAnswersController@store');
            });

        });

        Route::group(['prefix' => '/messages',], function () {

            Route::get('event-messages', 'EventChatMessagesController@get_event_messages');

            Route::get('', 'EventChatMessagesController@index');
            Route::get('{id}', 'EventChatMessagesController@show');
            Route::post('', 'EventChatMessagesController@store');
            Route::put('{id}', 'EventChatMessagesController@update');
            Route::delete('{id}', 'EventChatMessagesController@delete');

        });

        Route::group(['prefix' => '/registered',], function () {
            Route::get('event-users', 'RegisteredUserController@get_event_users');
        });

        Route::group(['prefix' => '/admins',], function () {

            Route::get('', 'AdminsController@index');
            Route::post('', 'AdminsController@store');
            Route::put('{id}', 'AdminsController@update');
            Route::delete('{id}', 'AdminsController@delete');

        });
    });
});
