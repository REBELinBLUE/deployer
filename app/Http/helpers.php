<?php

use App\Command;
use App\Project;
use App\Deployment;

/**
 * Checks if the deployment commit info is currently loading
 *
 * @param  string $value The commit info to check
 * @return string Either the commit info, or the Loading string from the language
 */
function loading_value($value)
{
    if ($value === Deployment::LOADING) {
        return Lang::get('deployments.loading');
    }

    return $value;
}

/**
 * Gets the CSS class for the project status
 *
 * @param Project $project
 * @return string
 */
function project_css_status(Project $project)
{
    if ($project->status === Project::FINISHED) {
        return 'success';
    } elseif ($project->status === Project::DEPLOYING) {
        return 'warning';
    } elseif ($project->status === Project::FAILED) {
        return 'danger';
    } elseif ($project->status === Project::PENDING) {
        return 'info';
    }

    return 'primary';
}

/**
 * Gets the CSS icon class for the project status
 *
 * @param Project $project
 * @param boolean $rotate Whether or not the icon should rotate
 * @return string
 */
function project_icon_status(Project $project, $rotate = true)
{
    if ($project->status === Project::FINISHED) {
        return 'check';
    } elseif ($project->status === Project::DEPLOYING) {
        if (!$rotate) {
            return 'spinner';
        }

        return 'spinner fa-spin';
    } elseif ($project->status === Project::FAILED) {
        return 'warning';
    } elseif ($project->status === Project::PENDING) {
        return 'clock-o';
    }

    return 'question-circle';
}

/**
 * Gets the translated project status string
 *
 * @param Project $project
 * @return string
 */
function project_status(Project $project)
{
    if ($project->status === Project::FINISHED) {
        return Lang::get('projects.finished');
    } elseif ($project->status === Project::DEPLOYING) {
        return Lang::get('projects.deploying');
    } elseif ($project->status === Project::FAILED) {
        return Lang::get('projects.failed');
    } elseif ($project->status === Project::PENDING) {
        return Lang::get('projects.pending');
    }

    return Lang::get('projects.not_deployed');
}

/**
 * Gets the CSS class for the deployment status for the timeline
 *
 * @param Deployment $deployment
 * @return string
 */
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

/**
 * Gets the CSS class for the deployment status
 *
 * @param Deployment $deployment
 * @return string
 */
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

/**
 * Gets the CSS icon class for the deployment status
 *
 * @param Deployment $deployment
 * @return string
 */
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

/**
 * Gets the translated deployment status string
 *
 * @param Deployment $deployment
 * @return string
 */
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

/**
 * Gets the deployment stage label from the numeric representation
 *
 * @param int $label
 * @return string
 * @see deploy_step_label()
 */
function deploy_stage_label($label)
{
    $step = 'clone';
    if ($label === Command::DO_INSTALL) {
        $step = 'install';
    } elseif ($label === Command::DO_ACTIVATE) {
        $step = 'activate';
    } elseif ($label === Command::DO_PURGE) {
        $step = 'purge';
    }

    return deploy_step_label($step);
}

/**
 * Gets the translated deployment stage label
 *
 * @param string $label
 * @return string
 */
function deploy_step_label($label)
{
    return Lang::get('commands.' . strtolower($label));
}

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
