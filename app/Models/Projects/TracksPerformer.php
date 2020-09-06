<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class TracksPerformer extends Model
{
    protected $guarded = [];

    protected $primaryKey = "performer_id";

    protected $table = "projects_tracks_performers";

    public $timestamps = false;
}
