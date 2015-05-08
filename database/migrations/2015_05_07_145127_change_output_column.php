<?php

use App\ServerLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// TODO: Test this migration on mysql as it seems to be acting strangely
class ChangeOutputColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_logs', function (Blueprint $table) {
            $table->longtext('new_output')->nullable();
        });

        foreach(ServerLog::all() as $log) {
            $log->new_output = $log->output;
            $log->save();
        }

        Schema::table('server_logs', function (Blueprint $table) {
            $table->dropColumn('output');
        });

        Schema::table('server_logs', function (Blueprint $table) {
            $table->renameColumn('new_output', 'output');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('server_logs', function (Blueprint $table) {
            $table->text('new_output')->nullable();
        });

        foreach(ServerLog::all() as $log) {
            $log->new_output = $log->output;
            $log->save();
        }

        Schema::table('server_logs', function (Blueprint $table) {
            $table->dropColumn('output');
        });

        Schema::table('server_logs', function (Blueprint $table) {
            $table->renameColumn('new_output', 'output');
        });
    }
}
