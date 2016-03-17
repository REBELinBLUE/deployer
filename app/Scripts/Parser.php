<?php

namespace REBELinBLUE\Deployer\Scripts;


use REBELinBLUE\Deployer\Command as Stage;

/**
 * Class which loads a shell script template and parses any variables.
**/
class Parser
{
    public function parseString($script, array $tokens = [])
    {
        $values = array_values($tokens);

        $tokens = array_map(function ($token) {
            return '{{ ' . strtolower($token) . ' }}';
        }, array_keys($tokens));


        return str_replace($tokens, $values, $script);
    }

    public function parseFile($file, array $tokens = [])
    {
        $template = resource_path('scripts/' . $file . '.sh');

        if (file_exists($template)) {
            return $this->parseString(file_get_contents($template), $tokens);
        }

        throw new \RuntimeException('Template ' . $template . ' does not exist');
    }
}
