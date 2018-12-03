<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journeys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('description')
                ->nullable();
            $table->string('type')
                ->comment('Journey style. | Hiking | Cycling | MTB | Motor | SmartMobil | ...');
            $table->enum('publish_stage',['Pending','Published','Private'])
                ->default('Pending')
                ->comment('Stage for publish the Journey-Log, Pending::Waiting for Publish, Published::Published on web, Private::Trun Private JourneyLog or Deleted');
            $table->string('author_name');
            $table->string('author_email');
            $table->string('key');
            $table->string('file_path')
                ->nullable()
                ->comment('gpx file uri');
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
        Schema::dropIfExists('journeys');
    }
}
