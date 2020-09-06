<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Influenced extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_influenced";

    public $timestamps = false;
}
