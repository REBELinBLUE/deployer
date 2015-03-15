**Current Build Status**

[![Build Status](http://ci.rebelinblue.com/build-status/image/3?branch=master)](http://ci.rebelinblue.com/build-status/view/3?branch=master)

**Tests**

The code is written to follow PSR-2 standards, this can be tested using PHP_CodeSniffer

    $ phpcs --standard=PSR2 --ignore=blade app/*

Code system can be checked with

    $ phpmd app/ text design,unusedcode,naming
