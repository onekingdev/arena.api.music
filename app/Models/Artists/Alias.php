<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Alias extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_aliases";

    public $timestamps = false;
}
