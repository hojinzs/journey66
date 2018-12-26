<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JourneyWaypointChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // change journeys table
        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage VARCHAR(255) NOT NULL;');

        Schema::table('journeys',function(Blueprint $table){
            $table->string('file_path')
                ->nullable($value = true)
                ->change();
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');
        
        // change waypoint table
        Schema::table('waypoints',function(Blueprint $table){
            $table->string('name')
                ->nullable($value = true)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //undo journey table
        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage VARCHAR(255) NOT NULL;');

        Schema::table('journeys',function(Blueprint $table){
            $table->string('file_path')
                ->nullable($value = false)
                ->change();
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');

        //undo waypoint table
        Schema::table('waypoints',function(Blueprint $table){
            $table->string('name')
            ->nullable($value = false)
            ->change();
        });
    }
}
