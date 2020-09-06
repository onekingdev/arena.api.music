<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_genres";

    public $timestamps = false;
}
