# Deployer

Deployer is a PHP Application deployment system powered by [Laravel 5.1](http://laravel.com), written & maintained by [Stephen Ball](https://github.com/REBELinBLUE).

Check out the [releases](https://github.com/REBELinBLUE/deployer/releases), [license](LICENSE.md), [screenshots](SCREENSHOTS.md), and [contribution guidelines](CONTRIBUTING.md).

**Current Build Status**

[![StyleCI](https://styleci.io/repos/33559148/shield?style=flat)](https://styleci.io/repos/33559148)
[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master)](http://ci.rebelinblue.com/build-status/view/3?branch=master)
[![Code Climate](https://codeclimate.com/github/REBELinBLUE/deployer/badges/gpa.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![Test Coverage](https://codeclimate.com/github/REBELinBLUE/deployer/badges/coverage.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/686dd98b-c0e5-465b-8f14-29b1cab47f3b.svg)](https://insight.sensiolabs.com/projects/686dd98b-c0e5-465b-8f14-29b1cab47f3b)

**Release Status**

[![PHP Dependency Status](https://www.versioneye.com/user/projects/5531329410e7141211000f29/badge.svg)](https://www.versioneye.com/user/projects/5531329410e7141211000f29)
[![Node Dependency Status](https://www.versioneye.com/user/projects/5531329610e714f9e500109c/badge.svg)](https://www.versioneye.com/user/projects/5531329610e714f9e500109c)
[![Latest Version](https://img.shields.io/github/release/REBELinBLUE/deployer.svg)](https://github.com/REBELinBLUE/deployer/releases)
[![License](https://img.shields.io/github/license/rebelinblue/deployer.svg)](https://github.com/REBELinBLUE/deployer/blob/master/LICENSE.md)

## What it does

* Deploys applications to multiple servers accessible via SSH
* Clones your projects git repository
* Installs composer dependencies
* Runs arbitrary commands
* Gracefully handles failure in any of these steps
* Keeps a number of previous deployments

## What it doesn't do

* Provision VMs
* Install system packages
* Configure the web server, database or other services

## Usage in production

The `master` branch of this repository is a development branch and **should not** be used in production. Instead, please check out the latest tag [release](https://github.com/REBELinBLUE/deployer/releases). For information on contributing see [contribution guidelines](CONTRIBUTING.md).

## Requirements

- PHP 5.5.9+ or newer
- [Composer](https://getcomposer.org)
- Beanstalkd
- Redis
- Node.js

## Installation

1. Clone the repository

```shell
$ git clone https://github.com/REBELinBLUE/deployer.git
```

2. Checkout the latest release

```shell
$ git checkout 0.0.17
```

3. Install dependences

```shell
$ composer install -o --no-dev
$ npm install --production
```

4. Run the installer and follow the instructions

```shell
$ php artisan app:install
```

## Updating

1. Get the latest colde

```shell
$ git fetch --all
$ git checkout 0.0.17
 ```

2. Update the dependencies

```shell
$ composer install -o --no-dev
```

3. Run the updater

```shell
$ php artisan app:update
```
