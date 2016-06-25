<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Template;

class AddGroupOrdering extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0);
        });

        $groups = Group::where('id', '<>', Template::GROUP_ID)
                       ->orderBy('name')
                       ->get();

        $i = 0;
        foreach ($groups as $group) {
            $group->order = $i;
            $group->save();

            $i++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
