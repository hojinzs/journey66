<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\JourneyPosted;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    //

    public function api(Request $request){

        // // input testcode
        try {
            //code...
            Mail::to($request->id)
            ->send(new JourneyPosted());

            return 'mail sent - '.$request->id;
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }

        return 'done';

    }
}
