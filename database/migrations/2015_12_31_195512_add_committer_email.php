<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddCommitterEmail extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // SQLite doesn't allow you to add columns which allow null values - http://bit.ly/1NXf2f1
        Schema::table('deployments', function (Blueprint $table) {
            $connection = config('database.default');
            if (config('database.connections.' . $connection . '.driver') === 'sqlite') {
                $table->string('committer_email')->default('none@example.com');
            } else {
                $table->string('committer_email')->after('committer');
            }
        });

        DB::table('deployments')->update([
            'committer_email' => 'none@example.com',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('committer_email');
        });
    }
}
