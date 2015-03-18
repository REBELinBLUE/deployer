<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeployLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deploy_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('deployment_id');
			$table->string('stage');
			$table->unsignedInteger('command_id')->nullable();
			$table->text('output')->nullable();
			$table->enum('status', ['Pending', 'Running', 'Failed', 'Cancelled', 'Completed'])->default('Pending');
			$table->timestamps();
			$table->foreign('deployment_id')->references('id')->on('deployment');
			$table->foreign('command_id')->references('id')->on('commands');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('deploy_logs');
	}

}
