<?php

use App\Heartbeat;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// TODO: Test this migration on mysql as it seems to be acting strangely
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
            $table->string('hash')->unique();
            $table->integer('interval');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('missed')->default(0);
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
