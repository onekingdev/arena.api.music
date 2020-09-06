<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class TracksComposer extends Model
{
    protected $guarded = [];

    protected $primaryKey = "composer_id";

    protected $table = "projects_tracks_composers";

    public $timestamps = false;
}
