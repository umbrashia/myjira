<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugTypeModel extends Model {

    protected $table = "bugType";
    protected $primaryKey = "auto_id";
    public $timestamps = false;
    protected $fillable = [
        "auto_id",
        "issue_id",
        "self",
        "value",
        "id"
    ];

    //
}
