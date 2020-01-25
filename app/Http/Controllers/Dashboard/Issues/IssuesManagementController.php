<?php

namespace App\Http\Controllers\Dashboard\Issues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unirest;
use Illuminate\Support\Facades\DB;
use App\Models\IssuesModel;
use \App\Helpers\GlobalHelper;
use Carbon\Carbon;
use Storage;

class IssuesManagementController extends Controller {

    public function __construct() {
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
    }

    public function geIssuesListing(Request $request) {
        $data = [];
        $data['issues']=IssuesModel::where(["issue_type"=>'Story'])->get();
//        dd($data['issues'][0]->created);
        
        return view('dashboard/issues/issueListing')->with($data);
    }
    
    public function viewIssue(Request $request,$id) {
        $data = [];
        $data['issue']=IssuesModel::findOrFail($id);
//        dd($data['issues'][0]->created);
        
        return view('dashboard/issues/viewIssue')->with($data);
    }
    

    //
}
