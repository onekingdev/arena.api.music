<?php

namespace App\Models\Artists;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "artists_styles";

    public $timestamps = false;
}
