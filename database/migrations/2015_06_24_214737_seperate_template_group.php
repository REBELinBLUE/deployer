<?php

use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Group;

class SeperateTemplateGroup extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $group = Group::find(1);

        // Had to move this from the previous migration due to
        // an issue caused by adding the Broadcast events later on
        // with an attribute which depends on a column added later
        // But the migration still needs to work for people who were
        // an older version
        if (!$group) {
            $group = Group::create([
                'name' => 'Projects',
            ]);
        }

        $new_group = Group::create([
            'name' => $group->name,
        ]);

        foreach ($group->projects as $project) {
            $project->group_id = $new_group->id;
            $project->save();
        }

        $group->name = 'Templates';
        $group->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $group = Group::findOrFail(1);
        $group->delete();
    }
}
