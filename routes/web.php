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

Route::delete('problems/{id}','ProblemsController@destroy');
Route::post('problems','ProblemsController@store');
Route::get('problems/{id}','ProblemsController@show');
Route::put('problems','ProblemsController@update');
Route::delete('files/acta/{id}','FileController@destroyActa');
Route::post('files','FileController@store');
Route::get('files/acta/{id}/{has}','FileController@showacta');
Route::delete('users/token/{id}','TokenUserController@destroy');
Route::post('users/token','TokenUserController@store');
Route::put('users/token','TokenUserController@update');
Route::get('enviar/','firebaseController@enviar');
Route::delete('meetings/user/{idu}/{idm}','meetingsController@delete_user');
Route::post('meetings/user','meetingsController@add_users');
Route::get('meetings/user/{id}','meetingsController@showUsers');
Route::get('meetings/{id?}','meetingsController@show');
Route::get('meetings/project/{id}','meetingsController@meetings_pro');
Route::post('meetings','meetingsController@store');
Route::put('meetings','meetingsController@update');
Route::delete('meetings/{id}','meetingsController@destroy');
Route::get('sprints/{id?}','SprintController@show');
Route::post('sprints','SprintController@store');
Route::put('sprints','SprintController@update');
Route::delete('sprints/{id}','SprintController@destroy');
Route::get('files/{id}','FileController@show');
Route::delete('files/{id}','FileController@destroy');
Route::put('comments','CommentsController@update');
Route::post('comments','CommentsController@store');
Route::post('comments/addfile','CommentsController@addfile');
Route::delete('comments/{id}','CommentsController@destroy');
Route::put('task','TaskController@update');
Route::delete('task/{id?}','TaskController@destroy');
Route::get('task/{id?}','TaskController@show');
Route::post('task','TaskController@store');
Route::post('task/user','TaskController@addUser');
Route::get('task/user/{id?}','TaskController@showUser');
Route::delete('task/user/{id}/{iduser}','TaskController@destroyUser');
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
Route::get('projects/problems/{id}','ProjectController@projectProblem');
Route::get('projects/{id}','ProjectController@show');
Route::get('projects/sprint/{id}','ProjectController@showsprint');
Route::get('projects','UserController@show')->Middleware(checkuser::class);
Route::get('project/task/{id}','TaskController@show_project_task');
Route::post('projects','ProjectController@store')->Middleware(checkuser::class);
Route::delete('projects/{id}','UserController@destroy')->Middleware(checkuser::class);
Route::put('projects/{id}','UserController@update')->Middleware(checkuser::class);
