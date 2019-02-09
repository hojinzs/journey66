<?php

namespace App;

class Calc
{
    public static function getTimezone($latitude,$longitude)
    {
        // set Timezone
        // use Google Timezone API (https://developers.google.com/maps/documentation/timezone/intro)
        // replace Geonames(http://www.geonames.org/) later.
        $timestamp = \Carbon\Carbon::now()->getTimestamp();
        $url = "https://maps.googleapis.com/maps/api/timezone/json?"
            ."location=".$latitude
            .",".$longitude
            ."&timestamp=".$timestamp
            ."&key=".env('GOOGLE_MAPS_SERVER_KEY',null);

        // get
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);
        $response = json_decode($response->getBody(), true);

        return $response['timeZoneId'];
    }

    public static function getDistance($double)
    {
        $km = round((double)$double * 0.001,2)."km";
        return $km;
    }

    public static function getElevation($int)
    {

        $m = round($int,1)+"m";
        return $m;
    }
}