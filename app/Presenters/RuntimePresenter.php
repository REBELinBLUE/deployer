<?php

namespace App\Presenters;

use App\Contracts\RuntimeInterface;

/**
 * View presenter for calculating the runtime in a readable format.
 */
trait RuntimePresenter
{
    /**
     * Converts a number of seconds into a more human readable format.
     *
     * @param int $seconds The number of seconds
     * @return string
     * TODO: Translate
     */
    public function presentReadableRuntime()
    {
        if (!$this->object instanceof RuntimeInterface) {
            throw new \RuntimeException('Model must implement  RuntimeInterface');
        }

        $seconds = $this->object->runtime();

        $units = [
            'week'   => 7 * 24 * 3600,
            'day'    => 24 * 3600,
            'hour'   => 3600,
            'minute' => 60,
            'second' => 1
        ];

        if ($seconds === 0) {
            return '0 seconds';
        }

        $readable = '';
        foreach ($units as $name => $divisor) {
            if ($quot = intval($seconds / $divisor)) {
                $readable .= $quot . ' ' . $name;
                $readable .= (abs($quot) > 1 ? 's' : '') . ', ';
                $seconds -= $quot * $divisor;
            }
        }

        return substr($readable, 0, -2);
    }
}
