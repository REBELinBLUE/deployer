<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRefsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('refs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_tag')->default(false);
            $table->unsignedInteger('project_id');
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('refs');
    }
}
