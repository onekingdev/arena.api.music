<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "projects_styles";

    public $timestamps = false;
}
