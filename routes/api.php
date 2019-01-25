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
 * journey write/edit API
 */

Route::post('/journeynew','journeyController@store'); // Posting New journey
Route::get('/journey/{id}','journeyController@get'); // Get journey data ajax
Route::post('/journey/{id}/edit','journeyController@update'); // Update journey
Route::delete('/journey/{id}/edit','journeyController@destroy'); // Delete journey temponary


/**
 * waypoint CRUD API
 */
Route::delete('/waypoint/{id}/delete','WaypointController@destroy'); // Delete waypoint temponary
Route::delete('/waypoint/{id}/image/{num}/delete','WaypointController@destroy'); // Delete waypoint temponary

/**
 * image file CRUD API
 */
Route::post('/image/upload','ImageController@store');


/**
 * Gpx Controller Routing
 */
Route::post('/gpx/{id}','GpxController@show');
Route::post('/gpxupload','GpxController@store');


/**
 * Test Controller
 */
Route::post('/test/{id}','TestController@api');