<?php
use \App\Http\Middleware\checkuser;
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
Route::post('users/register','UserController@store');
Route::post('users/login','UserController@login');

Route::get('projects','UserController@show')->Middleware(checkuser::class);
Route::post('projects','ProjectController@store')->Middleware(checkuser::class);
Route::delete('projects/{id}','UserController@destroy')->Middleware(checkuser::class);
Route::put('projects/{id}','UserController@update')->Middleware(checkuser::class);
