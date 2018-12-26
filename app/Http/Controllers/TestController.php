<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GpxController;
use Illuminate\Http\Request;
use phpGPX\phpGPX;

class TestController extends Controller
{
    //

    public function api(Request $request){
        //

    }

    public function web(Request $request){
        //

        return view('test');
    }
}