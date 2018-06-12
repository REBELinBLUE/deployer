# Deployer

[![StyleCI](https://styleci.io/repos/33559148/shield?style=flat-square&branch=master)](https://styleci.io/repos/33559148)
[![Build Status](https://img.shields.io/travis/REBELinBLUE/deployer/master.svg?style=flat-square&label=Travis+CI)](https://travis-ci.org/REBELinBLUE/deployer)
[![Code Coverage](https://img.shields.io/codecov/c/github/REBELinBLUE/deployer/master.svg?style=flat-square&label=Coverage)](https://codecov.io/gh/REBELinBLUE/deployer)


[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square&label=License)](/LICENSE.md)
[![Laravel Version](https://shield.with.social/cc/github/REBELinBLUE/deployer/master.svg?style=flat-square)](https://packagist.org/packages/laravel/framework)
[![Latest Version](https://img.shields.io/github/release/REBELinBLUE/deployer.svg?style=flat-square&label=Release)](https://github.com/REBELinBLUE/deployer/releases)
[![StackShare](https://img.shields.io/badge/tech-stack-0690fa.svg?style=flat-square&label=Tech)](https://stackshare.io/REBELinBLUE/deployer)
[![Gitter](https://img.shields.io/badge/chat-on%20gitter-brightgreen.svg?style=flat-square&label=Chat)](https://gitter.im/REBELinBLUE/deployer)

Deployer is a PHP Application deployment system powered by [Laravel 5.5](http://laravel.com), written & maintained by [Stephen Ball](https://github.com/REBELinBLUE).

Check out the [releases](https://github.com/REBELinBLUE/deployer/releases), [license](/LICENSE.md), [screenshots](https://github.com/REBELinBLUE/deployer/wiki/Screenshots) and [contribution guidelines](/.github/CONTRIBUTING.md).

See the [wiki](https://github.com/REBELinBLUE/deployer/wiki) for information on [system requirements](https://github.com/REBELinBLUE/deployer/wiki/system-requirements), [installation](https://github.com/REBELinBLUE/deployer/wiki/installation) & [upgrade](https://github.com/REBELinBLUE/deployer/wiki/upgrading) instructions and answers to [common questions](https://github.com/REBELinBLUE/deployer/wiki/common-issues).

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

## License

Deployer is licensed under [The MIT License (MIT)](/LICENSE.md).

