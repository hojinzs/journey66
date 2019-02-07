<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GpxController;
use Illuminate\Http\Request;
use phpGPX\phpGPX;

class TestController extends Controller
{
    //

    public function api(Request $request,$id){
        //
        return ;

    }

    public function web(Request $request){
        //
        return view('test');
    }

    public function phpGpxParser(Request $request){
        if($request->hasFile('gpx')){
            //Polyline Parsing & Encoding
            $xml = $request->gpx;
            $points = GpxController::getPointArraytoXml($xml);
            $sequence = GpxController::getSequenceArrayFromXml($xml);
            $encoded_polyline = GpxController::getEncodedPolyline($points);
            $encoded_polyline_summary = GpxController::getCompressedPolyline($encoded_polyline,2000);
            $meta = GpxController::getSummarizable($xml);
            $gpx = new phpGPX;
            $parse = $gpx->load($xml);
            
        } else {
            return response(400);
        };


        return response()
            ->json([
                'parse' => $meta,
                'polyline' => $encoded_polyline_summary,
                'sequence' => $sequence,
                'parseAll' => $parse,
            ]);
    }
}