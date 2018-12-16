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

Route::get('/write','journeyController@create');

Route::get('/journey/{id}','journeyController@show');

Route::get('/journey/{id}/edit','journeyController@edit');

Route::get('/test', function () {

    $journey_labels = App\label::getWhere('journey_type');
    $waypoint_labels = App\label::getWhere('waypoint_type');

    return view('test',[
        'journey_labels' => $journey_labels,
        'waypoint_labels' => $waypoint_labels,
    ]);
});