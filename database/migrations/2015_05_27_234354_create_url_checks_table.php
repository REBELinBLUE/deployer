<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Create application' health url table
 */
class CreateUrlChecksTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_checks', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title');
            $table->string('url');
            $table->unsignedInteger('project_id');
            $table->integer('period');
            $table->boolean('is_report'); // Report the user when it's check failed
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
        Schema::drop('url_checks');
    }
}
