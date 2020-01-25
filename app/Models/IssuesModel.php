<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuesModel extends Model {

    protected $table = "mainTableItem";
    protected $primaryKey = "main_issue_id";

    public function getCreatedAttribute($value) {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format("Y-m-d");
    }
    public function getTimespentAttribute($value) {
        return round(($value/60)/60,2);
    }
    
    public function getLinkedIssue() {
        return $this->hasMany('App\Models\IssuelinksModel', 'linked_id');
    }

    //
}
