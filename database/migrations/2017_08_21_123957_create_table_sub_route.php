<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSubRoute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_route', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('origin_name');
            $table->string('destination_name');

            $table->string('origin_lat');
            $table->string('origin_lng');
            $table->string('destination_lat');
            $table->string('destination_lng');

            $table->string('ordering');
            $table->string('main_route');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_route');
    }
}
