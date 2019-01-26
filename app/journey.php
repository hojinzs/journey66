<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class journey extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public $timestamps = false;


    /**
     * get the sections for the journeys
     */
    public function comments()
    {
        return $this->hasMany('App\journey_section');
    }

    /**
     * get all of the journey's metas
     */
    public function metas()
    {
        return $this->morphMany('App\Meta','metable');
    }

    public function waypoints()
    {
        return $this->hasMany('App\waypoint','journey_id');
    }
}
