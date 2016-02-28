<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Permission;
use REBELinBLUE\Deployer\Role;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\Project;

class PopulatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $root = Role::create([
            'name'  => 'root',
            'label' => 'Super Administrator',
        ]);

        $user = User::findOrFail(1);
        $user->assignRole($root);

        Role::create([
            'name'  => 'user',
            'label' => 'User',
        ]);

        //////////////////////////////
        //
        // Administration permissions
        //
        //////////////////////////////

        //////////////////////////////
        //
        // Project permissions
        //
        //////////////////////////////

        Permission::create([
            'name'  => 'projects.all.view',
            'label' => 'View all projects',
        ]);

        $projects = Project::notTemplates()
                           ->get()
                           ->lists('id');

        foreach ($projects as $project) {
            Permission::create([
                'name'  => 'projects.' . $project . '.view'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->delete();
        DB::table('roles')->delete();
    }
}
