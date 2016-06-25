<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DropScriptColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('server_logs', function (Blueprint $table) {
            $table->dropColumn('script');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('server_logs', function (Blueprint $table) {
            $table->text('script')->nullable();
        });
    }
}
