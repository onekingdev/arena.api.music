<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "projects_genres";

    public $timestamps = false;
}
