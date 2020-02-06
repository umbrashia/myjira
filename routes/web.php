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
    Route::prefix('issues')->group(function () {
        Route::post('issue-import', 'Dashboard\Issues\IssuesController@issueImport');
        Route::post('update-issues-with-sub', 'Dashboard\Issues\IssuesManagementController@updateIssuesWithSub');
        Route::get('import-jira', 'Dashboard\Issues\IssuesController@importJira');
        Route::get('get-count-issues', 'Dashboard\Issues\IssuesController@getCountIssues');
        Route::get('issues-listing', 'Dashboard\Issues\IssuesManagementController@geIssuesListing');
        Route::get('view-issue/{id}', 'Dashboard\Issues\IssuesManagementController@viewIssue');
        Route::get('get-estimation', 'Dashboard\Issues\IssuesManagementController@getEstimation');
    });
});





