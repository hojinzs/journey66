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
Route::post('/img/tmp','imageupload@store');

/**
 * Upload Image File
 */
Route::post('/media/upload','mediaController@store');

/**
 * Upload GPX file to Journey
 */
Route::post('/journey/{id}/gpxupload','gpxController@store');
