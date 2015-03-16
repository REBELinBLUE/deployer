<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('repository');
			$table->string('branch')->default('master');
			$table->text('private_key');
			$table->text('public_key');
			$table->string('url');
			$table->string('build_url');
			$table->enum('status', ['Finished', 'Deploying', 'Failed', 'Not Deployed'])->default('Not Deployed');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('projects');
	}
}
