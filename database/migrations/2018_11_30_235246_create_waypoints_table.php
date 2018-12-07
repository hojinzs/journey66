<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaypointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waypoints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('UWID')
                ->unique()
                ->description('hashed waypoint id. using for uri, file input');
            $table->unsignedInteger('journey_id');
            $table->foreign('journey_id')
                ->references('id')->on('journeys')
                ->onDelete('cascade');
            $table->integer('sequence');
            $table->string('name');
            $table->longText('description')
                ->nullable();
            $table->string('type')
                ->comment('Waypoint type. | milestone | landmark | restaurant | supplypoint | rest | ...');
            $table->double('latitude');
            $table->double('longitude');
            $table->softDeletes('deleted_at');

            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waypoints',function(Blueprint $table){
            $table->dropForeign('waypoints_journey_id_foreign');
        });
        Schema::dropIfExists('waypoints');
    }
}
