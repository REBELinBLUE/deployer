<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddCommitterEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->string('committer_email')->after('committer');
        });

        DB::table('deployments')->update([
            'committer_email' => 'none@example.com',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('committer_email');
        });
    }
}
