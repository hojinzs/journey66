<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('where');
            $table->string('name');
            $table->longText('description');
            $table->string('icon')
                ->description('label icon from fontawesome')
                ->default('tag');
            $table->softDeletes('deleted_at');

            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        DB::table('labels')->insert([
            [
                'where' => 'journey_type',
                'name' => 'cycling',
                'description' => 'riding a bike',
                'icon' => 'bicycle',
            ],
            [
                'where' => 'journey_type',
                'name' => 'mtb',
                'description' => 'riding a mtb',
                'icon' => 'bicycle',
            ],
            [
                'where' => 'journey_type',
                'name' => 'motorbike',
                'description' => 'riding a motorbike',
                'icon' => 'motorcycle',
            ],
            [
                'where' => 'journey_type',
                'name' => 'smart mobi',
                'description' => 'riding a smartmobil. like segway, electric scooter etc... ',
                'icon' => 'bolt',
            ],
            [
                'where' => 'journey_type',
                'name' => 'hiking',
                'description' => 'walk away',
                'icon' => 'walking',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'landmark',
                'description' => 'landmark view point, nice vista etc...',
                'icon' => 'mountain',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'restaurant',
                'description' => 'restaurant',
                'icon' => 'utensils',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'supplypoint',
                'description' => 'supplypoint. convenience store, well, etc...',
                'icon' => 'store',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'rest',
                'description' => 'resting point',
                'icon' => 'bicycle',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'event',
                'description' => 'converstation etc..',
                'icon' => 'comments',
            ],
            [
                'where' => 'waypoint_type',
                'name' => 'marker',
                'description' => 'etc..',
                'icon' => 'map-marker',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labels');
    }
}
