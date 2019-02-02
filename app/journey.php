<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Storage;

class journey extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public $timestamps = false;


    public function getGPXxml()
    {
        $disk = Storage::disk('gcs');
        $xml = $disk->get($this->file_path);

        return $xml;
    }

    public function setStartingPotint()
    {
        $starting = $this->waypoints()->where('type','starting')->first();

        if(!$starting) return;

        $this->started_lat = $starting->latitude;
        $this->started_lng = $starting->longitude;
        $this->started_timezone = $starting->timezone;
        
        $this->save();
    }

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
