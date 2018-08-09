<?php

use Illuminate\Http\Request;

Route::group(['prefix'=>'oauth'], function(){
    Route::post('register', 'Api\AuthController@register');
    Route::post('login', 'Api\AuthController@login');

    Route::group(['middleware'=>'auth:api'], function(){
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\AuthController@user');
    });
});