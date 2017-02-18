<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVariablesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('variables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('value');
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
        Schema::drop('variables');
    }
}
