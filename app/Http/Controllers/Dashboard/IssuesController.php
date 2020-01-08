<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IssuesController extends Controller
{
    
    public function issueListing(Request $request) {
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');

        $headers = array(
            'Accept' => 'application/json'
        );

        $response = Unirest\Request::get(
                        'https://kreatetechnologies.atlassian.net/rest/api/3/project',
                        $headers
        );

//        var_dump($response);
        dd($response);
    }
    //
}
