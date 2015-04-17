<?php

use App\Command;

/**
 * Converts a number of seconds into a more human readable format
 *
 * @param int $seconds The number of seconds
 * @return string
 */
function human_readable_duration($seconds)
{
    $units = [
        'week'   => 7 * 24 * 3600,
        'day'    => 24 * 3600,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1
    ];

    if ($seconds == 0) {
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

/**
 * Converts a numeric command number to the string representation
 *
 * @param int $command
 * @return string
 */
function command_name($command)
{
    if ($command === Command::DO_CLONE) {
        return 'clone';
    } elseif ($command === Command::DO_INSTALL) {
        return 'install';
    } elseif ($command === Command::DO_ACTIVATE) {
        return 'activate';
    }

    return 'purge';
}

/**
 * Gets the translated command label
 *
 * @param string $command
 * @return string
 */
function command_label($command)
{
    return Lang::get('commands.' . command_name($command));
}

/**
 * Gets a readable list of commands for a stage
 *
 * @todo Document this better
 */
function command_list_readable($commands, $step, $action)
{
    if (isset($commands[$step][$action])) {
        return implode(', ', $commands[$step][$action]);
    }

    return Lang::get('app.none');
}
