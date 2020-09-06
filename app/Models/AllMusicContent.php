<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllMusicContent extends Model
{
    protected $guarded = [];

    protected $primaryKey = "row_id";

    protected $table = "allmusic_content";

    public $timestamps = false;
}
