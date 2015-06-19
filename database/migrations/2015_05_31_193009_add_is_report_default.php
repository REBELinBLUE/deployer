<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsReportDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->boolean('is_report')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->boolean('is_report')->default(null)->change();
        });
    }
}
