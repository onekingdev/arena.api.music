<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_members";

    public $timestamps = false;
}
