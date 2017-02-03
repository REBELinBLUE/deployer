<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWebhookSourceDetails extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('source')->nullable();
            $table->string('build_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn(['source', 'build_url']);
        });
    }
}
