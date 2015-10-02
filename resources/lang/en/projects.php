<?php

return [

    'manage'             => 'Manage projects',
    'warning'            => 'The project could not be saved, please check the form below.',
    'none'               => 'There are currently no projects setup',
    'name'               => 'Name',
    'awesome'            => 'My awesome webapp',
    'group'              => 'Group',
    'repository'         => 'Repository',
    'builds'             => 'Builds to Keep',
    'branch'             => 'Default Branch',
    'image'              => 'Build Image',
    'ci_image'           => 'If you use a CI server which generates an image to indicate the build status ' .
                            'can put the URL here to have it show on the project page',
    'latest'             => 'Latest Deployment',
    'create'             => 'Add a new project',
    'edit'               => 'Edit a project',
    'url'                => 'URL',
    'details'            => 'Project details',
    'deployments'        => 'Deployments',
    'today'              => 'Today',
    'last_week'          => 'Last Week',
    'latest_duration'    => 'Latest Duration',
    'health'             => 'Health Check',
    'build_status'       => 'Build Status',
    'app_status'         => 'Application Status',
    'heartbeats_status'  => 'Heartbeat Status',
    'view_ssh_key'       => 'View the SSH Key',
    'public_ssh_key'     => 'Public SSH Key',
    'ssh_key'            => 'SSH key',
    'deploy_project'     => 'Deploy the project',
    'deploy'             => 'Deploy',
    'server_keys'        => 'This key must be added to the server\'s <strong>~/.ssh/authorized_keys</strong> ' .
                            'for each user you wish to run commands as.',
    'gitlab_keys'        => 'The key must also be added to the <strong>Deploy Keys</strong> ' .
                            'section for the project in Gitlab.',
    'finished'           => 'Finished',
    'pending'            => 'Pending',
    'deploying'          => 'Deploying',
    'failed'             => 'Failed',
    'not_deployed'       => 'Not Deployed',
    'clone_depth'        => 'Clone Depth',
    'full_clone'         => 'Full Clone',
    'shallow_clone'      => 'Shallow Clone',
    'full_clone_desc'    => 'Clones the entire history of the repository, this is useful on development systems '.
                            'where you might need access to historical data. The initial deploy will be '.
                            'slower than a shallow clone. If you do not have at least git 2.3 all deploys will '.
                            'be slow',
    'shallow_clone_desc' => 'Clones the repository with the history truncated to only the latest revision. This '.
                            'is useful for live environments where you will not need older code, build servers and '.
                            'systems where resources such as disk space are at a premium. Each deploy will be faster '.
                            'than using a full clone'

];
