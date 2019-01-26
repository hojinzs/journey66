<?php

namespace App\Http\Controllers;

use Validator;
use App\waypoint;
use App\Http\Requests\AuthByWaypoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ImageController extends Controller
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
        // Validation
        $validateData = Validator::make($request->all(),[
            'image' => 'required|image',
            'size' => 'max:15,360'
        ]);
        if($validateData->fails()){
            return response("Validation failed",400);
        }

        //
        if($request->hasFile('image')){
            $path = $request->image->store('tmp','gcs');
            $disk = Storage::disk('gcs');
            $disk->setVisibility($path,'public');
            $url = $disk->url($path);
            return response()->json([
                'url' => $url,
                'filename' => $path
            ]);
        };

        return response('Can not find image',400);
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
     * @param  \Illuminate\Http\Request\AuthByWaypoint  $request
     * @param  int  $id
     * @param  int  $num
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuthByWaypoint $request,$id,$num)
    {
        //AuthValidation
        $validated = $request->validated();

        try {
            $image = waypoint::where('UWID',$id)->first()
                ->waypoint_images()->where('id',$num)->first();
            $image->delete();
            return response('delete_success');

        } catch (\Throwable $th) {
            //throw $th;
            return response($th,400);
        }
    }
}
