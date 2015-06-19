<?php

use App\Group;
use App\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('repository');
            $table->string('hash');
            $table->string('branch')->default('master');
            $table->text('private_key');
            $table->text('public_key');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('builds_to_keep')->default(10);
            $table->string('url')->nullable();
            $table->string('build_url')->nullable();
            $table->enum('status', [Project::FINISHED, Project::PENDING,
                                    Project::DEPLOYING, Project::FAILED,
                                    Project::NOT_DEPLOYED, ])->default(Project::NOT_DEPLOYED);
            $table->dateTime('last_run')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('group_id')->references('id')->on('groups');
        });

        // Had to move this from the previous migration due to group having an attribute for project count
        Group::create([
            'name' => 'Projects',
        ]);
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
