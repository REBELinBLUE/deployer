# Introduction

Deployer is a application for managing the deployment of PHP applications

**Current Build Status**

[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master)](http://ci.rebelinblue.com/build-status/view/3?branch=master)

## What it does:
* Deploys applications to multiple servers accessible via SSH
* Clones your projects git repository
* Installs composer dependencies
* Runs arbitrary commands
* Gracefully handles failure in any of these steps
* Keeps a number of previous deployments

## What it doesn't do:
* Provision VMs
* Install system packages
* Configure the web server, database or other services

## Planned features:
* Rollback deploy
* Deployment templates
* Allow users to edit their own details from the user menu
* Checking application status is 200 on deploy

## Other changes:
* Massively refactor the JS, clean up the duplication and sort out the Backbone.js code to not modify the DOM directly

# Why?

Deployer was heavily inspired by [Envoyer.io](https://envoyer.io), if you want a reliable deployment system I suggest you give Envoyer a try.

Deployer came about as I needed a deployment system for deploying updates for a PHP application to a large number of clients. Although Envoyer was almost perfect it was missing 2 key things I needed

Firstly, I was unable to use a hosted service as it would not be able to connect to the servers I needed to deploy to without a lot of network configuration changes.

Secondly, the code for the application I was deploying was hosted on a Gitlab server, and Envoyer only supports Github & Bitbucket.

For these reasons I decided to make a clone of Envoyer which suited my needs, at the same time using it as an excuse to learn Laravel 5.

# Installation

#### Prerequisites 

* PHP >= 5.4
* PHP Mcrypt extension
* PHP SQLite extension
* PHP OpenSSL extension
* PHP Mbstring extension
* PHP Tokenizer extension
* PHP Json extension (normally built in)
* beanstalkd
* composer
* bower
* node
* gulp

**Clone the repository**

    $ git clone http://repository.url/repo.git

**Setup config**

    $ cp .env.example .env
    $ editor .env

An example of the config

    APP_ENV=production
    APP_URL=http://deploy.app
    APP_DEBUG=false
    APP_KEY=cJKwSTJFF75DK29ecw72ZRrkS6D0tqHy
    QUEUE_DRIVER=beanstalkd

**Install dependencies**

    $ composer install
    $ npm install
    $ bower install

**Setup the database**

    $ touch storage/database.sqlite
    $ php artisan migrate:install
    $ php artisan migrate

**Compile the assets**

    $ gulp --production
    $ gulp copy # This is needed to get the fonts needed by bootstrap in the correct place

**Start the queue listener**

    $ php artisan queue:listen --queue=default,deploy,connections,notify --timeout=0 --tries=1

Finally visit the site to login, the default login is `admin@example.com` with the password `password`


# Development

The code is written to follow PSR-2 standards, this can be tested using PHP_CodeSniffer

    $ phpcs --standard=PSR2 app

Code style can be checked with

    $ phpmd app text design,unusedcode,naming

PHP Docblocks can be checked with

    $ phpdoccheck --directory="app"
