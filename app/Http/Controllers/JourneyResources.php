<?php

namespace App\Http\Controllers;

use App\journey;
use App\journey_meta;

use Illuminate\Http\Request;

use App\Http\Requests\AuthByJourneyKey;

class JourneyResources extends Controller
{
    /**
     * Edit Journey Thumbnail Image
     */
    public function setThumbnail(AuthByJourneyKey $request, $id)
    {   
        // Validation
        $validated = $request->validated();

        try {
            //delete current thumbnail meta data
            $journey = journey::where('UJID',$id)->first();
            $old_thumbnails = $journey->metas()->where('name','thumbnail')->delete();

            // return $old_thumbnails;
            // $old_thumbnails[0]->delete();

            $url = $request->input('url');

            $new_thumbnails = $journey->setMetaData('thumbnail',$url);

            return $new_thumbnails;
        } catch (\Throwable $th) {
            //throw $th;
            return response($th);
        }
    }
}
