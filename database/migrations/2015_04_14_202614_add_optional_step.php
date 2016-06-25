<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOptionalStep extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->boolean('deploy_code')->default(true);
        });

        Schema::table('commands', function (Blueprint $table) {
            $table->boolean('optional')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('deploy_code');
        });

        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn('optional');
        });
    }
}
