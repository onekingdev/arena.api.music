<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Related extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_related";

    public $timestamps = false;
}
