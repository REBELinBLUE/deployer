<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\Heartbeat;

class CreateHeartbeatsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('hash')->unique();
            $table->integer('interval');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('missed')->default(0);
            $table->enum('status', [Heartbeat::OK, Heartbeat::UNTESTED,
                                    Heartbeat::MISSING, ])->default(Heartbeat::UNTESTED);
            $table->dateTime('last_activity')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('heartbeats');
    }
}
