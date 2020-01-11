<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unirest;

class IssuesController extends Controller {
    
    function autoCreateTable($table,$col,$value) {
        
    }

    public function issueListing(Request $request) {
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
//        $response = Unirest\Request::get(
//                        env('JIRA_APP_DOMAIN').'project',
//                        ['Accept' => 'application/json']
//        );
        $response = Unirest\Request::get(
                        env('JIRA_APP_DOMAIN') . 'search',
                        ['Accept' => 'application/json'],
                        [
                            'jql' => 'project = "BIL" AND resolution = Unresolved ORDER BY priority DESC',
                            'maxResults' => '5'
                        ]
        );
        
        echo json_encode($response->body);
//        return view()
    }

    //
}
