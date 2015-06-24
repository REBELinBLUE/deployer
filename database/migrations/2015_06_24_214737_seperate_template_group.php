<?php

use App\Group;
use Illuminate\Database\Migrations\Migration;

class SeperateTemplateGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $group = Group::findOrFail(1);

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
     *
     * @return void
     */
    public function down()
    {
        $group = Group::findOrFail(1);
        $group->delete();
    }
}
