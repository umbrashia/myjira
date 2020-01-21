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
    return view('dashboard.issues.issueListing');
});

Route::prefix('dashboard')->group(function () {
    Route::post('issue-listing', 'Dashboard\Issues\IssuesController@issueListing');
    Route::get('import-jira', 'Dashboard\Issues\IssuesController@importJira');
    Route::get('get-count-issues', 'Dashboard\Issues\IssuesController@getCountIssues');
});





