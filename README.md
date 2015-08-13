**Current Build Status**

[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master)](http://ci.rebelinblue.com/build-status/view/3?branch=master)
[![Code Climate](https://codeclimate.com/github/REBELinBLUE/deployer/badges/gpa.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![Test Coverage](https://codeclimate.com/github/REBELinBLUE/deployer/badges/coverage.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/686dd98b-c0e5-465b-8f14-29b1cab47f3b.svg)](https://insight.sensiolabs.com/projects/686dd98b-c0e5-465b-8f14-29b1cab47f3b)

**Release Status**

[![PHP Dependency Status](https://www.versioneye.com/user/projects/5531329410e7141211000f29/badge.svg)](https://www.versioneye.com/user/projects/5531329410e7141211000f29)
[![Nodge Dependency Status](https://www.versioneye.com/user/projects/5531329610e714f9e500109c/badge.svg)](https://www.versioneye.com/user/projects/5531329610e714f9e500109c)
[![Latest Version](https://img.shields.io/github/release/REBELinBLUE/deployer.svg)](https://github.com/REBELinBLUE/deployer/releases)
[![License](https://img.shields.io/github/license/rebelinblue/deployer.svg)](https://github.com/REBELinBLUE/deployer/blob/master/LICENSE.md)

**Development**

The code is written to follow PSR-2 standards, this can be tested using PHP_CodeSniffer

    $ ./vendor/bin/phpcs --standard=phpcs.xml

Code mess can be checked with

    $ ./vendor/bin/phpmd app text phpmd.xml

PHP Docblocks can be checked with

    $ ./vendor/bin/phpdoccheck --directory=app

The project includes a Vagrantfile for running deployer, it uses laravel/homestead. The VM uses the domain deploy.app and the IP address 192.168.10.10 so you will need to add them to your /etc/hosts file

Once you have started the VM you will need to run the following commands

    $ vagrant ssh
    $ cd /var/www/deployer
    $ composer install
    $ npm install
    $ bower install
    $ gulp
    $ php artisan key:generate
    $ php artisan migrate
    $ sudo service supervisor restart

The VM will set up the cronjob needed for heartbeats and it will setup supervisor to ensure the queue listener is always running
