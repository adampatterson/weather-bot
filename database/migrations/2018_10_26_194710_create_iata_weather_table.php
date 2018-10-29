<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIataWeatherTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iata_weather', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->timestamps();

            $table->string('key')->index();
            $table->string('name');
            $table->string('city');
            $table->string('country');
            $table->string('iata');
            $table->string('icao');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('altitude');
            $table->string('timezone');
            $table->string('dst');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iata_weather');
    }
}
