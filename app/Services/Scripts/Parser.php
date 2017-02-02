<?php

namespace REBELinBLUE\Deployer\Services\Scripts;

use Illuminate\Filesystem\Filesystem;

/**
 * Class which loads a shell script template and parses any variables.
 */
class Parser
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Parser constructor.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * Parse a string to replace the tokens.
     *
     * @param string $script
     * @param array  $tokens
     *
     * @return string
     */
    public function parseString($script, array $tokens = [])
    {
        $values = array_values($tokens);

        $tokens = array_map(function ($token) {
            return '{{ ' . strtolower($token) . ' }}';
        }, array_keys($tokens));

        return str_replace($tokens, $values, $script);
    }

    /**
     * Load a file and parse the the content.
     *
     * @param string $file
     * @param array  $tokens
     *
     * @return string
     */
    public function parseFile($file, array $tokens = [])
    {
        $template = resource_path('scripts/' . str_replace('.', '/', $file) . '.sh');

        if ($this->fs->exists($template)) {
            return $this->parseString($this->fs->get($template), $tokens);
        }

        throw new \RuntimeException('Template ' . $template . ' does not exist');
    }
}
