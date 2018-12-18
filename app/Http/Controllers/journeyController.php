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
            $new_journey = journeyController::setJourney($request);

            // set new Waypoints
            $wpArr = $request->input('waypoints');
            foreach ($wpArr as $key => $waypoint) {
                $new_waypoint = journeyController::setWaypoint($waypoint,$new_journey['id'],$key+1);
                $UWID[] = $new_waypoint['UWID'];

                //set Waypoint images
                $imgs = $waypoint['imgs'];
                foreach ($imgs as $key => $img) {
                    $new_image = journeyController::StoreImg($img,$new_waypoint['id'],$key);
                    $images[] = $new_image['path'];
                };
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response($th,400);
        }

        return response()->json([
            'UJID' => $new_journey->UJID,
            'UWID' => $UWID,
            'IMGS' => $images,
            'stauts' => 'success'
        ]);
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
        $journey = journey::where('UJID',$id)->first();
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
                'waypoints' => $waypoints,
                'gpx' => basename($journey->file_path)
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
        $journey = journey::where('UJID',$id)->first();
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

            $journey_labels = label::getWhere('journey_type');
            $waypoint_labels = label::getWhere('waypoint_type');

            return view('editJourney',[
                'journey' => $journey,
                'waypoints' => $waypoints,
                'gpx' => basename($journey->file_path),
                'journey_labels' => $journey_labels,
                'waypoint_labels' => $waypoint_labels,
                ]);
        };
        return redirect('404');
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
        // find target journey data
        $get_journey = journey::where('UJID',$id)->first();
        if(!$get_journey){
            return response('can not find journey '.$id,400);
        }

        try {
            // update Journey Data
            $saved_journeyid = journeyController::UpdateJourney($request,$get_journey->id);

            // update Waypoint Data
            foreach ($request->input('waypoints') as $key => $waypoint) {

                $sequnce = $key + 1;

                if (isset($waypoint['uwid'])) {
                    # currnet Waypoint. update it
                    $updated_waypoint = journeyController::UpdateWaypoint($waypoint,$waypoint['uwid'],$sequnce);

                    # control images
                    journeyController::ImgClasification($waypoint['imgs'],$updated_waypoint['id']);

                } else {
                    # new Waypoint. make it
                    $new_waypoint = journeyController::setWaypoint($waypoint,$saved_journeyid,$sequnce);

                    # control images
                    journeyController::ImgClasification($waypoint['imgs'],$new_waypoint['id']);

                }

            }
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
    

        // return response()->json([
        //     'UJID' => $new_journey['UJID'],
        //     'UWID' => $UWID,
        //     'IMGS' => $images,
        //     'stauts' => 'success'
        // ]);
        return "success";
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
        $email = $request->input('email');
        $author = $request->input('author');
        $key = Hash::make($email.$author);

        $gpx_path = journeyController::StoreGpxFile($request->input('gpx'));

        $journey->UJID = 'tmp'.time();
        $journey->name = $request->input('title');
        $journey->description = $request->input('description');
        $journey->type = $request->input('type');
        $journey->file_path = $gpx_path;
        $journey->key = $key;
        $journey->author_email = $email;
        $journey->author_name = $author;

        //insert
        $journey->save();

        //update
        $insertedId = $journey->id;
        $UJID = 'JN'.hash('crc32b',$insertedId);
        $journey->UJID = $UJID;
        $journey->save();

        return $journey;
    }

    /**
     * 
     */
    private function setWaypoint($request,$journey_id,$sequnce){

        // make resource
        $UWID = 'WP'.hash('crc32b',$journey_id.'.'.$sequnce.'.'.microtime());

        // set
        $waypoint = new waypoint;

        $waypoint->UWID = $UWID;
        $waypoint->journey_id = $journey_id;
        $waypoint->sequence = $sequnce;
        $waypoint->name = $request['name'];
        $waypoint->description = $request['description'];
        $waypoint->type = $request['type'];
        $waypoint->latitude = $request['Lat'];
        $waypoint->longitude = $request['Lng'];

        // input
        $waypoint->save();

        return $waypoint;

    }

    private function StoreImg($request,$waypoint_id,$key){
        $path = journeyController::StoreTmpImgFile($request['path']);

        $img = new waypoint_image;
        $img->waypoint_id = $waypoint_id;
        $img->number = $key;
        $img->type = 'image';
        $img->path = $path;

        $img->save();

        return $img;
    }

    private function StoreGpxFile($temp_path){

        //move tmp gpx file to gpx folder
        $disk = Storage::disk('gcs');
        $moved_path = 'gpxs/'.basename($temp_path);
        $disk->move($temp_path,$moved_path);

        return $moved_path;
    }

    private function StoreTmpImgFile($temp_path){
        $disk = Storage::disk('gcs');
        $moved_path = 'imgs/'.basename($temp_path);
        
        $disk->move($temp_path,$moved_path);
        $moved_url = $disk->url($moved_path);

        return $moved_url;
    }

    private function UpdateJourney($request,$id){

        //set Journey
        $journey = journey::where('id',$id)->first();

        //set Data
        $journey->name = $request->input('title');
        $journey->type = $request->input('type');
        $journey->description = $request->input('description');
        $journey->author_email = $request->input('email');
        $journey->author_name = $request->input('author');

        //update
        $journey->save();

        return $journey->id;
    }

    private function UpdateWaypoint($request,$uwid,$sequnce = NULL){

        // find
        $waypoint = waypoint::where('UWID',$uwid)->first();

        // set data
        if($sequnce){
            $waypoint->sequence = $sequnce;
        };
        $waypoint->name = $request['name'];
        $waypoint->description = $request['description'];
        $waypoint->type = $request['type'];
        $waypoint->latitude = $request['Lat'];
        $waypoint->longitude = $request['Lng'];

        // update
        $waypoint->save();

        return $waypoint;

    }

    private function ImgClasification($images = [],$waypoint_id){

        foreach ($images as $key => $img) {
            # Clasification Images
            switch ($img['type']) {
                case 'tmp':
                    # temponary saved image file. store it
                    journeyController::StoreImg($img,$waypoint_id,$key);
                    break;

                case 'del':
                    # delete this image file. destroy it
                    journeyController::DeleteImg($img['id']);
                    break;

                case 'cur':
                    # current image file. just update index number
                    journeyController::UpdateImg($img['id'],$key);
                    break;
                
                default:
                    # code...
                    break;
            }
            
        }
        
    }

    private function DeleteImg($img_id){
        $img = waypoint_image::where('id',$img_id)->first();
        $img->delete();
        return $img;
    }

    private function UpdateImg($img_id,$key){
        $img = waypoint_image::where('id',$img_id)->first();
        $img->number = $key;
        $img->save();
        return $img;
    }

}
