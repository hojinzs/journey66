<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class gpx
{

    public static function uploadGPX($gpx,$text="null")
    {
        try {
            //make hash
            $file_name = md5(microtime().$text);
            $path = 'gpxs/'.$file_name.'.gpx';

            //upload gpx
            $disk = Storage::disk('gcs');
            $disk->put($path,$gpx);
            $url = $disk->url($path);
        } catch (\Throwable $th) {
            //throw $th;
            
            return $th;
        }

        //return path, URL
        return $url;
    }
    
}
