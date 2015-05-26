<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * create table to store the emails to be notified
 */
class CreateNotifyEmailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notify_emails', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('email');
			$table->unsignedInteger('project_id');
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
		Schema::drop('notify_emails');
	}

}
