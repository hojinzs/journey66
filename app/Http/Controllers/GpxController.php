<?php

namespace App\Http\Controllers;

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
                //code...
                $filename = md5(microtime())."-".$request->gpx->getClientOriginalName();

                //Polyline Parsing & Encoding
                $xml = $request->gpx;
                $points = GpxController::getPointArraytoXml($xml);
                $output = GpxController::getEncodedPolyline($points);

                //Save polyline data to tmp folder
                $disk = Storage::disk('gcs');
                $polypath = $disk->put('tmp',$filename.'.poly',$output);

                //Keep Gpx file to tmp folder
                $gpxpath = $request->gpx->storeAs('tmp',$filename,'gcs');

                return response($gpxpath);
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
            //code...
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

    public static function getPointArraytoXml($xml){
        $gpx = new phpGPX();
        $file = $gpx->load($xml);
    
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

    public static function getEncodedPolyline($points = [],$compresslenth = null){

        $encode1 = new GooglePolyline;
        $output1 = $encode1->encodePoints($points);

        if($compresslenth == null){
            return $output1;
        }

        //compress Polyline
        $i = strlen($output1);
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

        $googleObject2 = new GooglePolyline;
        $output2 = $googleObject2->encodePoints($comp_point);

        return $output1;

    }
}

class GooglePolyline
{
  use \emcconville\Polyline\GoogleTrait;
}