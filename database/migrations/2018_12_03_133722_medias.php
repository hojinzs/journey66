<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Medias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('where')
                ->nullable()
                ->comment('target model, table name');
            $table->unsignedInteger('index')
                ->nullable()
                ->comment('index number of target model');
            $table->string('name');
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
        Schema::dropIfExists('medias');
    }
}
