<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class TracksFeature extends Model
{
    protected $guarded = [];

    protected $primaryKey = "feature_id";

    protected $table = "projects_tracks_features";

    public $timestamps = false;
}
