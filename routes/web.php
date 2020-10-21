<?php

use Illuminate\Support\Facades\Route;

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


Route::post('api/users','UserController@register');
Route::post('api/users/login','UserController@login');
Route::put('api/users/{id}','UserController@update');
Route::put('api/users/updatepassword/{id}','UserController@updatePassword');
Route::get('api/users','UserController@listar');
Route::delete('api/users\/{id}','UserController@delete');
Route::get('api/users/{id}','UserController@getById');
