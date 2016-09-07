<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Template;

class AddTargetableAttributes extends Migration
{
    private $relations = ['Command', 'Variable', 'ProjectFile', 'SharedFile'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->relations as $relation) {
            $className = "REBELinBLUE\\Deployer\\$relation";
            $instance = new $className;

            $table = $instance->getTable();

            // Add the target fields to the tables
            Schema::table($table, function (Blueprint $table) {
                $table->integer('target_id')->nullable();
                $table->string('target_type')->nullable();
            });

            if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] !== 'sqlite') {
                $drop = $_ENV['DB_TYPE'] === 'mysql' ? 'FOREIGN KEY' : 'CONSTRAINT';

                DB::statement("ALTER TABLE {$table} DROP {$drop} {$table}_project_id_foreign");
            }
        }

        // Now find the existing templates in the project template and move them to templates table
        $templates = [];
        foreach (Project::where('is_template', true)->get() as $project) {
            $data = $project->toArray();
            unset($data['id']);

            $templates[$project->id] = Template::create($data);
        }

        // Now loop through the relations and set the target details
        foreach ($this->relations as $relation) {
            $className = "REBELinBLUE\\Deployer\\$relation";
            $instance = new $className;

            foreach ($instance->all() as $row) {
                $row->target_id = $row->project_id;
                $row->target_type = 'project';

                if (isset($templates[$row->project_id])) {
                    $row->target_id = $templates[$row->project_id]->id;
                    $row->target_type = 'template';
                }

                $row->save();
            }
        };

        // Remove any deleted non templates from group 1 to group 2
        $project = new Project;
        $project->where('is_template', false)
                ->where('group_id', 1)
                ->withTrashed()
                ->update(['group_id' => 2]);

        // Remove the left over fake templates and the containing group
        Project::where('is_template', true)->forceDelete();
        Group::find(1)->forceDelete();

        // Remove the unneeded project ID column
        foreach ($this->relations as $relation) {
            $className = "REBELinBLUE\\Deployer\\$relation";
            $instance = new $className;

            $table = $instance->getTable();

            DB::statement("ALTER TABLE {$table} DROP COLUMN project_id");

            if ($_ENV['DB_TYPE'] === 'mysql') {
                DB::statement("ALTER TABLE {$table} MODIFY target_id INT(11) NOT NULL");
                DB::statement("ALTER TABLE {$table} MODIFY target_type VARCHAR(255) NOT NULL");
            } elseif ($_ENV['DB_TYPE'] === 'pgsql') {
                DB::statement("ALTER TABLE {$table} ALTER COLUMN target_id SET NOT NULL");
                DB::statement("ALTER TABLE {$table} ALTER COLUMN target_type SET NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->relations as $relation) {
            $className = "REBELinBLUE\\Deployer\\$relation";
            $instance = new $className;

            $table = $instance->getTable();

            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('target_id');
                $table->dropColumn('target_type');
            });
        }
    }
}
