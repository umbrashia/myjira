<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectModel extends Model {

    protected $table = "project";
    protected $primaryKey = "auto_id";
    public $timestamps = false;
    protected $fillable = [
        "auto_id",
        "issue_id",
        "self",
        "id",
        "key",
        "name",
        "projectTypeKey",
        "simplified"];

    //
}
