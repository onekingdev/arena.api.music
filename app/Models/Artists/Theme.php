<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_themes";

    public $timestamps = false;
}
