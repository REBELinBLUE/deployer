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