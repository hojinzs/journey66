<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class waypoint_image extends Model
{
    //
    public $timestamps = false;

    public function waypoint()
    {
        return $this->belongsTo('App\waypoint');
    }
}
