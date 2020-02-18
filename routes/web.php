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
Route::post('Create/user','UserController@store');
Route::post('Create/project','ProjectController@store')->Middleware(checkuser::class);
Route::post('login','UserController@login');
 
Route::post('view/user/projects','UserController@show')->Middleware(checkuser::class);
