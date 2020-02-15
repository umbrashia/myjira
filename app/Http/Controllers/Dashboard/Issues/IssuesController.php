<?php
namespace App\Http\Controllers\Dashboard\Issues;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Unirest;
use Illuminate\Support\Facades\DB;
use App\Models\IssuesModel;
use App\Helpers\GlobalHelper;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class IssuesController extends Controller
{

    public function __construct()
    {
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        Unirest\Request::jsonOpts(true);
    }

    function autoCreateTable($table, $col, $value)
    {}

    public function importJira(Request $request)
    {
        /*
         * $user = User::firstOrCreate(['email' => 'povilas@laraveldaily.com'],
         * ['first_name' => 'Povilas', 'last_name' => 'Korop']);
         */
        $date = "2019-11-28T17:13:44.593+0530";
        $fff = null;
        // dd(($fff??Carbon::parse($date)->format('Y-m-d H:i:s')));
        $data = [];
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        $response = Unirest\Request::get(env('JIRA_APP_DOMAIN') . 'project', [
            'Accept' => 'application/json'
        ]);
        $data['projects'] = $response->body;
        return view('dashboard/issues/importJira')->with($data);
    }

    public function autoRecall(\Unirest\Response $response)
    {
        foreach ($response->body['issues'] as $indexIssue => $value)
            if (@$value['fields']['issuetype']['name']) {

                if (Arr::has($value, "fields.assignee")) {
                    $row = \App\Models\AssigneeModel::where([
                        'accountId' => $value['fields']['assignee']['accountId']
                    ])->first();
                    if (@$row->auto_id)
                        $value['fields']['assignee_id'] = $row->auto_id;
                    else
                        $value['fields']['assignee_id'] = DB::table("assignee")->insertGetId(collect($value['fields']['assignee'])->filter()
                            ->only(GlobalHelper::getAllColsFromTbl("assignee", ""))
                            ->all());
                }
                if (Arr::has($value, "fields.reporter")) {
                    $row = \App\Models\AssigneeModel::where([
                        'accountId' => $value['fields']['reporter']['accountId']
                    ])->first();
                    if (@$row->auto_id)
                        $value['fields']['reporter_id'] = $row->auto_id;
                    else
                        $value['fields']['reporter_id'] = DB::table("assignee")->insertGetId(collect($value['fields']['reporter'])->filter()
                            ->only(GlobalHelper::getAllColsFromTbl("assignee", ""))
                            ->all());
                }
                $issue_type = $value['issue_type'] = $value['fields']['issuetype']['name'];
                $value['issue_type_id'] = \App\Models\IssueTypeModel::firstOrCreate([
                    "name" => $value['fields']['issuetype']['name']
                ], collect($value['fields']['issuetype'])->filter()
                    ->only(GlobalHelper::getAllColsFromTbl("issuetype", ""))
                    ->all())->auto_id;
                $value['issue_status'] = Arr::get($value, "fields.status.name");
                $value['bug_type'] = Arr::get($value, "fields.customfield_10177.value");
                if (Arr::has($value, "fields.customfield_10177.value"))
                    $value['bug_type_id'] = \App\Models\BugTypeModel::firstOrCreate([
                        'value' => Arr::get($value, "fields.customfield_10177.value")
                    ], collect($value['fields']['customfield_10177'])->filter()
                        ->only(GlobalHelper::getAllColsFromTbl("bugType", ""))
                        ->all())->auto_id;
                if ($issue_type == "Sub-task")
                    foreach ([
                        'project management',
                        'estimation',
                        'impact',
                        'coding',
                        'code review',
                        'release management',
                        'testing'
                    ] as $subTaskKey => $subTypes)
                        if (Str::contains(strtolower($value['fields']['summary']), $subTypes))
                            list ($value['subtask_type'], $value['subtask_type_order']) = [
                                Str::snake($subTypes),
                                ++ $subTaskKey
                            ];
                if (! empty($value['fields']['customfield_10180']))
                    $value['actual_end_date'] = @Carbon::parse($value['fields']['customfield_10180'])->format('Y-m-d H:i:s');
                if (! empty($value['fields']['customfield_10179']))
                    $value['actual_start_date'] = @Carbon::parse($value['fields']['customfield_10179'])->format('Y-m-d H:i:s');
                if (! empty($value['fields']['created']))
                    $value['fields']['created'] = @Carbon::parse($value['fields']['created'])->format('Y-m-d H:i:s');
                if (! empty($value['fields']['updated']))
                    $value['fields']['updated'] = @Carbon::parse($value['fields']['updated'])->format('Y-m-d H:i:s');
                if (! empty($value['fields']['customfield_10025']))
                    $value['fields']['start_date'] = @Carbon::parse($value['fields']['customfield_10025'])->format('Y-m-d H:i:s');
                if (! empty($value['fields']['duedate']))
                    $value['fields']['duedate'] = @Carbon::parse($value['fields']['duedate'])->format('Y-m-d H:i:s');
                if (! empty($value['fields']['customfield_10014']))
                    $value['fields']['story_point'] = $value['fields']['customfield_10014'];
                if (Arr::has($value, "fields.project"))
                    $value['project_id'] = \App\Models\ProjectModel::firstOrCreate([
                        "id" => $value['fields']['project']['id']
                    ], collect($value['fields']['project'])->filter()
                        ->only(GlobalHelper::getAllColsFromTbl("project", ""))
                        ->all())->auto_id;

                $issue_id = DB::table("mainTableItem")->insertGetId(collect($value)->except([
                    'fields'
                ])
                    ->merge($value['fields'])
                    ->filter()
                    ->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("mainTableItem", ""))
                    ->all());

                if (Arr::has($value, "fields.issuelinks")) {
                    // \Illuminate\Support\Facades\Log::debug($value['fields']['issuelinks']);
                    collect($value['fields']['issuelinks'])->map(function ($inner) use ($issue_id, $issue_type) {
                        $insertData = (empty($inner['outwardIssue'])) ? Arr::get($inner, "parent") : $inner['outwardIssue'];
                        $row = IssuesModel::where([
                            'key' => $insertData['key']
                        ])->first();
                        if (@$row->main_issue_id) {
                            $insertData['linked_id'] = $row->main_issue_id;
                            $insertData['issue_id'] = $issue_id;
                            $insertData['issue_type'] = $issue_type;
                            DB::table("issuelinks")->insert(collect($insertData)->merge($insertData['fields'])
                                ->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("issuelinks", ""))
                                ->all());
                        }
                    });
                }
                if (Arr::has($value, "fields.parent")) {
                    $row = IssuesModel::where([
                        'key' => $value['fields']['parent']['key']
                    ])->first();
                    if (@$row->main_issue_id)
                        $value['fields']['parent']['linked_id'] = $row->main_issue_id;
                    $value['fields']['parent']['issue_id'] = $issue_id;
                    $value['fields']['parent']['issue_type'] = $issue_type;
                    DB::table("issuelinks")->insert(collect($value['fields']['parent'])->merge($value['fields']['parent']['fields'])
                        ->only(\App\Helpers\GlobalHelper::getAllColsFromTbl("issuelinks", ""))
                        ->all());
                }
            }
    }

    public function jiraSyncImporter(Request $request, $inputVar = [])
    {
        $data = [];
        $totalCount = 50;
        $response = Unirest\Request::get(env('JIRA_APP_DOMAIN') . 'search', [
            'Accept' => 'application/json'
        ], [
            'jql' => 'project = ' . $request['project'] . ' AND issuetype in (' . $inputVar['issueType'] . ') ORDER BY created ASC, updated DESC',
            'maxResults' => $totalCount,
            'startAt' => $request->count
        ]);
        // dd($response->body);
        $data['step'] = $request->step;
        $data['project'] = $request['project'];
        $data['count'] = $request->count + $totalCount;
        $data['next'] = true;
        $data['title'] = $inputVar['progressText'];
        if (($data['count']) <= ($response->body['total'] + $totalCount))
            $this->autoRecall($response);
        else {
            $data['step'] = $request->step + 1;
            $data['count'] = 0;
            $data['next'] = true;
            $data['title'] = $inputVar['successText'];
        }
        $data['response'] = collect($response->body)->except("issues")->all();

        $data['html'] = view('dashboard/issues/recallCheck')->with($data)->toHtml();
        // echo ($data['html']);die;
        return response()->json($data);
    }

    public function issueImport(Request $request)
    {
        switch ($request->step) {
            case '0':
                // dd('dd');
                foreach ([
                    'temp_jira_data',
                    'mainTableItem',
                    'issuetype',
                    'project', /* 'assignee', */ 'issuelinks'
                ] as $value) {
                    DB::table($value)->truncate();
                }
                $data['next'] = true;
                $data['count'] = 0;
                $data['step'] = 1;
                $data['project'] = $request['project'];
                $data['response'] = null;
                $data['title'] = "Database Successfully Refined....";
                $data['html'] = view('dashboard/issues/recallCheck')->with($data)->toHtml();
                return response()->json($data);
                break;
            case '1':
                return $this->jiraSyncImporter($request, [
                    "progressText" => 'Epic\'s is reading....',
                    "successText" => 'Epic\'s is successfully synced...',
                    "issueType" => 'Epic'
                ]);
                break;
            case '2':
                // return response()->json(['next' => false]);
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
                return response()->json([
                    'next' => false
                ]);
                break;
        }

        // echo json_encode($response->body->issues);
        // exit;

        echo dd($response->body);
        // return view()
    }

    public function getCountIssues(Request $request)
    {
        $steps = [
            '0' => 'machine',
            '1' => 'Bug, Epic, Story, Task, "Test Case", Sub-task',
            '2' => 'Story',
            '3' => 'Bug',
            '4' => 'Sub-task'
        ];
        if (in_array($request->step, [
            '0'
        ]))
            return response()->json([
                'total' => 1
            ]);

        $response = Unirest\Request::get(env('JIRA_APP_DOMAIN') . 'search', [
            'Accept' => 'application/json'
        ], [
            'jql' => 'project = ' . env("JIRA_PROJECT_KEY") . ' AND issuetype in (' . $steps[$request->step] . ') ORDER BY priority DESC, updated DESC',
            'maxResults' => 10,
            'startAt' => 0
            // 'expand'=>'changelog',
        ]);
        // $data['total'] = $response->body->total; //collect($response->body)->except("issues")->all();
        $data['response'] = $response->body;
        // dd(Arr::get($response->body['issues'][0], "fields.status.name"));
        return response()->json($data);
    }

    public function jiraDataExporter(Request $request, $inputVar = [])
    {
        $data = [];
        $totalCount = 20;
        Unirest\Request::auth('shantanu.sharma@kreatetechnologies.com', 'vRZLGMKSFuIoUh6DHrbb9E81');
        $response = Unirest\Request::get(env('JIRA_APP_DOMAIN') . 'search', [
            'Accept' => 'application/json'
        ], [
            'jql' => 'project = ' . $request['project'] . ' AND issuetype in (' . $inputVar['issueType'] . ') ORDER BY created ASC, updated DESC',
            'maxResults' => $totalCount,
            'startAt' => $request->count
        ]);
        $data['step'] = $request->step;
        $data['project'] = $request['project'];
        $data['count'] = $request->count + $totalCount;
        $data['next'] = true;
        $data['title'] = $inputVar['progressText'];

        if (($data['count']) <= $response->body->total)
            collect($response->body->issues)->map(function ($ar) {
                DB::table("temp_jira_data")->insert([
                    "issuetype" => $ar->fields->issuetype->name,
                    'created' => Carbon::parse($ar->fields['created'])->format('Y-m-d H:i:s'),
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
        // echo ($data['html']);die;
        return response()->json($data);
    }

    //
}
