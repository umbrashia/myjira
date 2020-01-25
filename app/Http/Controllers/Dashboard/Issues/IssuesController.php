<?php

namespace App\Http\Controllers\Dashboard\Issues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unirest;
use Illuminate\Support\Facades\DB;
use App\Models\IssuesModel;
use \App\Helpers\GlobalHelper;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Storage;

class IssuesController extends Controller {

    public function __construct() {
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
    }

    function autoCreateTable($table, $col, $value) {
        
    }

    public function importJira(Request $request) {
        $date = "2019-11-28T17:13:44.593+0530";
        $fff = null;
//        dd(($fff??Carbon::parse($date)->format('Y-m-d H:i:s')));
        $data = [];
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        $response = Unirest\Request::get(
                        env('JIRA_APP_DOMAIN') . 'project',
                        ['Accept' => 'application/json']
        );
        $data['projects'] = $response->body;
        return view('dashboard/issues/importJira')->with($data);
    }

    public function autoRecall(\Unirest\Response $response) {

        foreach ($response->body->issues as $value)
            if (@$value->fields->issuetype->name) {
                if ($value->fields->assignee) {
                    $row = \App\Models\AssigneeModel::where(['accountId' => $value->fields->assignee->accountId])->first();
                    if (@$row->auto_id)
                        $value->fields->{'assignee_id'} = $row->auto_id;
                    else
                        $value->fields->{'assignee_id'} = DB::table("assignee")->insertGetId(
                                collect($value->fields->assignee)->filter()->only(GlobalHelper::getAllColsFromTbl("assignee", ""))->all()
                        );
                }
                if ($value->fields->reporter) {
                    $row = \App\Models\AssigneeModel::where(['accountId' => $value->fields->reporter->accountId])->first();
                    if (@$row->auto_id)
                        $value->fields->{'reporter_id'} = $row->auto_id;
                    else
                        $value->fields->{'reporter_id'} = DB::table("assignee")->insertGetId(
                                collect($value->fields->reporter)->filter()->only(GlobalHelper::getAllColsFromTbl("assignee", ""))->all()
                        );
                }
                $issue_type = $value->{'issue_type'} = $value->fields->issuetype->name;
                if ($issue_type == "Sub-task" && !empty($value->fields->summary))
                    foreach (['testing', 'release management', 'project management', 'coding', 'code review', 'estimation','impact',] as $subTypes)
                        if (Str::contains(Str::limit($value->fields->summary, 21), [$subTypes]))
                            $value->{'subtask_type'} = Str::snake($subTypes);
                if (!empty($value->fields->customfield_10180))
                    $value->{'actual_end_date'} = @Carbon::parse($value->fields->customfield_10180)->format('Y-m-d H:i:s');
                if (!empty($value->fields->customfield_10179))
                    $value->{'actual_start_date'} = @Carbon::parse($value->fields->customfield_10179)->format('Y-m-d H:i:s');
                if (!empty($value->fields->created))
                    $value->fields->{'created'} = @Carbon::parse($value->fields->created)->format('Y-m-d H:i:s');
                if (!empty($value->fields->updated))
                    $value->fields->{'updated'} = @Carbon::parse($value->fields->updated)->format('Y-m-d H:i:s');
//                \Illuminate\Support\Facades\Log::info($value->{'created'});
//                dd(collect($value)->except(['fields'])->merge($value->fields)->filter());
                $issue_id = DB::table("mainTableItem")->insertGetId(
                        collect($value)->except(['fields'])->merge($value->fields)->filter()->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("mainTableItem", ""))->all()
                );
                if ($value->fields->project) {
                    $value->fields->project->{'issue_id'} = $issue_id;
                    DB::table("project")->insert(
                            collect($value->fields->project)->filter()->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("project", ""))->all()
                    );
                }
                if (!in_array(@$value->issue_type, ['Story']) && @$value->fields->issuelinks) {
//                \Illuminate\Support\Facades\Log::debug($value->fields->issuelinks);
                    collect($value->fields->issuelinks)->map(function($inner)use($issue_id, $issue_type) {
                        $insertData = (empty($inner->outwardIssue)) ? $inner->inwardIssue : $inner->outwardIssue;
                        $row = IssuesModel::where(['key' => $insertData->key])->first();
                        if (@$row->main_issue_id) {
                            $insertData->{'linked_id'} = $row->main_issue_id;
                            $insertData->{'issue_id'} = $issue_id;
                            $insertData->{'issue_type'} = $issue_type;
                            DB::table("issuelinks")->insert(collect($insertData)->merge($insertData->fields)->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("issuelinks", ""))->all());
                        }
                    });
                }
                if (@$value->fields->parent) {
                    $row = IssuesModel::where(['key' => $value->fields->parent->key])->first();
                    if (@$row->main_issue_id)
                        $value->fields->parent->{'linked_id'} = $row->main_issue_id;
                    $value->fields->parent->{'issue_id'} = $issue_id;
                    $value->fields->parent->{'issue_type'} = $issue_type;
                    DB::table("issuelinks")->insert(collect($value->fields->parent)->merge($value->fields->parent->fields)->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("issuelinks", ""))->all());
                }
            }
    }

    public function jiraSyncImporter(Request $request, $inputVar = []) {
        $data = [];
        $totalCount = 50;
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        $response = Unirest\Request::get(
                        env('JIRA_APP_DOMAIN') . 'search',
                        ['Accept' => 'application/json'],
                        [
                            'jql' => 'project = ' . $request->project . ' AND issuetype in (' . $inputVar['issueType'] . ') ORDER BY created ASC, updated DESC',
                            'maxResults' => $totalCount,
                            'startAt' => $request->count,
                        ]
        );
//                dd($response->body);
        $data['step'] = $request->step;
        $data['project'] = $request->project;
        $data['count'] = $request->count + $totalCount;
        $data['next'] = true;
        $data['title'] = $inputVar['progressText'];
        if (($data['count']) <= ($response->body->total + $totalCount))
            $this->autoRecall($response);
        else {
            $data['step'] = $request->step + 1;
            $data['count'] = 0;
            $data['next'] = true;
            $data['title'] = $inputVar['successText'];
        }
        $data['response'] = collect($response->body)->except("issues")->all();

        $data['html'] = view('dashboard/issues/recallCheck')->with($data)->toHtml();
//                echo ($data['html']);die;
        return response()->json($data);
    }

    public function issueImport(Request $request) {

        switch ($request->step) {
            case '0':
//                dd('dd');
                foreach (['temp_jira_data', 'mainTableItem', 'issuetype', 'project', 'assignee', 'issuelinks',] as $value) {
                    DB::table($value)->truncate();
                }
                $data['next'] = true;
                $data['count'] = 0;
                $data['step'] = 1;
                $data['project'] = $request->project;
                $data['response'] = null;
                $data['title'] = "Database Successfully Refined....";
                $data['html'] = view('dashboard/issues/recallCheck')->with($data)->toHtml();
                return response()->json($data);
                break;
            case '1':
                return $this->jiraSyncImporter($request, [
                            "progressText" => 'Epic\'s is reading....',
                            "successText" => "Epic\'s is successfully synced...",
                            "issueType" => 'Epic'
                ]);
                break;
            case '2':
//                return response()->json(['next' => false]);
                return $this->jiraSyncImporter($request, [
                            "progressText" => "Story is reading....",
                            "successText" => "Story is successfully synced...",
                            "issueType" => "Story"
                ]);
                break;
            case '3':
                return $this->jiraSyncImporter($request, [
                            "progressText" => "Task is reading....",
                            "successText" => "Task is successfully synced...",
                            "issueType" => "Task"
                ]);
                break;
            case '4':
                return $this->jiraSyncImporter($request, [
                            "progressText" => "Sub-task is reading....",
                            "successText" => "Sub-task is successfully synced...",
                            "issueType" => "Sub-task"
                ]);
                break;
            case '5':
                return $this->jiraSyncImporter($request, [
                            "progressText" => "Test Case is reading....",
                            "successText" => "Test Case is successfully synced...",
                            "issueType" => '"Test Case"'
                ]);
                break;
            case '6':
                return $this->jiraSyncImporter($request, [
                            "progressText" => "Bug Case is reading....",
                            "successText" => "Bug Case is successfully synced...",
                            "issueType" => 'Bug'
                ]);
                break;
            case '7':
                return response()->json(['next' => false]);
                break;
        }


//        echo json_encode($response->body->issues);
//        exit;


        echo dd($response->body);
//        return view()
    }

    public function getCountIssues(Request $request) {
        $steps = [
            '0' => 'machine',
            '1' => 'Bug, Epic, Story, Task, "Test Case", Sub-task',
            '2' => 'Story, Bug',
            '3'=>'Bug',
        ];
        if (in_array($request->step, ['0']))
            return response()->json(['total' => 1]);

        $response = Unirest\Request::get(
                        env('JIRA_APP_DOMAIN') . 'search',
                        ['Accept' => 'application/json'],
                        [
                            'jql' => 'project = ' . env("JIRA_PROJECT_KEY") . ' AND issuetype in (' . $steps[$request->step] . ') ORDER BY priority DESC, updated DESC',
                            'maxResults' => 20,
                            'startAt' => 0
                        ]
        );
//        $data['total'] = $response->body->total; //collect($response->body)->except("issues")->all();
        $data['response'] = $response->body;
        return response()->json($data);
    }

    public function jiraDataExporter(Request $request, $inputVar = []) {
        $data = [];
        $totalCount = 20;
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        $response = Unirest\Request::get(
                        env('JIRA_APP_DOMAIN') . 'search',
                        ['Accept' => 'application/json'],
                        [
                            'jql' => 'project = ' . $request->project . ' AND issuetype in (' . $inputVar['issueType'] . ') ORDER BY created ASC, updated DESC',
                            'maxResults' => $totalCount,
                            'startAt' => $request->count,
                        ]
        );
        $data['step'] = $request->step;
        $data['project'] = $request->project;
        $data['count'] = $request->count + $totalCount;
        $data['next'] = true;
        $data['title'] = $inputVar['progressText'];

        if (($data['count']) <= $response->body->total)
            collect($response->body->issues)->map(function($ar) {
                DB::table("temp_jira_data")->insert([
                    "issuetype" => $ar->fields->issuetype->name,
                    'created' => Carbon::parse($ar->fields->created)->format('Y-m-d H:i:s'),
                    'jira_data_json' => collect($ar)->toJson()
                ]);
            });
        else {
            $data['step'] = $request->step + 1;
            $data['count'] = 0;
            $data['next'] = false;
            $data['title'] = $inputVar['successText'];
        }
        $data['response'] = collect($response->body)->except("issues")->all();

        $data['html'] = view('dashboard/issues/recallCheck')->with($data)->toHtml();
//                echo ($data['html']);die;
        return response()->json($data);
    }

    //
}
