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
        Schema::create('check_urls', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title');
            $table->string('url');
            $table->unsignedInteger('project_id');
            $table->integer('period')->index();
            $table->boolean('is_report');
            $table->boolean('last_status')->nullable();
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
        Schema::drop('check_urls');
    }
}
