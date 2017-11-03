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

Route::get('/', 'TbRefundController@data');
Route::get('/code/import', 'CodeController@import');
Route::get('/code/code_today', 'CodeController@code_today');
Route::get('/scode/list', 'SCodeController@list');
Route::get('/product/import', 'ProductController@import');