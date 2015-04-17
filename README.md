**Current Build Status**

[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master)](http://ci.rebelinblue.com/build-status/view/3?branch=master)
[![Code Climate](https://codeclimate.com/github/REBELinBLUE/deployer/badges/gpa.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)
[![Test Coverage](https://codeclimate.com/github/REBELinBLUE/deployer/badges/coverage.svg)](https://codeclimate.com/github/REBELinBLUE/deployer)

**Development**

The code is written to follow PSR-2 standards, this can be tested using PHP_CodeSniffer

    $ phpcs --standard=PSR2 app

Code style can be checked with

    $ phpmd app text design,unusedcode,naming

PHP Docblocks can be checked with

    $ phpdoccheck --directory="app"
