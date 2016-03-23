<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use REBELinBLUE\Deployer\Notification;

class AddHipchatNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (isset($_ENV['DB_TYPE']) && $_ENV['DB_TYPE'] !== 'sqlite') {
            DB::statement("ALTER TABLE notifications ADD COLUMN service ENUM('"
                . Notification::SLACK . "', '"
                . Notification::HIPCHAT . "', '"
                . Notification::GITTER . "') NOT NULL DEFAULT '" . Notification::SLACK . "'");
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                $table->enum('service', [Notification::SLACK, Notification::HIPCHAT, Notification::GITTER])->default(Notification::SLACK);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('service');
        });
    }
}
