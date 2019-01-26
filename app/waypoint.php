<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class waypoint extends Model
{
    //
    public $timestamps = false;

    /**
     * get all of the waypoint's metas
     */
    public function metas()
    {
        return $this->morphMany('App\Meta','metable');
    }

    public function waypoint_images()
    {
        return $this->hasMany('App\waypoint_image','waypoint_id');
    }

}
