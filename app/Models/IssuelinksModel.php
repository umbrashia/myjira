<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuelinksModel extends Model
{
    protected $table = "issuelinks";
    protected $primaryKey = "auto_id";
    
    function getIssue(){
        return $this->belongsTo('App\Models\IssuesModel','issue_id','main_issue_id')->whereIn("subtask_type",['testing', 'release management', 'project management', 'coding', 'code review', 'estimation', 'impact',]);
    }
    //
}
