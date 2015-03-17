<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('ip_address');
			$table->string('user');
			$table->string('path');
			$table->integer('project_id')->unsigned();
			$table->enum('status', ['Successful', 'Testing', 'Failed', 'Untested'])->default('Untested');
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
		Schema::drop('servers');
	}

}
