<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('user');
            $table->text('script');
            $table->unsignedInteger('project_id'); // FIXME: Turn these into constants
            $table->enum('step', ['Before Clone', 'After Clone', 'Before Install', 'After Install', 'Before Activate', 'After Activate', 'Before Purge', 'After Purge'])->default('After Install');
            $table->unsignedInteger('order')->default('0');
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
        Schema::drop('commands');
    }
}
