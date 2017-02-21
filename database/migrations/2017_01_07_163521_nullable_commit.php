<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableCommit extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('committer')->nullable()->change();
            $table->string('commit')->nullable()->change();
            $table->string('committer_email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('committer')->nullable(false)->change();
            $table->string('commit')->nullable(false)->change();
            $table->string('committer_email')->nullable(false)->change();
        });
    }
}
