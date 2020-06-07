<?php

use Illuminate\Database\Migrations\Migration;
use REBELinBLUE\Deployer\Channel;

class RemoveHipchat extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Channel::where('type', '=', 'hipchat')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
    }
}
