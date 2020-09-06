<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "projects_moods";

    public $timestamps = false;
}
