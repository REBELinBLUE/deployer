<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddIsWebhook extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->boolean('is_webhook')->default(false);
        });

        DB::table('deployments')->whereRaw('user_id IS NULL')
                                ->update(['is_webhook' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('is_webhook');
        });
    }
}
