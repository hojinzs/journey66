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

use App\Http\Requests\AuthByJourneyKey;

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
        try {
            // set new Journeys
            $new_journey = journeyController::setJourney($request);

            //get Sequence Array
            // $disk = Storage::disk('gcs');
            // $xml = $disk->get($new_journey['file_path']);
            // $sequence = GpxController::getSequenceArrayFromXml($xml);

            $xml = $new_journey->getGPXxml();
            $sequence = GpxController::getSequenceArrayFromXml($xml);

            // set new Waypoints
            $UWID = [];
            $images = [];
            $wpArr = $request->input('waypoints');
            foreach ($wpArr as $key => $waypoint) {
                $new_waypoint = journeyController::setWaypoint($waypoint,$new_journey['id'],$sequence);
                $UWID[] = $new_waypoint['UWID'];

                //set Waypoint images
                $imgs = $waypoint['imgs'];
                foreach ($imgs as $key => $img) {
                    $new_image = journeyController::StoreImg($img,$new_waypoint['id'],$key);
                    $images[] = $new_image['path'];
                };
            }
            //set Starting Point
            $new_journey->setStartingPotint();
        } catch (\Throwable $th) {
            //throw $th;
            return response($th,400);
        }

        // Set Meta Data
        try{
            if($request->has('staticmap')){
                $summary_path = journeyController::setSummaryMap($request->input('staticmap'),$new_journey['UJID']);

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
            'IMG' => $new_journey->getImages(),
            'cover' => $new_journey->getCover(),
            'KEY' => $new_journey->key,
            'stauts' => 'success',
            'mail' => $mail_send,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        $journey = journey::where('UJID',$id)->firstOrFail();

        // return  $journey->key."  and  ".$request->query('key');

        if($journey->publish_stage =='Published' || $request->query('key') == $journey->key ){
            $arr = waypoint::where('journey_id',$journey->id)
                ->join('labels', 'waypoints.type', '=', 'labels.name')
                ->select('waypoints.*', 'labels.icon')
                ->orderBy('sequence','asc')
                ->get();
            $waypoints = array();

            foreach($arr as $k => $waypoint){
                $images = waypoint_image::where('waypoint_id',$waypoint->id)->get();
                if($images){
                    $waypoint['images'] = $images;
                }
                array_push($waypoints,$waypoint);
            };

            //get Polyline & encode
            $poly = $journey->polyline_path;
            $cpoly = GpxController::getCompressedPolyline($poly,2000);

            return view('showJourney',[
                'journey' => $journey,
                'waypoints' => $waypoints,
                'gpx' => basename($journey->file_path),
                'summary_polyline' => $cpoly,
                ]);
        } return abort(401,"unpublished contents");
    }

    /**
     * 
     */
    public function get(Request $request, $id)
    {
        $journeyKey = $request->query('key');
        $journey = journey::where('UJID',$id)->first();
        if($journey){
            if($journey['publish_stage']=='Published'||$journey['key'] == $journeyKey ){
                $arr = waypoint::where('journey_id',$journey->id)->orderBy('sequence','asc')->get();
                $waypoints = array();
    
                foreach($arr as $k => $waypoint){
                    $images = waypoint_image::where('waypoint_id',$waypoint->id)->get();
                    if($images){
                        $waypoint['images'] = $images;
                    }
                    array_push($waypoints,$waypoint);
                };

                //get Polyline & encode
                $poly = $journey->polyline_path;
                $cpoly = GpxController::getCompressedPolyline($poly,2000);

                    //get sequence array
                    $disk = Storage::disk('gcs');
                    $xml = $disk->get($journey->file_path);
                    $sequence = GpxController::getSequenceArrayFromXml($xml);
                    $stats = GpxController::getSummarizable($xml);

                return response()->json([
                    'journey' => $journey,
                    'waypoints' => $waypoints,
                    'polyline' => $poly,
                    'summary_polyline' => $cpoly,
                    'sequence' => $sequence,
                    'stats' => $stats,
                ]);
            };
            return abort(403,'Unauthorized action - key undefined');
        };
        return abort(400,'Cannot find Journey');
    }

        /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRandom()
    {

        $shuffle = $this->ShufflingActivate();

        if($shuffle['activate'] == false) return abort(403,'unactivate -'.__('journey.home.shuffle_cointer',['count' => $shuffle['remain']]));

        $journey = journey::select('UJID')
            ->where('publish_stage','=','Published')
            ->inRandomOrder()
            ->first();
        return redirect()
            ->action('journeyController@show',[
                'id' => $journey->UJID
            ]);

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
            return redirect()
                ->action('journeyController@edit',['id'=>$id])
                ->with('journeyKey',$requestKey);
        } else {
            # invaild connection
            return abort(403,'Unauthorized action - key undefined');
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
            return abort(400,'Cannot find Journey');
        }

        // check Auth
        if ($request->session()->has('journeyKey')) {
            # check sessionkey
            $sessionKey = $request->session()->get('journeyKey');
            if($journey->key != $sessionKey){
                return abort(403,'Unauthorized action - key unmatched');
            };
        } else {
            # redirect
            return abort(403,'Unauthorized action - key invailed');
        }

        $request->session()->keep(['journeyKey']);

        // set waypoint data
        $arr = waypoint::where('journey_id',$journey->id)->orderBy('sequence','asc')->get();
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
     * @param  \Illuminate\Http\Request\AuthByJourneyKey  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AuthByJourneyKey $request, $id)
    {
        // Validation
        $validated = $request->validated();

        // find target journey data
        $get_journey = journey::where('UJID',$id)->first();
        if(!$get_journey){
            return response('can not find journey '.$id,400);
        };

        try {
            // update Journey Data
            $saved_journey = journeyController::UpdateJourney($request,$get_journey->id);

            $xml = $saved_journey->getGPXxml();
            $sequence = GpxController::getSequenceArrayFromXml($xml);

            // update Waypoint Data
            $UWID = [];
            $IMG = [];
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
                            $updated_waypoint = journeyController::UpdateWaypoint($waypoint,$waypoint['uwid']);
                            $UWID[] = $updated_waypoint['UWID'];
        
                            # control images
                            $images = journeyController::ImgClasification($waypoint['imgs'],$updated_waypoint['id']);
                            $IMG = array_merge($IMG,$images);
                            break;
                        default:
                            # code...
                            break;
                    }

                } else {
                    # new Waypoint. make it
                    $waypoints_index = $waypoints_index +1;
                    $new_waypoint = journeyController::setWaypoint($waypoint,$saved_journey['id'],$sequence);
                    $UWID[] = $new_waypoint['UWID'];

                    # control images
                    $images = journeyController::ImgClasification($waypoint['imgs'],$new_waypoint['id']);
                    $IMG = array_merge($IMG,$images);
                };

            };
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

        return response()
            ->json([
            'stauts' => 'success',
            'UJID' => $saved_journey['UJID'],
            'UWID' => $UWID,
            'IMG' => $saved_journey->getImages(),
            'cover' => $saved_journey->getCover(),
            'KEY' => $saved_journey->key,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuthByJourneyKey $request,$id)
    {
        // Validation
        $validated = $request->validated();

        try {
            //destory journey data
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
        // $email = $request->input('email');
        // $author = $request->input('author');
        $key = base64_encode(Hash::make(
            $request->input('email')
            .$request->input('email')
            .now()->timestamp
        ));

        $gpx_path = journeyController::StoreTmpFile($request->input('gpx'),'gpxs');
        // $polyline_path = journeyController::StoreTmpFile($request->input('polyline'),'poly');

        $journey->UJID = 'tmp'.time();
        $journey->name = $request->input('title');
        $journey->description = $request->input('description');
        $journey->type = $request->input('type');
        $journey->file_path = $gpx_path;
        $journey->polyline_path = $request->input('polyline');
        $journey->key = $key;
        $journey->author_email = $request->input('email');
        $journey->author_name = $request->input('author');

        // set stats data
        $xml = Storage::disk('gcs')->get($gpx_path);
        $stats = GpxController::getSummarizable($xml);
            $journey->distance = $stats['distance'];
            $journey->elevation = $stats['elevation'];
            $journey->duration = $stats['duration'];
            $journey->startedAt = $stats['startedAt'];
            $journey->finishedAt = $stats['finishedAt'];

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
    private function setWaypoint($request,$journey_id,$sequence){

        // make resource
        $UWID = 'WP'.hash('crc32b',$journey_id.'.'.$request['sequence'].'.'.microtime());

        // set
        $waypoint = new waypoint;

        $waypoint->UWID = $UWID;
        $waypoint->journey_id = $journey_id;
        $waypoint->sequence = $request['sequence'];
        $waypoint->name = $request['name'];
        $waypoint->description = $request['description'];
        $waypoint->type = $request['type'];
        $waypoint->latitude = $request['Lat'];
        $waypoint->longitude = $request['Lng'];

        // find stat data from sequnece
        if($sequence){
            $waypoint->distance = $sequence[$request['sequence']]['distance'];
            $waypoint->elevation = $sequence[$request['sequence']]['elevation'];
            $waypoint->time = $sequence[$request['sequence']]['time'];
            $waypoint->latitude =  $sequence[$request['sequence']]['latitude'];
            $waypoint->longitude = $sequence[$request['sequence']]['longitude'];
            $waypoint->timezone = \App\Calc::getTimezone(
                $sequence[$request['sequence']]['latitude'],
                $sequence[$request['sequence']]['longitude']
            );
        };

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

    private function UpdateWaypoint($request,$uwid){

        // find
        $waypoint = waypoint::where('UWID',$uwid)->first();

        // set data
        $waypoint->name = $request['name'];
        $waypoint->description = $request['description'];
        $waypoint->type = $request['type'];
        $waypoint->sequence = $request['sequence'];
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

        $waypoint_images = [];
        foreach ($images as $key => $img) {
            # Clasification Images
            switch ($img['type']) {
                case 'tmp':
                    # temponary saved image file. store it
                    $stored = journeyController::StoreImg($img,$waypoint_id,$key);
                    $waypoint_images[] = $stored;
                    break;

                case 'del':
                    # delete this image file. destroy it
                    journeyController::DeleteImg($img['id']);
                    break;

                case 'cur':
                    # current image file. just update index number
                    $updated = journeyController::UpdateImg($img['id'],$key);
                    $waypoint_images[] = $updated;
                    break;
                
                default:
                    # code...
                    break;
            }   
        }
        return $waypoint_images;
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

    //temponary feature. delete it when Shuffling is Activation
    public static function ShufflingActivate()
    {
        $published = \App\journey::where('publish_stage','=','Published')->get();
        $count = count($published);

        if($count >= 30){
            $shuffle = [
                'activate' => true,
                'remain' => 0,
            ];
        } else {
            $remain = (30 - $count);
            $shuffle = [
                'activate' => false,
                'remain' => $remain,
            ];
        };

        return $shuffle;
     }

}
