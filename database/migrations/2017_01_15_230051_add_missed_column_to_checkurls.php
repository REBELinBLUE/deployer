<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissedColumnToCheckurls extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->unsignedInteger('missed')->default(0);
            $table->dateTime('last_seen')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->dropColumn(['missed', 'last_seen']);
        });
    }
}
