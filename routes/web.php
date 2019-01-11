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

Route::match(array('GET','POST'),'login', 'AuthController@login');

Route::match(array('GET','POST'),'signup', 'AuthController@register');

Route::match(array('GET','POST'),'forgot_password', 'AuthController@forgot_password');

Route::get('/users/active/{id}', 'AuthController@activate');

Route::get('/logout', 'AuthController@logout');


Route::group(['middleware' => 'Coffee_Auth'], function () {

    Route::get('/', 'HomeController@index');

    Route::get('/cafe/qrs', 'HomeController@qr_page');

    Route::post('/cafe/generate_qr', 'HomeController@generate_qr');

    Route::match(array('GET','POST'),'cafe/profile', 'HomeController@profile');

    Route::get('/cafe/users', 'HomeController@scanned_users');

    Route::get('/cafe/generate_pdf', 'HomeController@generate_pdf');

    Route::get('/cafe/users/ajax_get_users', 'HomeController@ajax_search');

    Route::post('/cafe/change_password', 'AuthController@change_password');

});

// Admin Routes

Route::match(array('GET','POST'),'admin', 'AuthController@admin_login');

Route::group(['middleware' => 'Coffee_Admin'], function () {

    Route::get('/admin/dashboard', 'AdminController@index');

    Route::get('/admin/cafe/{id}', 'AdminController@cafe_list');

    Route::post('/admin/get_cafe_detail', 'AdminController@ajax_get_cafe_detail');

    Route::post('/admin/update_user_status', 'AdminController@ajax_change_users_status');

    Route::get('/admin/customers/{id}', 'AdminController@customers_list');

    Route::post('/admin/send_mail_to_user', 'AdminController@send_email');

    Route::match(array('GET','POST'),'/admin/promo_codes', 'AdminController@promo_codes');

    Route::post('/admin/change_status/promo_codes', 'AdminController@change_promo_state');


});

Route::get('/dsf', function () {
    return view('welcome');
});
