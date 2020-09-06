<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_moods";

    public $timestamps = false;
}
