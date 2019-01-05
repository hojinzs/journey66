<?php

namespace App\Http\Controllers;

use App\label;
use App\journey;
use App\waypoint;
use App\waypoint_image;
use App\journey_meta;
use App\Mail\JourneyPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GpxController;

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
            $UWID = [];
            $images = [];
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

        // Set Meta Data
        try{
            if($request->has('staticmap')){
                $summary_path = journeyController::setSummaryMap($request->input('staticmap'),$new_journey['UJID']);
                $meta[] = journey_meta::setMetaData('mapimg',$summary_path,$new_journey['id']);
                $meta[] = journey_meta::setMetaData('thumbnail',$summary_path,$new_journey['id']);

                $new_journey->metas()->createMany([
                    [
                    'name' => 'mapimg',
                    'value' => $summary_path
                    ],
                    [
                    'name' => 'thumbnail',
                    'value' => $summary_path
                    ],
                ]);
                
            };
        } catch (\Throwable $th) {
            $meta = 'fail';
        }

        // Send Mail
        try {
            Mail::to($new_journey->author_email)
            ->send(new JourneyPosted($new_journey));
            $mail_send = $new_journey->author_email;
        } catch (\Throwable $th) {
            //throw $th;
            $mail_send = 'fail';
        }

        // response
        return response()
            ->json([
            'UJID' => $new_journey->UJID,
            'UWID' => $UWID,
            'IMGS' => $images,
            'mail' => $mail_send,
            'meta' => $meta,
            'stauts' => 'success',
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
            if($journey['publish_stage']=='Published'){
                $arr = waypoint::where('journey_id',$journey->id)->get();
                $waypoints = array();
    
                foreach($arr as $k => $waypoint){
                    $images = waypoint_image::where('waypoint_id',$waypoint->id)->get();
                    if($images){
                        $waypoint['images'] = $images;
                    }
                    array_push($waypoints,$waypoint);
                };

                //get Polyline & encode
                $disk = Storage::disk('gcs');
                $poly = $disk->get($journey->polyline_path);
                $cpoly = GpxController::getCompressedPolyline($poly,2000);
    
                return view('showJourney',[
                    'journey' => $journey,
                    'waypoints' => $waypoints,
                    'gpx' => basename($journey->file_path),
                    'summary_polyline' => $cpoly,
                    ]);
            };
            return redirect('404');
        };
        return redirect('404');
    }

        /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRandom()
    {

        $journey = journey::select('UJID')
            ->where('publish_stage','=','Published')
            ->inRandomOrder()
            ->first();
        return redirect('journey/'.$journey->UJID);

    }

    /**
     * Check key and confirm edit
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getEditAuth(Request $request, $id){

        if ($request->has('key')) {
            # flash sesstion journey key
            $requestKey = $request->query('key');
            $request->session()->flash('journeyKey',$requestKey);
            return redirect('journey/'.$id.'/editor');
        } else {
            # invaild connection
            return redirect('404');
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        // get journey ID
        try {
            //find journey
            $journey = journey::where('UJID',$id)->firstOrFail();
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('404');
        }

        // check Auth
        if ($request->session()->has('journeyKey')) {
            # check sessionkey
            $sessionKey = $request->session()->get('journeyKey');
            if($journey->key != $sessionKey){
                return redirect('404');
            };
        } else {
            # redirect
            return redirect('404');
        }

        // set waypoint data
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

        // check key
        if($get_journey->key != $request->input('key')){
            return response('invaild journey key',400);
        }

        try {
            // update Journey Data
            $saved_journey = journeyController::UpdateJourney($request,$get_journey->id);

            // update Waypoint Data
            $UWID = [];
            $waypoints_index = 0;
            foreach ($request->input('waypoints') as $key => $waypoint) {

                if (isset($waypoint['uwid'])) {
                    # when it is currnet Waypoint
                    switch ($waypoint['mode']) {
                        case 'del':
                            # delete it
                            journeyController::DeleteWaypoint($waypoint['uwid']);
                            break;
                        case 'edit':
                            # update it
                            $waypoints_index = $waypoints_index +1;
                            $updated_waypoint = journeyController::UpdateWaypoint($waypoint,$waypoint['uwid'],$waypoints_index);
                            $UWID[] = $updated_waypoint['UWID'];
        
                            # control images
                            journeyController::ImgClasification($waypoint['imgs'],$updated_waypoint['id']);                            
                            break;
                        default:
                            # code...
                            break;
                    }

                } else {
                    # new Waypoint. make it
                    $waypoints_index = $waypoints_index +1;
                    $new_waypoint = journeyController::setWaypoint($waypoint,$saved_journey['id'],$waypoints_index);
                    $UWID[] = $new_waypoint['UWID'];

                    # control images
                    journeyController::ImgClasification($waypoint['imgs'],$new_waypoint['id']);

                }

            }
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

        return response()
            ->json([
            'UJID' => $saved_journey['UJID'],
            'UWID' => $UWID,
            // 'IMGS' => $images,
            'stauts' => 'success',
        ]);
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
        try {
            //code...
            $journey = journey::where('UJID',$id)->first();
            $journey->delete();

            return response(200,'journey delete success');
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
        
    }

    private function setJourney($request){
        // set
        $journey = new journey;

        // make resource
        $email = $request->input('email');
        $author = $request->input('author');
        $key = Hash::make($email.$author);

        $gpx_path = journeyController::StoreTmpFile($request->input('gpx'),'gpxs');
        $polyline_path = journeyController::StoreTmpFile($request->input('polyline'),'poly');

        $journey->UJID = 'tmp'.time();
        $journey->name = $request->input('title');
        $journey->description = $request->input('description');
        $journey->type = $request->input('type');
        $journey->file_path = $gpx_path;
        $journey->polyline_path = $polyline_path;
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

    private function StoreTmpFile($temp_path,$type){

        //move tmp gpx file to gpx folder
        $disk = Storage::disk('gcs');
        $moved_path = $type.'/'.basename($temp_path);
        $disk->copy($temp_path,$moved_path);

        return $moved_path;
    }

    private function StoreTmpImgFile($temp_path){
        $disk = Storage::disk('gcs');
        $moved_path = 'imgs/'.basename($temp_path);
        
        $disk->copy($temp_path,$moved_path);
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
        $journey->publish_stage = $request->input('publish_stage');

        //update
        $journey->save();

        return $journey;
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

    private function DeleteWaypoint($uwid){
        try {
            // find
            $waypoint = waypoint::where('UWID',$uwid)->first();

            //delete
            $waypoint->delete();

            return 'success';
        } catch (\Throwable $th) {
            //throw $th;
            return 'fail';
        }
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

    private function UpdateImg($img_id,$key)
    {
        $img = waypoint_image::where('id',$img_id)->first();
        $img->number = $key;
        $img->save();
        return $img;
    }

    private function setSummaryMap($StaticMapURL,$UJID)
    {
        $img = file_get_contents($StaticMapURL);
        $path = 'imgs/'.$UJID.'_staticimags.png';
        $disk = Storage::disk('gcs');
        $disk->put($path,$img);
        $disk->setVisibility($path,'public');
        $url = $disk->url($path);

        return $url;
    }

}
