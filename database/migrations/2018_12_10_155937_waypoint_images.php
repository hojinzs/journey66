<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WaypointImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('medias');
        
        Schema::create('waypoint_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('waypoint_id');
            $table->foreign('waypoint_id')
                ->references('id')->on('waypoints')
                ->onDelete('cascade');
            $table->integer('number');
            $table->string('type');
            $table->string('path')
                ->comment('file uri');

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
        //
        Schema::table('waypoint_images',function(Blueprint $table){
            $table->dropForeign('waypoint_images_waypoint_id_foreign');
        });
        Schema::dropIfExists('waypoint_images');
    }
}
