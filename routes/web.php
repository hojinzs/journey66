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

    // temponaly feature
    $shuffle = \App\Http\Controllers\journeyController::ShufflingActivate();

    return view('home',[
        'shuffle' => $shuffle,
    ]);
});

Route::get('/write','journeyController@create');

Route::get('/journey_shuffle','journeyController@showRandom');

Route::get('/journey/{id}','journeyController@show');

Route::get('/journey/{id}/edit','journeyController@getEditAuth');

Route::get('/journey/{id}/editor','journeyController@edit');

Route::get('/test','TestController@web');


/**
 * Referrence Pages
 */
Route::get('/ref/imgeo-js',function(){
    return view('ref.imgeo_js');
});
Route::get('/ref/php-gpx',function(){
    return view('ref.php-gpx');
});
Route::get('/ref/animate-js',function(){
    return view('ref.animate_js');
});