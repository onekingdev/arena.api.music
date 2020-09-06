<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $guarded = [];

    protected $primaryKey = "track_id";

    protected $table = "projects_tracks";

    public $timestamps = false;
}
