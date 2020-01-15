<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unirest;
use Illuminate\Support\Facades\DB;
use App\Models\IssuesModel;
use \App\Helpers\GlobalHelper;

class IssuesController extends Controller {

    function autoCreateTable($table, $col, $value) {
        
    }

    public function importJira(Request $request) {
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
                $value->{'issue_type'} = $value->fields->issuetype->name;
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
                    collect($value->fields->issuelinks)->map(function($inner)use($issue_id) {
                        $insertData = (empty($inner->outwardIssue)) ? $inner->inwardIssue : $inner->outwardIssue;
                        $row = IssuesModel::where(['key' => $insertData->key])->first();
                        if (@$row->main_issue_id) {
                            $insertData->{'issue_id'} = $row->main_issue_id;
                            DB::table("issuelinks")->insert(collect($insertData)->merge($insertData->fields)->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("issuelinks", ""))->all());
                        }
                    });
                }
                if (@$value->fields->parent) {
                    $row = IssuesModel::where(['key' => $value->fields->parent->key])->first();
                    if (@$row->main_issue_id)
                        $value->fields->parent->{'issue_id'} = $row->main_issue_id;
                    DB::table("issuelinks")->insert(collect($value->fields->parent)->merge($value->fields->parent->fields)->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("issuelinks", ""))->all());
                }
            }
    }

    public function issueListing(Request $request, $step) {
        $data = [];
        $totalCount = 20;
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        switch ($step) {
            case '0':
                foreach (['mainTableItem', 'issuetype', 'project', 'assignee', 'issuelinks',] as $value) {
                    DB::table($value)->truncate();
                }
                $data['next'] = true;
                $data['count'] = 1;

                $data['response'] = [];
                $data['html'] = "";
                return response()->json($data);
                break;
            case '1':
                $response = Unirest\Request::get(
                    env('JIRA_APP_DOMAIN') . 'search',
                                ['Accept' => 'application/json'],
                                [
                                    'jql' => 'project = ' . $request->project . ' AND issuetype = Story ORDER BY priority DESC, updated DESC',
                                    'maxResults' => $totalCount
                                ]
                );
                $data['step'] = $step;
                $data['count'] = $request->count + $totalCount;
                $data['next'] = false;
                if (($data['count'] + $totalCount) < $response->body->total)
                    $this->autoRecall($response);
                else {
                    $data['step'] = $step + 1;
                    $data['count'] = $totalCount;
                    $data['next'] = true;
                }
                $data['response'] = collect($response)->except("issues")->all();
                $data['html'] = view('dashboard/issues/importJira')->with($data);
                return response()->json($data);
                break;
            case '2':

                break;
            case '3':

                break;
        }


//        echo json_encode($response->body->issues);
//        exit;


        echo dd($response->body);
//        return view()
    }

    //
}
