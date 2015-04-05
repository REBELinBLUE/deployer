<?php

use App\Project;
use App\Deployment;

function project_css_status(Project $project)
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

function project_icon_status(Project $project, $rotate = true)
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

function deployment_css_status(Deployment $deployment)
{
    if ($deployment->status === Deployment::COMPLETED) {
        return 'success';
    } elseif ($deployment->status === Deployment::FAILED) {
        return 'danger';
    } elseif ($deployment->status === Deployment::DEPLOYING) {
        return 'warning';
    }

    return 'info';
}

function timeline_css_status(Deployment $deployment)
{
    if ($deployment->status === Deployment::COMPLETED) {
        return 'green';
    } elseif ($deployment->status === Deployment::FAILED) {
        return 'red';
    } elseif ($deployment->status === Deployment::DEPLOYING) {
        return 'yellow';
    }

    return 'aqua';
}

function deployment_icon_status(Deployment $deployment)
{
    if ($deployment->status === Deployment::COMPLETED) {
        return 'check';
    } elseif ($deployment->status === Deployment::FAILED) {
        return 'warning';
    } elseif ($deployment->status === Deployment::DEPLOYING) {
        return 'spinner fa-spin';
    }

    return 'clock-o';
}


function deployment_status(Deployment $deployment)
{
    if ($deployment->status === Deployment::COMPLETED) {
        return Lang::get('deployments.completed');
    } elseif ($deployment->status === Deployment::FAILED) {
        return Lang::get('deployments.failed');
    } elseif ($deployment->status === Deployment::DEPLOYING) {
        return Lang::get('deployments.deploying');
    }

    return Lang::get('deployments.pending');
}

function deploy_step_label($label)
{
    return Lang::get('commands.' . strtolower($label));
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
