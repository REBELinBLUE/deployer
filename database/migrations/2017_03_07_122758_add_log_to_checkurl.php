<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogToCheckurl extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->text('last_log')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->dropColumn('last_log');
        });
    }
}
