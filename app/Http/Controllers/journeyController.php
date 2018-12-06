<?php

namespace App\Http\Controllers;

use App\gpx;
use App\journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        try {
            //code...
            $gpx = urldecode(base64_decode($request->input('gpx')));

            //hashmake using author name & email
            $email = $request->input('confirm.email');
            $author = $request->input('confirm.author');
            $key = Hash::make($email.$author);

            $gpx_path = gpx::uploadGPX($gpx);

            //create new journey
    
            $journey = new journey;
    
            $journey->name = $request->input('title.journey-title');
            $journey->description = $request->input('title.journey-description');
            $journey->type = $request->input('title.journey-type');
            $journey->file_path = $request->$gpx_path;
            $journey->key = $key;
            
            $journey->author_email = $email;
            $journey->author_name = $author;
    
            $journey->save();

            $insertedId = $journey->id;

        } catch (\Throwable $th) {
            //throw $th;

            return $th;
        }

        return $insertedId;
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

}
