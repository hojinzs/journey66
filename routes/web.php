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

Route::get('/journey/{id}','journeyController@show');

Route::get('/test', function () {

    $journey_labels = App\label::getWhere('journey_type');
    $waypoint_labels = App\label::getWhere('waypoint_type');

    return view('test',[
        'journey_labels' => $journey_labels,
        'waypoint_labels' => $waypoint_labels,
    ]);
});