<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Create table project_files.
 */
class CreateProjectFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('path');
            $table->text('content');
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
        Schema::drop('project_files');
    }
}
