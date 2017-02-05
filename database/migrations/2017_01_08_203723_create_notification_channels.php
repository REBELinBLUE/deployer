<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNotificationChannels extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::rename('channels', 'slack');

        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->text('config');
            $table->boolean('on_deployment_success')->default(false);
            $table->boolean('on_deployment_failure')->default(false);
            $table->boolean('on_link_down')->default(false);
            $table->boolean('on_link_still_down')->default(false);
            $table->boolean('on_link_recovered')->default(false);
            $table->boolean('on_heartbeat_missing')->default(false);
            $table->boolean('on_heartbeat_still_missing')->default(false);
            $table->boolean('on_heartbeat_recovered')->default(false);
            $table->unsignedInteger('project_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        DB::table('notify_emails')->orderBy('id')->chunk(100, function ($rows) {
            $channels = [];
            foreach ($rows as $row) {
                $channels[] = $this->channelData($row, [
                    'email' => $row->email,
                ], 'mail');
            }

            DB::table('channels')->insert($channels);
        });

        DB::table('slack')->orderBy('id')->chunk(100, function ($rows) {
            $channels = [];
            foreach ($rows as $row) {
                $channels[] = $this->channelData($row, [
                    'webhook' => $row->webhook,
                    'channel' => $row->channel,
                    'icon'    => empty($row->icon) ? null : $row->icon,
                ], 'slack');
            }

            DB::table('channels')->insert($channels);
        });

        Schema::drop('slack');
        Schema::drop('notify_emails');
    }

    /**
     * Reverse the migrations
     * WARNING data will be lost as this does not convert back.
     */
    public function down()
    {
        Schema::drop('channels');
        Schema::create('notify_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('email');
            $table->unsignedInteger('project_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('webhook');
            $table->string('channel');
            $table->string('icon')->nullable();
            $table->unsignedInteger('project_id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('failure_only')->default(false);
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    private function channelData(stdClass $row, array $config, $type)
    {
        $is_slack = ($type === 'slack');

        $on_success = true;
        $on_failure = $is_slack;

        if ($type === 'slack' && $row->failure_only === 0) {
            $on_success = false;
        }

        return [
            'project_id'                 => $row->project_id,
            'type'                       => $type,
            'name'                       => $row->name,
            'config'                     => json_encode($config),
            'created_at'                 => $row->created_at,
            'deleted_at'                 => $row->deleted_at,
            'updated_at'                 => $row->updated_at,
            // Set the values to maintain previous behaviour
            'on_deployment_success'      => $on_success,
            'on_deployment_failure'      => $on_failure,
            'on_link_down'               => $is_slack,
            'on_link_still_down'         => false,
            'on_link_recovered'          => false,
            'on_heartbeat_missing'       => $is_slack,
            'on_heartbeat_still_missing' => false,
            'on_heartbeat_recovered'     => $is_slack,
        ];
    }
}
