<?php

namespace REBELinBLUE\Deployer\Services\Filesystem;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem as BaseFilesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * An extension to the laravel filesystem class to add the tempnam function.
 */
class Filesystem extends BaseFilesystem
{
    /**
     * Create a randomly generated unique file and return the path.
     *
     * @param string $path   The folder to use
     * @param string $prefix A prefix to apply to the filename
     *
     * @throws FileNotFoundException
     * @return string
     */
    public function tempnam($path, $prefix = '')
    {
        if ($this->isDirectory($path)) {
            $tmpFile = tempnam($path, $prefix);

            if ($tmpFile !== false) {
                return $tmpFile;
            }

            throw new IOException('A temporary file could not be created.'); // @codeCoverageIgnore
        }

        throw new FileNotFoundException("Directory does not exist at path {$path}");
    }
}
