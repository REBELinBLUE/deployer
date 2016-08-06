<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommandsServersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('command_server', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('command_id');
            $table->unsignedInteger('server_id');
            $table->foreign('command_id')->references('id')->on('commands');
            $table->foreign('server_id')->references('id')->on('servers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('command_server');
    }
}
