<?php

if (!function_exists('webpack')) {
    /**
     * If running in the webpack environment prefixes all assets with the webpack server host
     * otherwise just returns the elixir path
     *
     * @param  string  $file
     * @param  string  $buildDirectory
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    function webpack($file, $buildDirectory = 'build')
    {
        $path = elixir($file, $buildDirectory);

        if (!env('WEBPACK_ENABLED', false)) {
            return $path;
        }

        return env('APP_URL') . ':8080' . $path;
    }
}
