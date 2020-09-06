<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    protected $primaryKey = "project_id";

    protected $table = "projects";

    public $timestamps = false;
}
