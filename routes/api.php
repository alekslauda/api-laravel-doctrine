<?php
use Illuminate\Support\Facades\Config;

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
        $app->post('login', 'AuthController@login')->middleware('api.validate.boilerplate:user_login');
        $app->post('register', 'AuthController@register')->middleware('api.validate.boilerplate:user_register');
        $app->get('confirm/{token?}', 'AuthController@confirm')->name('confirm.email');
        $app->post('facebook', 'AuthController@facebook');
//        $app->get('facebook/callback', 'AuthController@handleFacebookCallbackUrl');
        $app->get('refresh', 'AuthController@refresh')->middleware('jwt.refresh');
        $app->post('logout', 'AuthController@logout')->middleware('jwt.auth');

        $app->post('recovery', 'AuthController@recovery')->name('password.email')->middleware('api.validate.boilerplate:user_forgot_password');
        //$app->get('password/reset/{token}', 'AuthController@showResetForm')->name('password.reset');
        $app->post('reset', 'AuthController@reset')->name('password.reset')->middleware('api.validate.boilerplate:user_reset_password');
    });

    Route::group([
        'middleware' => ['jwt.auth']
    ], function ($app) {
        $app->get('/users', 'UserController@all');
        $app->get('/user', 'UserController@single');
    });


});

