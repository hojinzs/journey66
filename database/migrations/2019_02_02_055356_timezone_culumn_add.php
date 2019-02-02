<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TimezoneCulumnAdd extends Migration
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
            $table->double('started_lat')
                ->nullable()
                ->description('startind locaton latitute');
            $table->double('started_lng')
                ->nullable()
                ->description('startind locaton longitude');
            $table->string('started_timezone')
                ->nullable()
                ->description('starting location timezone');
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');

        Schema::table('waypoints',function(Blueprint $table){
            $table->string('timezone')
                ->nullable()
                ->description('waypoint location timezone');
        });

        
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
            $table->dropColumn(['started_lat','started_lng','started_timezone']);
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');

        Schema::table('waypoints',function(Blueprint $table){
            $table->dropColumn(['timezone']);
        });
    }
}
