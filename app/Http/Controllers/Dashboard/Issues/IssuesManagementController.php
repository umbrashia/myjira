<?php

namespace App\Http\Controllers\Dashboard\Issues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IssuesManagementController extends Controller
{
    public function geIssuesListing(Request $request) {
        $data=[];
        return view('dashboard/issues/issueListing')->with($data);
    }
    //
}
