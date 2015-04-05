<?php

use App\Project;
use App\Deployment;

function loading_value($value) 
{
    if ($value === Deployment::LOADING) {
        return Lang::get('deployments.loading');
    }

    return $value;
}

function project_css_status(Project $project)
{
    if ((int) $project->status === Project::FINISHED) {
        return 'success';
    } elseif ((int) $project->status === Project::DEPLOYING) {
        return 'warning';
    } elseif ((int) $project->status === Project::FAILED) {
        return 'danger';
    } elseif ((int) $project->status === Project::PENDING) {
        return 'info';
    }

    return 'primary';
}

function project_icon_status(Project $project, $rotate = true)
{
    if ((int) $project->status === Project::FINISHED) {
        return 'check';
    } elseif ((int) $project->status === Project::DEPLOYING) {
        if (!$rotate) {
            return 'spinner';
        }

        return 'spinner fa-spin';
    } elseif ((int) $project->status === Project::FAILED) {
        return 'warning';
    } elseif ((int) $project->status === Project::PENDING) {
        return 'clock-o';
    }

    return 'question-circle';
}

function project_status(Project $project)
{
    if ((int) $project->status === Project::FINISHED) {
        return Lang::get('projects.finished');
    } elseif ((int) $project->status === Project::DEPLOYING) {
        return Lang::get('projects.deploying');
    } elseif ((int) $project->status === Project::FAILED) {
        return Lang::get('projects.failed');
    } elseif ((int) $project->status === Project::PENDING) {
        return Lang::get('projects.pending');
    }

    return Lang::get('projects.not_deployed');
}

function timeline_css_status(Deployment $deployment)
{
    if ((int) $deployment->status === Deployment::COMPLETED) {
        return 'green';
    } elseif ((int) (int) $deployment->status === Deployment::FAILED) {
        return 'red';
    } elseif ((int) $deployment->status === Deployment::DEPLOYING) {
        return 'yellow';
    }

    return 'aqua';
}

function deployment_css_status(Deployment $deployment)
{
    if ((int) $deployment->status === Deployment::COMPLETED) {
        return 'success';
    } elseif ((int) $deployment->status === Deployment::FAILED) {
        return 'danger';
    } elseif ((int) $deployment->status === Deployment::DEPLOYING) {
        return 'warning';
    }

    return 'info';
}

function deployment_icon_status(Deployment $deployment)
{
    if ((int) $deployment->status === Deployment::COMPLETED) {
        return 'check';
    } elseif ((int) $deployment->status === Deployment::FAILED) {
        return 'warning';
    } elseif ((int) $deployment->status === Deployment::DEPLOYING) {
        return 'spinner fa-spin';
    }

    return 'clock-o';
}

function deployment_status(Deployment $deployment)
{
    if ((int) $deployment->status === Deployment::COMPLETED) {
        return Lang::get('deployments.completed');
    } elseif ((int) $deployment->status === Deployment::FAILED) {
        return Lang::get('deployments.failed');
    } elseif ((int) $deployment->status === Deployment::DEPLOYING) {
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
