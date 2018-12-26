<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJourneyMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('journey_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('journey_id');
            $table->foreign('journey_id')
                ->references('id')->on('journeys')
                ->onDelete('cascade');
            $table->string('meta_name');
            $table->string('value');

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
        Schema::table('journey_metas',function(Bluepront $table){
            $table->dropForeign('journey_metas_journey_id_foreign');
        });
        Schema::dropIfExists('journey_metas');
    }
}
