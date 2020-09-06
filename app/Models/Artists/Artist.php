<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $guarded = [];

    protected $primaryKey = "artist_id";

    protected $table = "artists";

    public $timestamps = false;
}
