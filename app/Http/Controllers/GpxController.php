<?php

namespace App\Http\Controllers;

use Config;
use phpGPX\phpGPX;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GpxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if($request->hasFile('gpx')){
            try {

                //Polyline Parsing & Encoding
                $xml = $request->gpx;
                $points = GpxController::getPointArraytoXml($xml);
                $sequence = GpxController::getSequenceArrayFromXml($xml);
                $encoded_polyline = GpxController::getEncodedPolyline($points);
                $encoded_polyline_summary = GpxController::getCompressedPolyline($encoded_polyline,2000);
                $stats = GpxController::getSummarizable($xml);

                //Save polyline data to tmp folder
                $disk = Storage::disk('gcs');
                $filename = md5(microtime())."-".$request->gpx->getClientOriginalName();
                // $disk->put('tmp/'.$filename.'.poly',$encoded_polyline);
                // $polypath = 'tmp/'.$filename.'.poly';

                //Keep Gpx file to tmp folder
                $gpxpath = $request->gpx->storeAs('tmp',$filename,'gcs');

                return response()->json([
                    'stats' => $stats,
                    'gpx_path' => $gpxpath,
                    // 'polyline_path' => $polypath,
                    'points' => $points,
                    'sequence' => $sequence,
                    'encoded_polyline' => $encoded_polyline,
                    'encoded_polyline_summary' => $encoded_polyline_summary,
                ]);
            } catch (\Throwable $th) {
                //throw $th;
                return $th;
            }
        };

        return response('not gpx file',400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            $get = Storage::disk('gcs')->get('gpxs/'.$id);
            return response($get,200);
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public static function getSummarizable($path){
        $gpx = new phpGPX();
        
        try {
            //code...
            $file = $gpx->load($path);
        } catch (\Throwable $th) {
            //throw $th;
            $file = $gpx->parse($path);
        };

        foreach($file->tracks as $track)
        {
            $stats = $track->stats->toArray();
            break;
        };

        $startedAt = \Carbon\Carbon::parse($stats['startedAt'])->toDateTimeString();
        $finishedAt = \Carbon\Carbon::parse($stats['finishedAt'])->toDateTimeString();

        $array = [
            'distance' => $stats['distance'],
            'duration' => $stats['duration'],
            'elevation' => $stats['cumulativeElevationGain'],
            'startedAt' => $startedAt,
            'finishedAt' => $finishedAt,
            'timezone' => ''
        ];

        return $array;
    }

    public static function getPointArraytoXml($xml){
        $gpx = new phpGPX();
        try {
            //code...
            $file = $gpx->load($xml);
        } catch (\Throwable $th) {
            //throw $th;
            $file = $gpx->parse($xml);
        };
    
        $points = [];
        foreach ($file->tracks as $track)
        {
            // Statistics for whole track
            $track_points = $track->getPoints();
            foreach ($track_points as $point) {
                # code...
                // $points[] = $point;
                $points[] = [$point->latitude,$point->longitude];
            }
        }
        return $points;
    }


    public static function getSequenceArrayFromXml($xml){
        $gpx = new phpGPX();

        try {
            //code...
            $file = $gpx->load($xml);
        } catch (\Throwable $th) {
            //throw $th;
            $file = $gpx->parse($xml);
        }
    
        $points = [];
        foreach ($file->tracks as $track)
        {
            $track_points = $track->getPoints();
            foreach ($track_points as $sequence => $point) {
                
                // $time = \Carbon\Carbon::instance($point->time)->timezone(Config::get('app.timezone'))->toDateTimeString();
                // $distance = round($point->distance * 0.001,2)."km";

                $time = \Carbon\Carbon::instance($point->time)->toDateTimeString();

                $points[] = [
                    'sequence' => $sequence,
                    'latitude' => $point->latitude,
                    'longitude' => $point->longitude,
                    'distance' => $point->distance, // $distance,
                    'elevation' => $point->elevation,
                    'time' => $time,
                ];
            }
        }

        return $points;
    }

    public static function getEncodedPolyline($points = []){

        $encode1 = new GooglePolyline;
        $output1 = $encode1->encodePoints($points);

        return $output1;

    }

    public static function getCompressedPolyline($encodedPolyline,$compresslenth)
    {
        $i = strlen($encodedPolyline);
        if($compresslenth >= $i){
            return $encodedPolyline;
        };

        //Decode and Get points
        $googlePolyline1 = new GooglePolyline;
        $points = $googlePolyline1->decodeString($encodedPolyline);

        //compress Polyline Start
        $g = $compresslenth;
        $s = $i/($g-1000);
        $f = floor($s);
        $comp_point = [];
        $c = 0;
        foreach ($points as $p) {
            # code...
            if($c == $f){
                $comp_point[] = $p;
                $c = 0;
            } else {
                $c = $c + 1;
            }

        };

        //encode
        $googleObject2 = new GooglePolyline;
        $output2 = $googleObject2->encodePoints($comp_point);

        return $output2;

    }
}

/**
 * Reference :: https://github.com/emcconville/polyline-encoder
 */
class GooglePolyline
{
  use \emcconville\Polyline\GoogleTrait;
}