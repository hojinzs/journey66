<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/write', function () {
    $journey_labels = App\label::getWhere('journey_type');
    $waypoint_labels = App\label::getWhere('waypoint_type');

    return view('create',[
        'journey_labels' => $journey_labels,
        'waypoint_labels' => $waypoint_labels,
    ]);
});

Route::get('/test', function () {

    $journey_labels = App\label::getWhere('journey_type');
    $waypoint_labels = App\label::getWhere('waypoint_type');

    return view('test',[
        'journey_labels' => $journey_labels,
        'waypoint_labels' => $waypoint_labels,
    ]);
});

Route::prefix('api')->group(function(){
    
    /**
     * Posting New Journey
     */
    Route::post('/newjourney','journeyController@store');

    /**
     * Upload Image File
     */
    Route::post('/media/upload','mediaController@store');

    /**
     * Upload GPX file to Journey
     */
    Route::post('/journey/{id}/gpxupload','gpxController@store');

});