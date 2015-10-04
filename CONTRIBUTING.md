# Contribution Guidelines

## Languages

When needing to add labels, placeholders or general text, you **must not** write directly into the source file, please make use of the `./resources/lang/` directory.

Always provide the English translation - making sure that the indentation and alignment of the arrays are updated.

## Coding Standards

The code is written to follow PSR-2 standards, this can be tested using PHP_CodeSniffer

    $ ./vendor/bin/phpcs --standard=phpcs.xml

Code mess can be checked with

    $ ./vendor/bin/phpmd app text phpmd.xml

PHP Docblocks can be checked with

    $ ./vendor/bin/phpdoccheck --directory=app

## .editorconfig

You should make use of the [.editorconfig](/.editorconfig) file found within the root of the repository. It'll make sure that your editor is setup with the same file settings.

## Requirements

Along with the standard requirements, development also requires the following

- Gulp
- Bower

## Development Environment 

The project includes a [Vagrantfile](/Vagrantfile) for running deployer, it uses [laravel/homestead](https://github.com/laravel/homestead). The VM uses the domain `deploy.app` and the IP address `192.168.10.10` so you will need to add them to your `/etc/hosts` file

Once you have started the VM you will need to run the following commands

    $ vagrant ssh
    $ cd /var/www/deployer
    $ composer install
    $ editor .env (change APP_ENV to local and APP_DEBUG to true)
    $ php artisan app:install
    $ npm install
    $ bower install
    $ gulp
    $ sudo service supervisor restart

You can reset your database running

    $ php artisan app:reset

The VM will set up the cronjob needed for heartbeats and it will setup supervisor to ensure the queue listener is always running