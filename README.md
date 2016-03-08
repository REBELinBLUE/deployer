# Deployer

Deployer is a PHP Application deployment system powered by [Laravel 5.2](http://laravel.com), written & maintained by [Stephen Ball](https://github.com/REBELinBLUE).

Check out the [releases](https://github.com/REBELinBLUE/deployer/releases), [license](/LICENSE.md), [screenshots](/SCREENSHOTS.md), and [contribution guidelines](/.github/CONTRIBUTING.md).

[![Gitter](https://img.shields.io/badge/chat-on%20gitter-green.svg)](https://gitter.im/REBELinBLUE/deployer)

**Current Build Status**

[![StyleCI](https://styleci.io/repos/33559148/shield?style=flat)](https://styleci.io/repos/33559148)
[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master&style=flat&label=PHPCI)](http://ci.rebelinblue.com/build-status/view/3?branch=master)
[![Code Climate](https://codeclimate.com/github/REBELinBLUE/deployer/badges/gpa.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![Test Coverage](https://codeclimate.com/github/REBELinBLUE/deployer/badges/coverage.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/686dd98b-c0e5-465b-8f14-29b1cab47f3b.svg)](https://insight.sensiolabs.com/projects/686dd98b-c0e5-465b-8f14-29b1cab47f3b)

**Release Status**

[![PHP Dependency Status](https://www.versioneye.com/user/projects/5531329410e7141211000f29/badge.svg)](https://www.versioneye.com/user/projects/5531329410e7141211000f29)
[![Node Dependency Status](https://www.versioneye.com/user/projects/5531329610e714f9e500109c/badge.svg)](https://www.versioneye.com/user/projects/5531329610e714f9e500109c)
[![Latest Version](https://img.shields.io/github/release/REBELinBLUE/deployer.svg)](https://github.com/REBELinBLUE/deployer/releases)

## What it does

* Deploys applications to multiple servers accessible via SSH
* Clones your project's git repository
* Installs composer dependencies
* Runs arbitrary bash commands
* Gracefully handles failure in any of these steps
* Keeps a number of previous deployments
* Monitors that cronjobs are running
* Allows deployments to be triggered via a webhook

## What it doesn't do

* Provision VMs
* Install system packages
* Configure the web server, database or other services
* [Run a test suite or check code quality](http://phptesting.org)

## Usage in production

The `master` branch of this repository is a development branch and **should not** be used in production. Changes are merged into the `release` branch when they are considered stable and may then be tagged for release at any time. It is recommended that you use the latest tag [release](https://github.com/REBELinBLUE/deployer/releases) for production. For information on contributing see [contribution guidelines](/.github/CONTRIBUTING.md).

## Requirements

- [PHP](http://www.php.net) 5.5.9+ or newer
- A database, either [MySQL](https://www.mysql.com) or [PostgreSQL](http://www.postgresql.org) are recommended but [SQLite](https://www.sqlite.org) can be used
- [Composer](https://getcomposer.org)
- [Redis](http://redis.io)
- [Node.js](https://nodejs.org/)
- A suitable [queue](http://laravel.com/docs/5.1/queues) for Laravel, [Beanstalkd](http://kr.github.io/beanstalkd/) is recommended but Redis can also be used

### Optional extras

- [Supervisor](http://supervisord.org) to keep the queue listener and Node.js socket server running
- A [caching server](http://laravel.com/docs/5.1/cache), unless you expect a lot of traffic the default `file` cache is probably enough

## Installation

1. Clone the repository

    ```shell
    $ git clone https://github.com/REBELinBLUE/deployer.git
    ```

2. Checkout the latest release

    ```shell
    $ git checkout 0.0.30
    ```

3. Install dependencies

    ```shell
    $ composer install -o --no-dev
    $ npm install --production
    ```

4. Make sure the storage and upload directories are writable

    ```shell
    $ chmod -R 777 storage
    $ chmod -R 777 public/upload
    ```

5. Run the installer and follow the instructions

    ```shell
    $ php artisan app:install
    ```

6. (Optional) Make any additional configuration changes

    ```shell
    $ editor .env
    ```

7. Configure your web server to point to `public/`, see `examples/` for Apache and nginx sample configuration files.

8. Start socket server and setup cron jobs.
    If you are not configuring `supervisor` you will need to manually start the socket
    server with `node socket.js` (listens on port 6001 by default) and setup cron jobs, see `examples/crontab`.
    If you are configuring `supervisor` see `examples/supervisor.conf`

### Updating

1. Get the latest code

    ```shell
    $ git fetch --all
    $ git checkout 0.0.30
     ```

2. Update the dependencies

    ```shell
    $ composer install -o --no-dev
    $ npm install --production
    ```

3. Run the updater

    ```shell
    $ php artisan app:update
    ```

## License

Deployer is licensed under [The MIT License (MIT)](LICENSE.md).
