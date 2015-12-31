<?php

return [

    'label'             => 'Environment Variables',
    'create'            => 'Add a new variable',
    'edit'              => 'Edit the variable',
    'name'              => 'Variable',
    'value'             => 'Value',
    'warning'           => 'The variable could not be saved, please check the form below.',
    'description'       => 'Sometimes you need may need certain environmental variables defined during a deployment ' .
                           'but you do not want to set them in the <code>~/.bashrc</code> file on the server.',
    'example'           => 'For example, you may want to set <code>COMPOSER_PROCESS_TIMEOUT</code> to allow composer ' .
                           'to run for longer, or <code>SYMFONY_ENV</code> if you are deploying a symfony project.',

];
