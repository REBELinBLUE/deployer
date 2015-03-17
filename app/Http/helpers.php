<?php

function project_css_status(\App\Project $project) {
    if ($project->status == 'Finished') {
        return 'success';
    }

    if ($project->status == 'Running') {
        return 'warning';
    }

    if ($project->status == 'Failed') {
        return 'danger';
    }

    return 'primary';
}

function project_icon_status(\App\Project $project) {
    if ($project->status == 'Finished') {
        return 'check';
    }

    if ($project->status == 'Running') {
        return 'spinner';
    }

    if ($project->status == 'Failed') {
        return 'warning';
    }

    return 'question';
}

function server_css_status(\App\Server $server) {
    if ($server->status == 'Successful') {
        return 'success';
    }

    if ($server->status == 'Testing') {
        return 'warning';
    }

    if ($server->status == 'Failed') {
        return 'danger';
    }

    return 'primary';
}

function server_icon_status(\App\Server $server) {
    if ($server->status == 'Successful') {
        return 'check';
    }

    if ($server->status == 'Testing') {
        return 'spinner';
    }

    if ($server->status == 'Failed') {
        return 'warning';
    }

    return 'question';
}

function deployment_css_status(\App\Deployment $deployment) {
    if ($deployment->status == 'Completed') {
        return 'success';
    }

    if ($deployment->status == 'Failed') {
        return 'danger';
    }

    return 'warning';
}

function deployment_icon_status(\App\Deployment $deployment) {
    if ($deployment->status == 'Completed') {
        return 'check';
    }

    if ($deployment->status == 'Failed') {
        return 'warning';
    }

    return 'spinner';
}