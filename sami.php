<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;
use Sami\Parser\Filter\TrueFilter;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/app')
;

$sami = new Sami($iterator, [
    'title'                => 'Deployer API',
    'build_dir'            => __DIR__ . '/storage/docs',
    'default_opened_level' => 1
]);

$sami['filter'] = function () {
    return new TrueFilter();
};

return $sami;
