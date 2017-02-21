# Contribution Guidelines

## Languages

When needing to add labels, placeholders or general text, you **must not** write directly into the source file, please make use of the `./resources/lang/` directory.

Always provide the English translation - making sure that the indentation and alignment of the arrays are updated.

## Coding Standards

The code is written to follow [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) standards, this can be tested using PHP_CodeSniffer

    $ make phpcs

Linting can be checked with

    $ make lint

Mess can be checked with

    $ make phpmd

PHPDoc blocks can be checked with

    $ make phpdoccheck

Unit tests can be run with

    $ make phpunit
    
You can also run all tests at once by running

    $ make test
    
Style problems can automatically be fixed with

    $ make fix


### Automated testing

Coding standards are checked using [StyleCI](https://styleci.io/repos/33559148); mess, duplication, PHPDoc blocks and PHP syntax are checked using [TravisCI](https://travis-ci.org/REBELinBLUE/deployer)

## .editorconfig

You should make use of the [.editorconfig](/.editorconfig) file found within the root of the repository. It'll make sure that your editor is setup with the same file settings. See [http://editorconfig.org](http://editorconfig.org) for more details.

## Requirements

Along with the standard requirements, development also requires the following

- [Gulp](http://gulpjs.com)
- [Bower](http://bower.io)
- [Vagrant](https://www.vagrantup.com/), optional but it makes development easier

## Development environment

There is a [Vagrant VM](https://github.com/REBELinBLUE/deployer-vm) for running Deployer, it uses the `ubuntu/trusty64` box. The VM uses the domain `deployer.app` and the IP address `192.168.10.10` so you will need to add the following line your `/etc/hosts` file

    192.168.10.10 deployer.app

You will need to install the required box and start vagrant

    $ vagrant box add ubuntu/trusty64
    $ vagrant up

You may copy the `config.default.json` file to `config.json` and make any desired changes before starting the VM.

Once you have started the VM you will need to run the following commands

    $ vagrant ssh
    $ cd /var/www/deployer
    $ composer install
    $ editor .env                       # Change APP_ENV to local and APP_DEBUG to true
    $ php artisan app:install           # Use the values from .env if you want to use MySQL
    $ npm install
    $ bower install
    $ gulp
    $ sudo service supervisor restart

You can reset your database by running

    $ php artisan app:reset

The VM will set up the cronjob needed for heartbeats and it will setup supervisor to ensure the queue listener is always running

Please note, this VM will copy `~/.ssh/id_rsa` and `~/.gitconfig` from your host on first boot.
