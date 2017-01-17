# Deployer

[![StyleCI](https://styleci.io/repos/33559148/shield?style=flat-square&branch=master)](https://styleci.io/repos/33559148)
[![Build Status](https://img.shields.io/travis/REBELinBLUE/deployer/master.svg?style=flat-square)](https://travis-ci.org/REBELinBLUE/deployer)
[![Code Coverage](https://img.shields.io/codecov/c/github/REBELinBLUE/deployer/master.svg?style=flat-square)](https://codecov.io/gh/REBELinBLUE/deployer)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/686dd98b-c0e5-465b-8f14-29b1cab47f3b.svg?style=flat-square)](https://insight.sensiolabs.com/projects/686dd98b-c0e5-465b-8f14-29b1cab47f3b)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](/LICENSE.md)
[![Laravel Version](https://shield.with.social/cc/github/REBELinBLUE/deployer/master.svg?style=flat-square)](https://packagist.org/packages/laravel/framework)
[![Latest Version](https://img.shields.io/github/release/REBELinBLUE/deployer.svg?style=flat-square)](https://github.com/REBELinBLUE/deployer/releases)
[![StackShare](https://img.shields.io/badge/tech-stack-0690fa.svg?style=flat)](https://stackshare.io/REBELinBLUE/deployer)
[![Gitter](https://img.shields.io/badge/chat-on%20gitter-brightgreen.svg?style=flat-square)](https://gitter.im/REBELinBLUE/deployer)

Deployer is a PHP Application deployment system powered by [Laravel 5.3](http://laravel.com), written & maintained by [Stephen Ball](https://github.com/REBELinBLUE).

Check out the [releases](https://github.com/REBELinBLUE/deployer/releases), [license](/LICENSE.md), [screenshots](/SCREENSHOTS.md), and [contribution guidelines](/.github/CONTRIBUTING.md).

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

### Common Error

If you see an error like the following in the logs

```
'ErrorException' with message 'file_get_contents(/var/www/deployer/public/build/rev-manifest.json): failed to open stream: No such file or directory' in /var/www/deployer/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php:343
```

or the following on the page

```
ErrorException (E_ERROR) file_get_contents(/var/www/deployer/public/build/rev-manifest.json): failed to open stream: No such file or directory
```

it means you are not using a production build. You either need to checkout the `release` branch or a specific release, or install the additional development dependencies

```shell
$ composer install
$ npm install (or yarn install)
```

and then build the assets

```shell
$ gulp
```

## Requirements

- [PHP](http://www.php.net) 5.6.4+ or newer
- A database, either [MySQL](https://www.mysql.com) or [PostgreSQL](http://www.postgresql.org) ([SQLite](https://www.sqlite.org) can also be used but is not recommended).
- [Composer](https://getcomposer.org)
- [Redis](http://redis.io)
- [Node.js](https://nodejs.org/)
- [Beanstalkd](http://kr.github.io/beanstalkd/) for queuing jobs. Although Laravel can use redis there is currently [an issue when using more than 1 queue worker](https://github.com/laravel/framework/issues/8577), the default in Deployer is 3.

### Optional extras

- [Supervisor](http://supervisord.org) to keep the queue listener and Node.js socket server running
- A [caching server](http://laravel.com/docs/5.2/cache), unless you expect a lot of traffic the default `file` cache is probably enough
- [Yarn](https://yarnpkg.com/) an improved package manager for Node.js

## Installation

1. Clone the repository

```shell
$ git clone https://github.com/REBELinBLUE/deployer.git
```

2. Checkout the latest release

```shell
$ git checkout 0.0.40
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

7. Configure your web server to point to `public/`, see `examples/` for Apache and nginx sample configuration files. If you use Apache you will need to ensure you have the `mod_rewrite` module enabled.

8. Start socket server and setup cron jobs.
    If you are not configuring `supervisor` you will need to manually start the socket server with `node socket.js` (listens on port 6001 by default) and setup cron jobs, see `examples/crontab`.
    If you are configuring `supervisor` see `examples/supervisor.conf`

### Updating

1. Get the latest code

```shell
$ git fetch --all
$ git checkout 0.0.40
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

Deployer is licensed under [The MIT License (MIT)](/LICENSE.md).
