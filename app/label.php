<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class label extends Model
{

    public $timestamps = false;

    public static function getWhere($key)
    {
        $labels = label::select('id','name','description')
        ->where('where','=',$key)
        ->get();

        return $labels;
    }
    
}
