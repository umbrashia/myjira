<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuelinksModel extends Model
{
    protected $table = "issuelinks";
    protected $primaryKey = "auto_id";
    
    function getIssue(){
        return $this->hasOne('App\Models\IssuesModel','main_issue_id','issue_id');//->whereIn("summary",['testing', 'release management', 'project management', 'coding', 'code review', 'estimation', 'impact',]);
    }
    //
}
