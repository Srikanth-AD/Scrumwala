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
Route::get('/', function() {
   return redirect('auth/login');
});
Route::get('projects/{projects}/plan', 'ProjectsController@plan');
Route::resource('projects', 'ProjectsController');

Route::get('issues/search', 'IssuesController@search');
Route::post('issues/statuschange', 'IssuesController@statuschange');
Route::post('issues/sprintchange', 'IssuesController@sprintchange');
Route::post('issues/quickAdd', 'IssuesController@quickAdd');
Route::resource('issues', 'IssuesController');
Route::resource('issuestatuses', 'IssueStatusesController');

Route::post('sprints/add', 'SprintsController@add');
Route::patch('sprints/activate', 'SprintsController@activate');
Route::post('sprints/complete', 'SprintsController@complete');
Route::resource('sprints', 'SprintsController');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);