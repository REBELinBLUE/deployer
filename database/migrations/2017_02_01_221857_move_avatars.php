<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\User;

class MoveAvatars extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        User::withTrashed()->chunk(100, function (Collection $users) {
            foreach ($users as $user) {
                $user->avatar = str_replace('/uploads/', '/storage/', $user->avatar);
                $user->save();
            }
        });

        $fs = new Filesystem();
        if ($fs->exists(public_path('uploads'))) {
            foreach ($fs->directories(public_path('uploads')) as $dir) {
                $fs->moveDirectory($dir, public_path('storage/' . $fs->basename($dir)));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        User::withTrashed()->chunk(100, function (Collection $users) {
            foreach ($users as $user) {
                $user->avatar = str_replace('/storage/', '/uploads/', $user->avatar);
                $user->save();
            }
        });

        $fs = new Filesystem();
        if ($fs->exists(storage_path('public'))) {
            foreach ($fs->directories(storage_path('public')) as $dir) {
                $fs->moveDirectory($dir, public_path('uploads/' . $fs->basename($dir)));
            }
        }
    }
}
