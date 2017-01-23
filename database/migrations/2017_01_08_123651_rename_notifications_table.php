<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::rename('notifications', 'channels');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::rename('channels', 'notifications');
    }
}
