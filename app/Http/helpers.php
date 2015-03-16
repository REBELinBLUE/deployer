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