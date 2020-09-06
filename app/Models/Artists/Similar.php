<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Similar extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_similar";

    public $timestamps = false;
}
