<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

/**
 * Format the console log lines.
 */
class LogFormatter
{
    /**
     * Format an error message.
     *
     * @param string $message
     *
     * @return string
     */
    public function error($message)
    {
        return $this->format($message, 'error');
    }

    /**
     * Format an info message.
     *
     * @param string $message
     *
     * @return string
     */
    public function info($message)
    {
        return $this->format($message, 'info');
    }

    /**
     * Formats the string if it contains non-whitespace.
     *
     * @param string $message
     * @param string $style
     *
     * @return string
     */
    private function format($message, $style)
    {
        $cleaned = trim($message);
        if (!empty($cleaned)) {
            $trimmed   = rtrim($message);
            $formatted = '<' . $style . '>' . $trimmed . '</' . $style . '>';

            return str_replace($trimmed, $formatted, $message);
        }

        return $message;
    }
}
