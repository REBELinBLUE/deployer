<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Environment;
use REBELinBLUE\Deployer\Server;

class AddServerEnvironment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->unsignedInteger('environment_id')->nullable();
            $table->foreign('environment_id')->references('id')->on('environments');
        });

        Project::withTrashed()->chunk(100, function (Collection $projects) {
            foreach ($projects as $project) {
                $environment = Environment::create([
                    'name' => 'Default',
                    'description' => 'Default',
                    'project_id' => $project->id,
                ]);

                foreach (Server::where('project_id', $project->id)->withTrashed()->get() as $server) {
                    $server->environment_id = $environment->id;
                    $server->save();
                }
            }
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->unsignedInteger('environment_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('environment_id');
        });
    }
}
