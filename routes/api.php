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

/**
 * Posting New Journey
 */
Route::post('/newjourney','journeyController@store');

/**
 * Upload Tmp Image File
 */
Route::post('/imageuploader','ImageUploader@store');

/**
 * Set Waypoint Image File
 */
Route::post('/setwaypointimg','WaypointImageController@store');

Route::post('/gpx/{id}','GpxController@show');