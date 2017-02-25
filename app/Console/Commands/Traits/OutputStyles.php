<?php

namespace REBELinBLUE\Deployer\Console\Commands\Traits;

use Symfony\Component\Console\Helper\FormatterHelper;

trait OutputStyles
{
    /**
     * A wrapper around symfony's formatter helper to output a block.
     *
     * @param string|array $messages Messages to output
     * @param string       $type     The type of message to output
     */
    public function block($messages, $type = 'error')
    {
        $output = [];

        if (!is_array($messages)) {
            $messages = (array) $messages;
        }

        foreach ($messages as $message) {
            $output[] = trim($message);
        }

        $formatter = new FormatterHelper();
        $this->line($formatter->formatBlock($output, $type, true));
    }

    /**
     * Outputs a header block.
     *
     * @param string $header The text to output
     */
    protected function header($header)
    {
        $this->block($header, 'question');
    }

    /**
     * @param string $line1
     * @param string $line2
     */
    protected function failure($line1, $line2)
    {
        $this->block([$line1, PHP_EOL, $line2]);
    }
}
