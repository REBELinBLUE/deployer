<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Illuminate\Support\Facades\Lang;
use RuntimeException;

/**
 * View presenter for calculating the runtime in a readable format.
 */
trait RuntimePresenter
{
    /**
     * Converts a number of seconds into a more human readable format.
     *
     * @throws RuntimeException
     * @return string
     */
    public function presentReadableRuntime()
    {
        if (!$this->getWrappedObject() instanceof RuntimeInterface) {
            throw new RuntimeException(
                'Model ' . get_class($this->getWrappedObject()) . ' must implement RuntimeInterface'
            );
        }

        $seconds = $this->getWrappedObject()->runtime();

        $units = [
            'hour'   => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        if ($seconds === 0) {
            return Lang::choice('deployments.second', 0, ['time' => 0]);
        }

        // If the runtime is more than 3 hours show a simple message
        if ($seconds >= $units['hour'] * 3) {
            return Lang::get('deployments.very_long_time');
        }

        $readable = '';
        foreach ($units as $name => $divisor) {
            if ($quot = (int) ($seconds / $divisor)) {
                $readable .= Lang::choice('deployments.' . $name, $quot, ['time' => $quot]) . ', ';
                $seconds -= $quot * $divisor;
            }
        }

        return substr($readable, 0, -2);
    }
}
