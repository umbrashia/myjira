<?php

namespace App\Http\Controllers\Dashboard\Issues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unirest;
use Illuminate\Support\Facades\DB;
use App\Models\IssuesModel;
use \App\Helpers\GlobalHelper;
use \Illuminate\Support\Carbon;
use Storage;

class IssuesManagementController extends Controller {

    public function __construct() {
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        Carbon::macro('isDayOff', function (Carbon $date) {
            return $date->isSunday() || $date->isSaturday();
        });
        Carbon::macro('isNotDayOff', function ($date) {
            return !$date->isDayOff();
        });
    }

    public function geIssuesListing(Request $request) {
        $data = [];
        $data['issues'] = IssuesModel::where(["issue_type" => 'Story'])->get();
//        dd($data['issues'][0]->created);

        return view('dashboard/issues/issueListing')->with($data);
    }

    public function viewIssue(Request $request, $id) {
        $data = [];
        $data['issue'] = IssuesModel::findOrFail($id);

        $data['subIssues'] = $data['issue']->getLinkedIssue()->selectRaw('assignee.name as assignee_name,'
                        . 'mainTableItem.*')
                ->rightJoin('mainTableItem', 'issuelinks.issue_id', '=', 'mainTableItem.main_issue_id')
                ->leftJoin('assignee', 'mainTableItem.assignee_id', '=', 'assignee.auto_id')
                ->whereIn("mainTableItem.subtask_type", array_map("Str::snake", ['testing', 'release management', 'project management', 'coding', 'code review', 'estimation', 'impact',]))
                ->orderBy('mainTableItem.subtask_type_order','ASC')
                ->get();
//        throw "error";
//        dd($joinData);

        return view('dashboard/issues/viewIssue')->with($data);
    }

    public function getEstimation(Request $request) {
        $estimation = [];
        $estimation['coding']['hours'] = $request->codingHours;

        $estimation['story']['startDate'] = $request->startDate;

        // S T O R Y   E S T I M A T I O N 
        $estimation['story']['hours'] = $estimation['coding']['hours'] * 1.66;
        $newDate = Carbon::parse($estimation['story']['startDate']);
        $estimation['story']['dueDate'] = $newDate->addWeekdays(ceil($estimation['story']['hours'] / 8))->format('Y-m-d');
        $estimation['story']['hours_round'] = ceil($estimation['story']['hours']);
        // I M P A C T   E S T I M A T I O N 
        $newDate = Carbon::parse($estimation['story']['startDate']);
        $estimation['estimation']['percent'] = $estimation['project_management']['percent'] = $estimation['impact']['percent'] = 10;
        $estimation['estimation']['hours'] = $estimation['project_management']['hours'] = $estimation['impact']['hours'] = (($estimation['impact']['percent'] / 100) * $estimation['story']['hours']);
        $estimation['story']['startDate'] = $estimation['estimation']['startDate'] = $estimation['project_management']['startDate'] = $estimation['impact']['startDate'] = $newDate->addWeekdays(0.1)->format('Y-m-d');
        $estimation['estimation']['dueDate'] = $estimation['project_management']['dueDate'] = $estimation['impact']['dueDate'] = Carbon::parse($estimation['impact']['startDate'])->addWeekdays(ceil($estimation['impact']['hours'] / 8))->format('Y-m-d');
        $estimation['estimation']['hours_round'] = $estimation['project_management']['hours_round'] = $estimation['impact']['hours_round'] = ceil($estimation['impact']['hours']);
        // C O D I N G   E S T I M A T I O N 
        $newDate = Carbon::parse($estimation['impact']['dueDate']);
        $estimation['coding']['startDate'] = $estimation['impact']['dueDate'];
        $estimation['coding']['dueDate'] = $newDate->addWeekdays(ceil($estimation['coding']['hours'] / 8))->format('Y-m-d');
        $estimation['coding']['hours_round'] = ceil($estimation['coding']['hours']);
        // C O D E   R E V I E W
        $newDate = Carbon::parse($estimation['coding']['dueDate']);
        $estimation['release_management']['percent'] = $estimation['code_review']['percent'] = 15;
        $estimation['release_management']['startDate'] = $estimation['code_review']['startDate'] = $estimation['coding']['dueDate'];
        $estimation['code_review']['hours'] = (($estimation['code_review']['percent'] / 100) * $estimation['story']['hours']);
        $estimation['release_management']['dueDate'] = $estimation['code_review']['dueDate'] = $newDate->addWeekdays(ceil($estimation['code_review']['hours'] / 8))->format('Y-m-d');
        $estimation['release_management']['hours_round'] = $estimation['code_review']['hours_round'] = ceil($estimation['code_review']['hours']);
        // Q A   T E S T I N G 
        $estimation['testing']['percent'] = 20;
        $estimation['testing']['startDate'] = $estimation['code_review']['dueDate'];
        $estimation['testing']['hours'] = (($estimation['testing']['percent'] / 100) * $estimation['story']['hours']);
        $estimation['testing']['hours_round'] = ceil($estimation['testing']['hours']);
        $estimation['story']['dueDate'] = $estimation['testing']['dueDate'] = $newDate->addWeekdays(ceil($estimation['testing']['hours'] / 8))->format('Y-m-d');
        return response()->json($estimation);
    }

    private function getDateWihoutWeekend(Carbon $carbonDate, $holidays = []): Carbon {
        $count = 5;
        while (($count > 0)) {
            if ($carbonDate->isDayOff() || in_array($carbonDate->format("Y-m-d"), $holidays))
                $someDate->addDay();
            else {
                $someDate->addDay();
                $count--;
            }
        }
    }

    public function updateIssuesWithSub(Request $request) {
        $post = $request->all();
//        dump($post);
        $issueData = [];
        foreach ($post['key'] as $key => $value) {
            $temp ['fields'] = [
//                'key' => $value,
                "duedate" => $post['duedate'][$key],
//                "main_issue_id" => $post['main_issue_id'][$key],
                "timetracking" => ["originalEstimate" => $post['timeestimate'][$key]],
                "customfield_10025" => $post['start_date'][$key],
                    // ""=>$post[''][$key],
            ];
//            dd(collect($temp)->toJson());
            $response = Unirest\Request::put(
                            env('JIRA_APP_DOMAIN') . 'issue/' . $value,
                            [
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json'
                            ],
                            collect($temp)->toJson()
            );

            $issueData[] = $temp;
        }
        return redirect()->back()->with("successMessage", "Estimation is successfully saved...");
        dd($issueData);


        /*
          {"fields":{"timetracking":{"originalEstimate":"24"},"duedate":"2020-01-24","customfield_10025":"2020-01-13"}}
         */
    }

    //
}
