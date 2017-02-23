<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNotNullOnUserCommands extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->text('user')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->text('user')->nullable(false)->change();
        });
    }
}
