<?php

namespace REBELinBLUE\Deployer\Scripts;

/**
 * Class which loads a shell script template and parses any variables.
 */
class Parser
{
    /**
     * Parse a string to replace the tokens.
     *
     * @param  string $script
     * @param  array  $tokens
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
     * @param  string $file
     * @param  array  $tokens
     * @return string
     */
    public function parseFile($file, array $tokens = [])
    {
        $template = resource_path('scripts/' . str_replace('.', '/', $file) . '.sh');

        if (file_exists($template)) {
            return $this->parseString(file_get_contents($template), $tokens);
        }

        throw new \RuntimeException('Template ' . $template . ' does not exist');
    }
}
