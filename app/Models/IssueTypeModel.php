<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueTypeModel extends Model {

    protected $table = "issuetype";
    protected $primaryKey = "auto_id";
    public $timestamps = false;
    protected $fillable = [
        "auto_id",
        "issue_id",
        "summary",
        "self",
        "id",
        "description",
        "iconUrl",
        "name",
        "subtask",
        "avatarId"];

    //
}
