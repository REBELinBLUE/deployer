<?php

function project_css_status(\App\Project $project)
{
    if ($project->status == 'Finished') {
        return 'success';
    } elseif ($project->status == 'Deploying') {
        return 'warning';
    } elseif ($project->status == 'Failed') {
        return 'danger';
    } elseif ($project->status == 'Pending') {
        return 'info';
    }

    return 'primary';
}

function project_icon_status(\App\Project $project, $rotate = true)
{
    if ($project->status == 'Finished') {
        return 'check';
    } elseif ($project->status == 'Deploying') {
        if (!$rotate) {
            return 'spinner';
        }
        return 'spinner fa-spin';
    } elseif ($project->status == 'Failed') {
        return 'warning';
    } elseif ($project->status == 'Pending') {
        return 'clock-o';
    }

    return 'question-circle';
}

function deployment_css_status(\App\Deployment $deployment)
{
    if ($deployment->status == 'Completed') {
        return 'success';
    } elseif ($deployment->status == 'Failed') {
        return 'danger';
    } elseif ($deployment->status == 'Deploying') {
        return 'warning';
    }

    return 'info';
}

function timeline_css_status(\App\Deployment $deployment)
{
    if ($deployment->status == 'Completed') {
        return 'green';
    } elseif ($deployment->status == 'Failed') {
        return 'red';
    } elseif ($deployment->status == 'Deploying') {
        return 'yellow';
    }

    return 'aqua';
}

function deployment_icon_status(\App\Deployment $deployment)
{
    if ($deployment->status == 'Completed') {
        return 'check';
    } elseif ($deployment->status == 'Failed') {
        return 'warning';
    } elseif ($deployment->status == 'Deploying') {
        return 'spinner fa-spin';
    }

    return 'clock-o';
}

function deploy_step_label($label)
{
    if ($label == 'Clone') {
        return 'Clone New Release';
    } elseif ($label == 'Install') {
        return 'Install Composer Dependencies';
    } elseif ($label == 'Activate') {
        return 'Activate New Release';
    } elseif ($label == 'Purge') {
        return 'Purge Old Releases';
    }

    return $label;
}

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

function command_list_readable($commands, $step, $action)
{
    if (isset($commands[$step][$action])) {
        return implode(', ', $commands[$step][$action]);
    }

    return 'None';
}
