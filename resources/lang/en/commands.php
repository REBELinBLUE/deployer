<?php

return [

    'label'                => 'Commands',
    'deploy_webhook'       => 'Deployments may be triggered by using the following webhook URL',
    'webhook_example'      => 'If the POST data contains the field \'reason\' it will set the deployment reason',
    'generate_webhook'     => 'Generate a new webhook URL (old URL will stop working)',
    'step'                 => 'Step',
    'before'               => 'Before',
    'name'                 => 'Name',
    'run_as'               => 'Run As',
    'migrations'           => 'Migrations',
    'bash'                 => 'Bash Script',
    'servers'              => 'Servers',
    'options'              => 'You can use the following tokens in your script',
    'release_id'           => 'The release ID',
    'release_path'         => 'The full release path',
    'project_path'         => 'The project path',
    'after'                => 'After',
    'configure'            => 'Configure',
    'clone'                => 'Clone New Release',
    'install'              => 'Install Composer Dependencies',
    'activate'             => 'Activate New Release',
    'purge'                => 'Purge Old Releases',
    'title'                => ':step Commands',
    'warning'              => 'The command could not be saved, please check the form below.',
    'create'               => 'Add a new command',
    'edit'                 => 'Edit a command',
    'sha'                  => 'The commit SHA hash',
    'short_sha'            => 'The short commit SHA hash',
    'none'                 => 'No commands have been configured',
    'optional'             => 'Optional',
    'example'              => 'e.g.',
    'optional_description' => 'Ask at deploy time whether or not the include this step'

];