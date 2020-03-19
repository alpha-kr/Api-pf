<?php
use \App\Http\Middleware\checkuser;
use \App\Http\Middleware\checkuserpro;
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
Route::get('files/{id}','FileController@show');
Route::delete('files/{id}','FileController@destroy');
Route::put('comments','CommentsController@update');
Route::post('comments','CommentsController@store');
Route::post('comments','CommentsController@addfile');
Route::delete('comments/{id}','CommentsController@destroy');
Route::put('task','TaskController@update');
Route::delete('task/{id?}','TaskController@destroy');
Route::get('task/{id?}','TaskController@show');
Route::post('task','TaskController@store');
Route::post('users/register','UserController@store');
Route::post('users/login','UserController@login');
Route::get('status','statusController@All');
Route::get('users/{word?}','UserController@All');
Route::get('Roles','RoleController@show');
Route::post('userstory','UserstoriesController@store')->Middleware(checkuserpro::class);
Route::get('userstory/{id}','UserstoriesController@show')->Middleware(checkuserpro::class);
Route::delete('userstory/{id}','UserstoriesController@destroy')->Middleware(checkuserpro::class);
Route::put('userstory','UserstoriesController@update')->Middleware(checkuserpro::class);
Route::post('userproject','ProjectController@addUser');
Route::delete('userproject/{id}/{proj}','ProjectController@Deleteuser');
Route::get('userproject/{id}','ProjectController@users');
Route::put('userproject','ProjectController@updateuser');
Route::get('projects/{id}','ProjectController@show');
Route::get('projects','UserController@show')->Middleware(checkuser::class);
Route::get('project/task/{id}','TaskController@show_project_task');
Route::post('projects','ProjectController@store')->Middleware(checkuser::class);
Route::delete('projects/{id}','UserController@destroy')->Middleware(checkuser::class);
Route::put('projects/{id}','UserController@update')->Middleware(checkuser::class);
