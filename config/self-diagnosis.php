<?php

use BeyondCode\SelfDiagnosis\Checks\AppKeyIsSet;
use BeyondCode\SelfDiagnosis\Checks\ComposerWithDevDependenciesIsUpToDate;
use BeyondCode\SelfDiagnosis\Checks\ComposerWithoutDevDependenciesIsUpToDate;
use BeyondCode\SelfDiagnosis\Checks\ConfigurationIsCached;
use BeyondCode\SelfDiagnosis\Checks\ConfigurationIsNotCached;
use BeyondCode\SelfDiagnosis\Checks\CorrectPhpVersionIsInstalled;
use BeyondCode\SelfDiagnosis\Checks\DatabaseCanBeAccessed;
use BeyondCode\SelfDiagnosis\Checks\DebugModeIsNotEnabled;
use BeyondCode\SelfDiagnosis\Checks\DirectoriesHaveCorrectPermissions;
use BeyondCode\SelfDiagnosis\Checks\EnvFileExists;
use BeyondCode\SelfDiagnosis\Checks\ExampleEnvironmentVariablesAreSet;
use BeyondCode\SelfDiagnosis\Checks\ExampleEnvironmentVariablesAreUpToDate;
use BeyondCode\SelfDiagnosis\Checks\MaintenanceModeNotEnabled;
use BeyondCode\SelfDiagnosis\Checks\MigrationsAreUpToDate;
use BeyondCode\SelfDiagnosis\Checks\PhpExtensionsAreDisabled;
use BeyondCode\SelfDiagnosis\Checks\PhpExtensionsAreInstalled;
use BeyondCode\SelfDiagnosis\Checks\RoutesAreCached;
use BeyondCode\SelfDiagnosis\Checks\RoutesAreNotCached;
use BeyondCode\SelfDiagnosis\Checks\StorageDirectoryIsLinked;

return [

    /*
     * A list of environment aliases mapped to the actual environment configuration.
     */
    'environment_aliases' => [
        'prod'  => 'production',
        'live'  => 'production',
        'local' => 'development',
    ],

    /*
     * Common checks that will be performed on all environments.
     */
    'checks' => [
        AppKeyIsSet::class,
        CorrectPhpVersionIsInstalled::class,
        DatabaseCanBeAccessed::class => [
            'default_connection' => true,
            'connections'        => [],
        ],
        DirectoriesHaveCorrectPermissions::class => [
            'directories' => [
                storage_path(),
                base_path('bootstrap/cache'),
            ],
        ],
        EnvFileExists::class,
        ExampleEnvironmentVariablesAreSet::class,
//        \BeyondCode\SelfDiagnosis\Checks\LocalesAreInstalled::class => [
//            'required_locales' => [
//                'en_US',
//                PHP_OS === 'Darwin' ? 'en_US.UTF-8' : 'en_US.utf8',
//            ],
//        ],
        MaintenanceModeNotEnabled::class,
        MigrationsAreUpToDate::class,
        PhpExtensionsAreInstalled::class => [
            'extensions' => [
                'openssl',
                'PDO',
                'mbstring',
                'tokenizer',
                'xml',
                'ctype',
                'json',
                'curl',
                'gd',
                'json',
            ],
            'include_composer_extensions' => true,
        ],
        //\BeyondCode\SelfDiagnosis\Checks\RedisCanBeAccessed::class => [
        //    'default_connection' => true,
        //    'connections' => [],
        //],
        StorageDirectoryIsLinked::class,
    ],

    /*
     * Environment specific checks that will only be performed for the corresponding environment.
     */
    'environment_checks' => [
        'development' => [
            ComposerWithDevDependenciesIsUpToDate::class,
            ConfigurationIsNotCached::class,
            RoutesAreNotCached::class,
            ExampleEnvironmentVariablesAreUpToDate::class,
        ],
        'production' => [
            ComposerWithoutDevDependenciesIsUpToDate::class,
            ConfigurationIsCached::class,
            DebugModeIsNotEnabled::class,
            PhpExtensionsAreDisabled::class => [
                'extensions' => [
                    'xdebug',
                    'pcov',
                ],
            ],
            RoutesAreCached::class,
            //\BeyondCode\SelfDiagnosis\Checks\ServersArePingable::class => [
            //    'servers' => [
            //        'www.google.com',
            //        ['host' => 'www.google.com', 'port' => 8080],
            //        '8.8.8.8',
            //        ['host' => '8.8.8.8', 'port' => 8080, 'timeout' => 5],
            //    ],
            //],
            //\BeyondCode\SelfDiagnosis\Checks\SupervisorProgramsAreRunning::class => [
            //    'programs' => [
            //        'horizon',
            //    ],
            //    'restarted_within' => 300,
            //],
            //\BeyondCode\SelfDiagnosis\Checks\HorizonIsRunning::class,
        ],
    ],

];
