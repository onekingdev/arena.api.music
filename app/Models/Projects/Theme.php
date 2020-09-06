<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "projects_themes";

    public $timestamps = false;
}
