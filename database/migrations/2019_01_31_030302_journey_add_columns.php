<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JourneyAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add columns journeys table
        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage VARCHAR(255) NOT NULL;');

        Schema::table('journeys',function(Blueprint $table){
            $table->double('distance')
                ->nullable()
                ->description('Distance in meters (m)');
            $table->double('elevation')
                ->nullable()
                ->description('Cumulative elevation gain in meters (m)');
            $table->string('duration')
                ->nullable()
                ->description('Duration is seconds');
            $table->timestamp('startedAt')
                ->nullable()
                ->description('Started time');
            $table->timestamp('finishedAt')
                ->nullable()
                ->description('Ending time');
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');

        // add columns waypoint table
        Schema::table('waypoints',function(Blueprint $table){
            $table->double('distance')
                ->nullable()
                ->description('Distance in meters (m)');
            $table->double('elevation')
                ->nullable()
                ->description('Cumulative elevation gain in meters (m)');
            $table->timestamp('time')
                ->nullable()
                ->description('Sequencing time');
        });

        // add sequence colums label table
        Schema::table('labels',function(Blueprint $table){
            $table->integer('seq')
                ->description('label ordering number');
        });

        // delete and add waypoint label data
        DB::table('labels')->delete();
        DB::table('labels')->insert([
            [
                'where' => 'journey_type',
                'name' => 'cycling',
                'seq' => 1,
                'description' => 'riding a bike',
                'icon' => 'bicycle',
            ],
            [
                'where' => 'journey_type',
                'name' => 'mtb',
                'seq' => 2,
                'description' => 'riding a mtb',
                'icon' => 'bicycle',
            ],
            [
                'where' => 'journey_type',
                'name' => 'motorbike',
                'seq' => 3,
                'description' => 'riding a motorbike',
                'icon' => 'motorcycle',
            ],
            [
                'where' => 'journey_type',
                'name' => 'smart mobi',
                'seq' => 4,
                'description' => 'riding a smartmobil. like segway, electric scooter etc... ',
                'icon' => 'bolt',
            ],
            [
                'where' => 'journey_type',
                'name' => 'hiking',
                'seq' => 5,
                'description' => 'walk away',
                'icon' => 'walking',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'starting',
                'seq' => 1,
                'description' => 'starting point',
                'icon' => 'flag',  
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'milestone',
                'seq' => 2,
                'description' => 'etc..',
                'icon' => 'map-marker',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'event',
                'seq' => 3,
                'description' => 'converstation etc..',
                'icon' => 'comments',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'landmark',
                'seq' => 4,
                'description' => 'landmark view point, nice vista etc...',
                'icon' => 'landmark',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'restaurant',
                'seq' => 5,
                'description' => 'restaurant',
                'icon' => 'utensils',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'supplypoint',
                'seq' => 6,
                'description' => 'supplypoint. convenience store, well, etc...',
                'icon' => 'charging-station',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'rest',
                'seq' => 7,
                'description' => 'resting point',
                'icon' => 'hot-tub',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'accident',
                'seq' => 8,
                'description' => 'accident',
                'icon' => 'car-crash',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'destination',
                'seq' => 9,
                'description' => 'destination',
                'icon' => 'flag-checkered',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage VARCHAR(255) NOT NULL;');

        Schema::table('journeys',function(Blueprint $table){
            $table->dropColumn('distance');
            $table->dropColumn('elevation');
            $table->dropColumn('duration');
            $table->dropColumn('startedAt');
            $table->dropColumn('finishedAt');
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');

        Schema::table('waypoints',function(Blueprint $table){
            $table->dropColumn('seq');
        });


    }
}
