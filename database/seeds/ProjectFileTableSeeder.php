<?php

use Illuminate\Database\Seeder;
use REBELinBLUE\Deployer\ProjectFile;

class ProjectFileTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('project_files')->delete();

        ProjectFile::create([
            'name' => 'Configuration',
            'path' => '.env',
            'content' => 'APP_ENV=local
APP_DEBUG=true
APP_KEY=KkaOy5AZuzQ8ILAs6EwEYnK4VZVZJvNT
APP_URL=http://deploy.app
APP_TIMEZONE=UTC
APP_LOCALE=en
APP_THEME=green
APP_LOG=daily

JWT_SECRET=zLBooByVMcfVWJYaSEKr7iKHIMluVBAl

SOCKET_URL=http://deploy.app
SOCKET_PORT=6001

DB_TYPE=mysql
DB_HOST=localhost
DB_DATABASE=deployer
DB_USERNAME=homestead
DB_PASSWORD=secret

MAIL_DRIVER=mail
MAIL_FROM_NAME=Deployer
MAIL_FROM_ADDRESS=deployer@deploy.app

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

QUEUE_DRIVER=beanstalkd
QUEUE_HOST=localhost

CACHE_DRIVER=file
SESSION_DRIVER=file
IMAGE_DRIVER=gd
',
            'project_id' => 1,
        ]);
    }
}
