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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/tool/check-url-status', 'ToolsController@checkUrlStatus');
Route::get('/tool/pagespeed', 'ToolsController@checkGooglePageSpeed');
