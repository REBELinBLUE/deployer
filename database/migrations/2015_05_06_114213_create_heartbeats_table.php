<?php

use App\Heartbeat;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeartbeatsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('hash');
            $table->integer('interval');
            $table->unsignedInteger('project_id');
            $table->enum('status', [Heartbeat::OK, Heartbeat::UNTESTED,
                                    Heartbeat::MISSING])->default(Heartbeat::UNTESTED);
            $table->dateTime('last_activity')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('heartbeats');
    }

}
