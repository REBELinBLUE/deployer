<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\ServerLog;

class CreateServerLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('server_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('server_id');
            $table->unsignedInteger('deploy_step_id');
            $table->enum('status', [ServerLog::PENDING, ServerLog::RUNNING,
                                    ServerLog::FAILED, ServerLog::CANCELLED,
                                    ServerLog::COMPLETED, ])->default(ServerLog::PENDING);
            $table->text('script')->nullable();
            $table->text('output')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
            $table->foreign('server_id')->references('id')->on('servers');
            $table->foreign('deploy_step_id')->references('id')->on('deploy_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('server_logs');
    }
}
