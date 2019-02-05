<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class journey_meta extends Model
{
    public $timestamps = false;

    public static function setMetaData($meta,$value,$journey_id)
    {
        $journey = journey::findOrFail($journey_id);

        $journey_meta = new journey_meta;
        $journey_meta->journey_id = $journey->id;
        $journey_meta->meta_name = $meta;
        $journey_meta->value = $value;
        $journey_meta->save();

        return $journey_meta;

    }

}
