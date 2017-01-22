<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCheckurlsIsReportColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->dropColumn('is_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->boolean('is_report')->default(true);
        });
    }
}
