<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\Command;

class CreateCommandsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('user');
            $table->text('script');
            $table->enum('step', [Command::BEFORE_CLONE, Command::AFTER_CLONE,
                                  Command::BEFORE_INSTALL, Command::AFTER_INSTALL,
                                  Command::BEFORE_ACTIVATE, Command::AFTER_ACTIVATE,
                                  Command::BEFORE_PURGE, Command::AFTER_PURGE, ])->default(Command::AFTER_INSTALL);
            $table->unsignedInteger('order')->default('0');
            $table->timestamps();
            $table->softDeletes();

            // Needed so that the sqlite tests continue to run
            $connection = config('database.default');
            if (config('database.connections.' . $connection . '.driver') !== 'sqlite') {
                $table->unsignedInteger('project_id');
                $table->foreign('project_id')->references('id')->on('projects');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('commands');
    }
}
