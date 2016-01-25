<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'IndexController@index');
//提交预发
Route::post('release', 'IndexController@postRelease');
//完成预发
Route::get('complete', 'IndexController@completeRelease');
//删除预发
Route::get('delete', 'IndexController@deleteRelease');
//用户登录页面
Route::any('user/login', 'UserController@login');

Route::get('package', 'PackageController@index');

Route::post('zip', 'PackageController@zip');