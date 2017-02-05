<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('groups')->insert([
            'name' => 'Projects',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('groups');
    }
}
