<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JourneySectionToMetadatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** migrate :: journey_metas -> metas */
        $journey_metas = DB::table('journey_metas')->get();
        foreach ($journey_metas as $journey_meta) {
            $journey = App\journey::where('id',$journey_meta->journey_id)->first();
            $journey->metas()->create([
                'name' => $journey_meta->meta_name,
                'value' => $journey_meta->value,
            ]);
        }

        /** label :: waypoint type add */
        DB::table('labels')->insert([
            [
                'where' => 'waypoint_type',
                'name' => 'accident',
                'description' => 'accident',
                'icon' => 'car-crash',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'starting',
                'description' => 'starting point',
                'icon' => 'play-circle',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'destination',
                'description' => 'destination',
                'icon' => 'checkered',
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
        DB::table('metas')->truncate();
    }
}
