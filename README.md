**Current Build Status**

[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master)](http://ci.rebelinblue.com/build-status/view/3?branch=master)
[![Code Climate](https://codeclimate.com/github/REBELinBLUE/deployer/badges/gpa.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![Test Coverage](https://codeclimate.com/github/REBELinBLUE/deployer/badges/coverage.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)

**Release Status**

[![PHP Dependency Status](https://www.versioneye.com/user/projects/5531329410e7141211000f29/badge.svg)](https://www.versioneye.com/user/projects/5531329410e7141211000f29)
[![Nodge Dependency Status](https://www.versioneye.com/user/projects/5531329610e714f9e500109c/badge.svg)](https://www.versioneye.com/user/projects/5531329610e714f9e500109c)
[![Latest Version](https://img.shields.io/github/release/REBELinBLUE/deployer.svg)](https://github.com/REBELinBLUE/deployer/releases)

**Development**

The code is written to follow PSR-2 standards, this can be tested using PHP_CodeSniffer

    $ phpcs --standard=PSR2 app

Code mess can be checked with

    $ phpmd app text design,unusedcode,naming

PHP Docblocks can be checked with

    $ phpdoccheck --directory="app"

The project includes a Vagrantfile for running deployer, it uses laravel/homestead. The VM uses the domain deploy.app and the IP address 192.168.10.10 so you will need to add them to your /etc/hosts file

Once you have started the VM you will need to run the following commands

    $ vagrant ssh
    $ cd /var/www/deployer
    $ composer install
    $ npm install
    $ bower install
    $ gulp && gulp copy
    $ php artisan key:generate
    $ php artisan migrate

The VM will set up the cronjob needed for heartbeats and it will setup supervisor to ensure the queue listener is always running