<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/journey/{id}','journeyController@get');

/**
 * journey write/edit API
 */
Route::post('/newjourney','journeyController@store'); // Posting New journey
Route::post('/editjourney/{id}','journeyController@update'); // Update journey
Route::delete('/deletejourney/{id}','journeyController@destroy'); // Delete journey temponary

/**
 * waypoint CRUD API
 */
Route::delete('/waypoint/{id}/delete','WaypointController@destroy'); // Delete journey temponary

/**
 * Upload Tmp Image File
 */
Route::post('/imageuploader','ImageUploader@store');

/**
 * Gpx Controller Routing
 */

Route::post('/gpx/{id}','GpxController@show');

Route::post('/gpxupload','GpxController@store');


/**
 * Test Controller
 */
Route::post('/test/{id}','TestController@api');