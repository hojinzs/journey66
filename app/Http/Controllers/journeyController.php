<?php

namespace App\Http\Controllers;

use App\gpx;
use App\label;
use App\journey;
use App\waypoint;
use App\waypoint_image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;

class journeyController extends Controller
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
    public function create(Request $request)
    {
        // get Label Data
        $journey_labels = label::getWhere('journey_type');
        $waypoint_labels = label::getWhere('waypoint_type');

        return view('createJourney',[
            'journey_labels' => $journey_labels,
            'waypoint_labels' => $waypoint_labels,
        ]);
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

        try {

            // set new Journeys
            $jn = journeyController::setJourney($request);
            $UJID = $jn['UJID'];

            // set new Waypoints
            $wpArr = $request->input('waypoints');
            $wps = journeyController::setWaypoints($wpArr,$jn['id']);
            $UWID = $wps;

        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

        return response()->json([
            'UJID' => $UJID,
            'UWID' => $UWID,
            'stauts' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($UJID)
    {
        //
        $journey = journey::where('UJID',$UJID)->first();
        if($journey){
            $arr = waypoint::where('journey_id',$journey->id)->get();
            $waypoints = array();

            foreach($arr as $k => $waypoint){
                $images = waypoint_image::where('waypoint_id',$waypoint->id)->get();
                if($images){
                    $waypoint['images'] = $images;
                }
                array_push($waypoints,$waypoint);
            };

            return view('showJourney',[
                'journey' => $journey,
                'waypoints' => $waypoints
                ]);
        };
        return redirect('404');
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

    private function setJourney($request){
        // set
        $journey = new journey;

        // make resource
        $gpx = urldecode(base64_decode($request->input('gpx')));
        $email = $request->input('email');
        $author = $request->input('author');
        $key = Hash::make($email.$author);
        $gpx_path = gpx::uploadGPX($gpx);

        $journey->UJID = 'tmp'.time();
        $journey->name = $request->input('title');
        $journey->description = $request->input('description');
        $journey->type = $request->input('type');
        $journey->file_path = $gpx_path;
        $journey->key = $key;
        $journey->author_email = $email;
        $journey->author_name = $author;

        // insert
        $journey->save();
        $insertedId = $journey->id;
        $UJID = 'JN'.hash('crc32b',$insertedId);
        $journey->UJID = $UJID;
        $journey->save();

        $v['id'] = $insertedId;
        $v['UJID'] = $UJID;

        //update

        return $v;
    }

    /**
     * 
     */
    private function setWaypoints($WArr,$j_id){

        $v = [];
        
        foreach ($WArr as $k => $wp) {
            // make resource
            $sequnce = $k +1;
            $UWID = 'WP'.hash('crc32b',$j_id.'.'.$k);

            // set
            $waypoint = new waypoint;

            $waypoint->UWID = $UWID;
            $waypoint->journey_id = $j_id;
            $waypoint->sequence = $sequnce;
            $waypoint->name = $wp['name'];
            $waypoint->description = $wp['description'];
            $waypoint->type = $wp['type'];
            $waypoint->latitude = $wp['Lat'];
            $waypoint->longitude = $wp['Lng'];

            // input
            $waypoint->save();

            $v[$k] = $waypoint->UWID;

        };

        return $v;

    }

}
