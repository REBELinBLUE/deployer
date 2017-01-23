<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Contracts\RuntimeInterface;

/**
 * View presenter for calculating the runtime in a readable format.
 */
trait RuntimePresenter
{
    /**
     * Converts a number of seconds into a more human readable format.
     *
     * @return string
     */
    public function presentReadableRuntime()
    {
        if (!$this->getObject() instanceof RuntimeInterface) {
            throw new \RuntimeException('Model must implement RuntimeInterface');
        }

        $seconds = $this->getObject()->runtime();

        $units = [
            'week'   => 7 * 24 * 3600,
            'day'    => 24 * 3600,
            'hour'   => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        if ($seconds === 0) {
            return Lang::choice('deployments.second', 0, ['time' => 0]);
        }

        $readable = '';
        foreach ($units as $name => $divisor) {
            if ($quot = intval($seconds / $divisor)) {
                $readable .= Lang::choice('deployments.' . $name, $quot, ['time' => $quot]) . ', ';
                $seconds -= $quot * $divisor;
            }
        }

        return substr($readable, 0, -2);
    }
}
