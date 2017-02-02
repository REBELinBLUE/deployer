<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\ConfigFile;

class ConfigFileTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('config_files')->delete();

        ConfigFile::create([
            'name'        => 'Configuration',
            'path'        => '.env',
            'target_type' => 'project',
            'target_id'   => 1,
            'content'     => 'APP_ENV=local
APP_DEBUG=true
APP_KEY=KkaOy5AZuzQ8ILAs6EwEYnK4VZVZJvNT
APP_URL=http://deployer.app
APP_TIMEZONE=UTC
APP_LOCALE=en
APP_THEME=green
APP_LOG=daily
APP_LOG_LEVEL=error

JWT_SECRET=zLBooByVMcfVWJYaSEKr7iKHIMluVBAl

SOCKET_URL=http://deployer.app
SOCKET_PORT=6001

DB_CONNECTION=mysql
DB_PORT=3306
DB_HOST=localhost
DB_DATABASE=deployer
DB_USERNAME=deployer
DB_PASSWORD=secret

MAIL_DRIVER=mail
MAIL_FROM_NAME=Deployer
MAIL_FROM_ADDRESS=deployer@deployer.app

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

QUEUE_DRIVER=beanstalkd
QUEUE_HOST=localhost

CACHE_DRIVER=file
SESSION_DRIVER=file
IMAGE_DRIVER=gd
',
        ]);
    }
}
