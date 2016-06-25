<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsCommandDefaultOn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->boolean('default_on')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn('default_on');
        });
    }
}
