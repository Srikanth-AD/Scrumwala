<?php

Route::get('/', function () {
	return redirect('auth/login');
});

/* Projects */
Route::get('projects/{projects}/plan', 'ProjectsController@plan');
Route::resource('projects', 'ProjectsController');

/* Issues */
Route::get('issues/search', 'IssuesController@search');
Route::post('issues/statuschange', 'IssuesController@statuschange');
Route::post('issues/sprintchange', 'IssuesController@sprintchange');
Route::post('issues/quickAdd', 'IssuesController@quickAdd');
Route::post('issues/sortorder', 'IssuesController@sortorder');
Route::resource('issues', 'IssuesController');
Route::resource('issuestatuses', 'IssueStatusesController');

/* Sprints */
Route::post('sprints/add', 'SprintsController@add');
Route::patch('sprints/activate', 'SprintsController@activate');
Route::post('sprints/complete', 'SprintsController@complete');
Route::resource('sprints', 'SprintsController');

/* Auth */
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);