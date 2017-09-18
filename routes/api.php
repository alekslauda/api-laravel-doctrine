<?php


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

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1'], function(){

    Route::group(['prefix' => 'auth'], function($app){
        $app->post('login', 'AuthController@login');
        $app->post('register', 'AuthController@register');
        $app->post('facebook', 'AuthController@facebook');
//        $app->get('facebook/callback', 'AuthController@handleFacebookCallbackUrl');
        $app->get('refresh', 'AuthController@refresh')->middleware('jwt.refresh');
        $app->post('logout', 'AuthController@logout')->middleware('jwt.auth');
    });

    Route::group([
        'middleware' => ['jwt.auth']
    ], function ($app) {
        $app->get('/users', 'UserController@all');
        $app->get('/user', 'UserController@single');
    });


});

