<?php

use App\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_commands', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->text('user');
            $table->text('script');
            $table->unsignedInteger('template_id');
            $table->enum('step', [Command::BEFORE_CLONE, Command::AFTER_CLONE,
                                  Command::BEFORE_INSTALL, Command::AFTER_INSTALL,
                                  Command::BEFORE_ACTIVATE, Command::AFTER_ACTIVATE,
                                  Command::BEFORE_PURGE, Command::AFTER_PURGE])->default(Command::AFTER_INSTALL);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('template_id')->references('id')->on('templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('template_commands');
    }
}
