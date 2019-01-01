<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JourneyPolylinePathAdd extends Migration
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
            $table->string('polyline_path')
                ->nullable($value = true)
                ->unique()
                ->comment('encoded polyline string file stored uri');
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');
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
            $table->dropColumn('polyline_path');
        });

        DB::statement('ALTER TABLE journeys CHANGE COLUMN publish_stage publish_stage ENUM("Pending","Published","Private") NOT NULL DEFAULT "Pending";');
    }
}
