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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v0.1'], function () {

    Route::post('login', 'ApiController@login');

    Route::post('signup', 'ApiController@register');

    Route::post('forgot_password', 'ApiController@forgot_password');

    Route::post('change_password', 'ApiController@change_password');

    Route::post('update_profile', 'ApiController@update_profile');

    Route::post('get_profile', 'ApiController@get_user_profile');

    Route::post('update_profile_image', 'ApiController@update_profile_image');

    Route::post('get_cafe_list', 'ApiController@get_cafe_list');

    Route::post('get_cafe_detail', 'ApiController@get_cafe_detail');

    Route::post('scan_coffee_qr', 'ApiController@scan_qr');

    Route::post('get_coffee_card', 'ApiController@get_coffee_card');

    Route::post('get_coffee_awards', 'ApiController@coffee_awards');

    Route::post('user_subscription', 'ApiController@user_subscription');

    Route::post('facebook_login', 'ApiController@check_is_facebook_user');

    Route::post('utilize_promo', 'ApiController@utilize_promo');
});