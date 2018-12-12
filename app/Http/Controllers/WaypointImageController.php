<?php

namespace App\Http\Controllers;

use App\journey;
use App\waypoint;
use App\waypoint_image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class WaypointImageController extends Controller
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
        $imgArr = $request->input('ImgList');

        try {
            $v = [];

            foreach($imgArr as $k => $img){
                $path = WaypointImageController::StoreImgFile($img['path']);
                $waypoint = waypoint::where('UWID',$img['target'])->first();
    
                $img = new waypoint_image;
    
                $img->waypoint_id = $waypoint['id'];
                $img->number = $k;
                $img->type = 'image';
                $img->path = $path;
    
                $img->save();
            }

            return response('success',200);
        } catch (\Throwable $th) {
            return $th;
        }

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

    private function StoreImgFile($path){
        $disk = Storage::disk('gcs');
        $moved_path = 'imgs/'.basename($path);
        
        $disk->move($path,$moved_path);
        $moved_url = $disk->url($moved_path);

        return $moved_url;
    }
}


