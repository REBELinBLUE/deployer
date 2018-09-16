<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlMatchColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::table('check_urls', function (Blueprint $table) {
            $table->string('match')->after('period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::table('check_urls', function (Blueprint $table) {
            $table->dropColumn('match');
        });
    }
}
