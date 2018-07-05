<?php

return [

    'manage'            => 'Manage projects',
    'warning'           => 'The project could not be saved, please check the form below.',
    'none'              => 'There are currently no projects setup',
    'name'              => 'Name',
    'awesome'           => 'My awesome webapp',
    'group'             => 'Group',
    'repository'        => 'Repository',
    'repository_url'    => 'Repository URL',
    'builds'            => 'Builds to Keep',
    'build_options'     => 'Build options',
    'project_details'   => 'Project details',
    'branch'            => 'Default Branch',
    'image'             => 'Build Image',
    'ci_image'          => 'If you use a CI server which generates an image to indicate the build status ' .
                           'can put the URL here to have it show on the project page',
    'latest'            => 'Latest Deployment',
    'create'            => 'Add a new project',
    'edit'              => 'Edit a project',
    'url'               => 'URL',
    'details'           => 'Project Details',
    'deployments'       => 'Deployments',
    'today'             => 'Today',
    'last_week'         => 'Last Week',
    'latest_duration'   => 'Latest Duration',
    'health'            => 'Health Check',
    'build_status'      => 'Build Status',
    'app_status'        => 'Application Status',
    'heartbeats_status' => 'Heartbeat Status',
    'view_ssh_key'      => 'View the SSH Key',
    'private_ssh_key'   => 'Private SSH Key',
    'public_ssh_key'    => 'Public SSH Key',
    'ssh_key'           => 'SSH key',
    'ssh_key_info'      => 'If you have a specific private key you wish to use you can paste it here. The key must ' .
                           'not have a passphrase.',
    'ssh_key_example'   => 'An SSH key will be generated automatically if you do not enter one, this is recommended.',
    'deploy_project'    => 'Deploy the project',
    'deploy'            => 'Deploy',
    'redeploy'          => 'Redeploy',
    'server_keys'       => 'This key must be added to the server\'s <code>~/.ssh/authorized_keys</code> ' .
                           'for each user you wish to run commands as.',
    'git_keys'          => 'The key will also need to be added to the <strong>Deploy Keys</strong> ' .
                           'for you repository unless you are using a public/unauthenticated URL.',
    'finished'          => 'Finished',
    'pending'           => 'Pending',
    'deploying'         => 'Deploying',
    'failed'            => 'Failed',
    'not_deployed'      => 'Not Deployed',
    'options'           => 'Options',
    'change_branch'     => 'Allow other branches to be deployed?',
    'include_dev'       => 'Install composer development packages?',
    'insecure'          => 'Your Deployer installation is not running over a secure connection, it is recommended ' .
                           'that you let Deployer generate an SSH key rather than supply one yourself so that the ' .
                           'private key is not transmitted over an insecure connection.',
    'users'             => 'Users'

];
